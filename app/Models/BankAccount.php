<?php

namespace App\Models;

use App\Models\Bank;
use App\Models\AccountType;
use App\Models\RecurringTransactions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'bank_id',
        'account_type_id',
        'creditLimit',
        'active',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the bank that owns the BankAccount
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    /**
     * Get the AccountType that owns the BankAccount
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function accountType()
    {
        return $this->belongsTo(AccountType::class);
    }

    /**
     * Get all of the recurring transactions for the BankAccount
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recurringTransactions()
    {
        return $this->hasMany(RecurringTransactions::class);
    }

    /**
     * Get all of the transactions for the BankAccount
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
