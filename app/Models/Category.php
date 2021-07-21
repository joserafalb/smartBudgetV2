<?php

namespace App\Models;

use App\Models\CategoryType;
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
}
