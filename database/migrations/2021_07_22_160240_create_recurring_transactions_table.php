<?php

use App\Models\BankAccount;
use App\Models\Category;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecurringTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recurring_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('quantity')->nullable();
            $table->tinyInteger('schedule_type')->comment('1=every x days 2=day of the month 3=day of the week');
            $table->string('schedule_parameter');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->foreignIdFor(Category::class)->constrained();
            $table->decimal('amount', 10, 2, true);
            $table->string('description');
            $table->foreignIdFor(BankAccount::class)->constrained();
            $table->string('limit_type');
            $table->boolean('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recurring_transactions');
    }
}
