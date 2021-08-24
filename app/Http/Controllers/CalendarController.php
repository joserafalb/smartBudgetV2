<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Classes\BalanceHelper;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $fromDate = Carbon::create($request->year, $request->month);
        $toDate = Carbon::create($request->year, $request->month)->endOfMonth();
        $bankAccountId = $request->account ?? BankAccount::where('active', 1)->first()->id;

        // Get the balance on the first day of the month
        $balance = BalanceHelper::getBalanceAtDate($fromDate, $bankAccountId);
        $available = BalanceHelper::getBalanceAtDate($fromDate, $bankAccountId, true);

        // Get the totals to display in the calendar
        $events = Transaction::join('categories AS c', 'c.id', '=', 'category_id')
            ->join('category_types AS ct', 'c.category_type_id', '=', 'ct.id')
            ->where('bank_account_id', $bankAccountId)
            ->whereBetween('date', [$fromDate, $toDate])
            ->groupBy('ct.color', 'date')
            ->select('ct.color', 'date', DB::raw('sum(amount) AS total'))
            ->get()
            ->transform(
                function ($item) {
                    return [
                        'name' => number_format(round($item->total, 2), 2),
                        'start' => $item->date,
                        'timed' => false,
                        'color' => $item->color
                    ];
                }
            );

        // Get the trasnactions detail
        $transactions = Transaction::join('categories AS c', 'c.id', '=', 'category_id')
            ->join('category_types AS ct', 'c.category_type_id', '=', 'ct.id')
            ->where('bank_account_id', $bankAccountId)
            ->whereBetween('date', [$fromDate, $toDate])
            ->select('transactions.id', 'ct.name AS type', 'ct.color', 'date', 'amount', 'status', 'description')
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
            $days[$request->month][$day] = [
                'balance' => number_format(round($balance, 2), 2),
                'available' => number_format(round($available, 2), 2),
            ];
        }

        return Inertia::render(
            'Calendar/Index',
            [
                'date' => $fromDate->format('Y-m-d'),
                'events' => $events,
                'days' => $days,
                'transactions' => $transactions,
            ]
        );
    }
}
