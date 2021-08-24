<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $fromDate = Carbon::create($request->year, $request->month);
        $toDate = Carbon::create($request->year, $request->month)->endOfMonth();
        $bankAccountId = $request->bankAccount ?? BankAccount::where('active', 1)->first()->id;

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

        $transactions = Transaction::join('categories AS c', 'c.id', '=', 'category_id')
            ->join('category_types AS ct', 'c.category_type_id', '=', 'ct.id')
            ->where('bank_account_id', $bankAccountId)
            ->whereBetween('date', [$fromDate, $toDate])
            ->select('transactions.id', 'ct.color', 'date', 'amount', 'status', 'description')
            ->get();

        $days = [];
        for ($day = 1; $day <= $toDate->day; $day++) {
            $days[$request->month][$day] = [
                'balance' => number_format(round(22222, 2), 2),
                'available' => number_format(round(22222, 2), 2),
            ];
        }

        // ddd($events);

        // this.events = [
        //     {
        //         name: "test",
        //         start: new Date(),
        //         timed: false,
        //         color: "red"
        //     }
        // ]

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
