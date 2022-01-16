<?php

namespace App\Console\Commands;

use App\Classes\EmailSender;
use App\Http\Controllers\TransactionController;
use App\Models\Transaction;
use Illuminate\Console\Command;

class PaymentReminderNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'paymentReminderNotification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        // Get all transactions that are still pending and are due in the next 2 days
        $transactions = Transaction::where('status', TransactionController::STATUS_PENDING)
            ->where('date', '<=', now()->addDays(4)->setTime(0, 0, 0)->toDateString())
            ->where('notification_count', '<', 3)
            ->get();

        // Loop all transactions
        foreach ($transactions as $transaction) {
            // For each trasnaction, send an email
            EmailSender::sendTransactionalEmail(
                'joserafalb@gmail.com',
                'Jose Lopez',
                2,
                [
                    'DESCRIPTION' => $transaction->description,
                    'DATE' => $transaction->date,
                    'AMOUNT' => $transaction->amount,
                ]
            );
        }
    }
}
