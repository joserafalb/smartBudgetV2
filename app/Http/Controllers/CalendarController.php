<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Category;
use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Classes\BalanceHelper;
use Illuminate\Support\Facades\DB;
use App\Models\RecurringTransactions;
use App\Http\Controllers\RecurringTransactionsController;
use App\Models\RecurringTransactionLog;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $fromDate = Carbon::create($request->year, $request->month);
        $toDate = Carbon::create($request->year, $request->month)->endOfMonth();
        $bankAccountId = $request->account ?? BankAccount::where('active', 1)->first()->id;

        // Create recurring transactions
        $this->createRecurringTransactions($fromDate, $toDate, $bankAccountId);

        // Get the balance on the first day of the month
        $balance = BalanceHelper::getBalanceAtDate($fromDate, $bankAccountId);
        $available = BalanceHelper::getBalanceAtDate($fromDate, $bankAccountId, true);

        // Get the totals to display in the calendar
        $events = Transaction::join('categories AS c', 'c.id', '=', 'category_id')
            ->join('category_types AS ct', 'c.category_type_id', '=', 'ct.id')
            ->where('bank_account_id', $bankAccountId)
            ->whereBetween('date', [$fromDate, $toDate])
            ->groupBy('ct.color', 'date')
            ->select('ct.color', 'date', DB::raw('sum(amount) AS total'), DB::raw('MIN(transactions.status) AS status'))
            ->get()
            ->transform(
                function ($item) {
                    return [
                        'name' => number_format(round($item->total, 2), 2),
                        'start' => $item->date,
                        'timed' => false,
                        'color' => $item->color,
                        'isPending' => $item->status !== 1,
                    ];
                }
            );

        // Get the trasnactions detail
        $transactions = Transaction::join('categories AS c', 'c.id', '=', 'category_id')
            ->join('category_types AS ct', 'c.category_type_id', '=', 'ct.id')
            ->where('bank_account_id', $bankAccountId)
            ->whereBetween('date', [$fromDate, $toDate])
            ->select(
                'transactions.id',
                'ct.name AS type',
                'ct.color',
                'date',
                'amount',
                'status',
                'description',
                'category_id'
            )
            ->get();

        // Build array of balance per each day in the month
        $days = [];
        for ($day = 1; $day <= $toDate->day; $day++) {
            // Search for transactions on each day
            $filteredTransactions = $transactions->filter(
                function ($item) use ($toDate, $day) {
                    return $item->date == Carbon::create($toDate->year, $toDate->month, $day)->format('Y-m-d');
                }
            );

            // If there are transactions update balance and available
            foreach ($filteredTransactions as $transaction) {
                $amount = $transaction->amount;
                if ($transaction->type !== 'Income') {
                    $amount = $amount * -1;
                }

                if ($transaction->status === 1) {
                    $balance += $amount;
                    $available += $amount;
                } else {
                    $available += $amount;
                }
            }

            // Add information to array
            $days[intval($request->month)][$day] = [
                'balance' => round($balance, 2),
                'available' => round($available, 2),
            ];
        }

        return Inertia::render(
            'Calendar/Index',
            [
                'date' => $fromDate->format('Y-m-d'),
                'events' => $events,
                'days' => $days,
                'transactions' => $transactions,
                'categories' => Category::orderBy('name')->select('id', 'name')->get(),
                'bankAccountId' => $bankAccountId,
            ]
        );
    }

    private function createRecurringTransactions($fromDate, $toDate, $bankAccountId): void
    {
        // Pull all the recurring transactions that already started
        $recurringTrasnactions = RecurringTransactions::where('start_date', '<=', $toDate)
            ->where(
                function ($query) use ($fromDate) {
                    $query->where('end_date', '>=', $fromDate)
                        ->orWhere('limit_type', 'None');
                }
            )
            ->get();

        foreach ($recurringTrasnactions as $recurringTrasnaction) {
            // Get the days we need to add this recurring transaction
            $dates = RecurringTransactionsController::getDaysToAdd($recurringTrasnaction, $fromDate, $toDate);

            // Loop all dates to add the transactions
            foreach ($dates as $date) {
                // Check if we need to add this transaction
                $RecurringTransactionLog = RecurringTransactionLog
                    ::where('recurring_transactions_id', $recurringTrasnaction->id)
                    ->where('creation_date', $date)
                    ->first();

                if ($RecurringTransactionLog === null) {
                    RecurringTransactionLog::create(
                        [
                            'recurring_transactions_id' => $recurringTrasnaction->id,
                            'creation_date' => $date,
                        ]
                    );

                    Transaction::create(
                        [
                            'description' => $recurringTrasnaction->description,
                            'date' => $date,
                            'category_id' => $recurringTrasnaction->category_id,
                            'amount' => $recurringTrasnaction->amount,
                            'status' => TransactionController::STATUS_PENDING,
                            'bank_account_id' => $recurringTrasnaction->bank_account_id,
                        ]
                    );
                }
            }
        }
    }
}
