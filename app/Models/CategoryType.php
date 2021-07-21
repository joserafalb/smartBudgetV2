<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoryType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'active'];

    /**
     * Get all of the categories for the CategoryType
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categories()
    {
        return $this->hasMany(Category::class);
    }
}
