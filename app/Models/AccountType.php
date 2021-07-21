<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'active', 'isCredit'];

    /**
     * Get all of the bank accounts for the AccountType
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class);
    }
}
