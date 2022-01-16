<?php

namespace App\Classes;

use App\Http\Controllers\TransactionController;
use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class BalanceHelper
{
    public static function getBalanceAtDate($date, $bankAccountId, $pending = false)
    {
        $balance = BankAccount::findOrFail($bankAccountId)->only('initial_balance')['initial_balance'];

        $transactionStatus = [TransactionController::STATUS_PROCESSED];
        if ($pending) {
            $transactionStatus[] = TransactionController::STATUS_PENDING;
        }

        $transactions = Transaction::join('categories AS c', 'c.id', '=', 'category_id')
            ->join('category_types AS ct', 'c.category_type_id', '=', 'ct.id')
            ->where('bank_account_id', $bankAccountId)
            ->where('date', '<', $date)
            ->whereIn('transactions.status', $transactionStatus)
            ->groupBy('ct.name')
            ->select('ct.name', DB::raw('SUM(amount) AS Total'))
            ->get();

        foreach ($transactions as $transaction) {
            switch ($transaction->name) {
                case 'Income':
                    $balance += $transaction->Total;
                    break;
                default:
                    $balance -= $transaction->Total;
                    break;
            };
        };

        return $balance;
    }
}
