<?php

namespace App\Models;

use App\Models\CategoryType;
use App\Models\RecurringTransactions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category_type_id', 'active'];

    /**
     * Get the category yype that owns the Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function categoryType()
    {
        return $this->belongsTo(CategoryType::class);
    }

    /**
     * Get all of the recurring transactions for the Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recurringTransactions()
    {
        return $this->hasMany(RecurringTransactions::class);
    }
}
