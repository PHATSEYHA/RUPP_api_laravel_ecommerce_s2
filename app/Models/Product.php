<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    // 'published_at','thumbnail',total_view', 'total_share', 'total_react' ( user មិនត្រូវបានបញ្វូល)
    protected $fillable = ['title', 'thumbnail', 'category_id', 'price', 'stock', 'short_desc', 'desc', 'status',  'expired_at'];

    // -- product(child) -> category(Parent)
    public function category()
    {
        return $this->belongsTo(
            Category::class,
            'category_id',
            'id'
        );
    }

    // -- product(child) -> tags(parent)
    // Accessor to get the formatted price
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }
}
