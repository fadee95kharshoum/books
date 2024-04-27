<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'text',
        'is_approved',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function review_ratings()
    {
        return $this->hasMany(ReviewRating::class);
    }

    // to check if comment is Approved
    public function isApproved()
    {
        return $this->is_approved === 1;
    }

    public static function getHighestRatedComments($productId, $limit = 5)
    {
        return self::where('product_id', $productId)
            ->orderBy('rating', 'desc')
            ->take($limit)
            ->get();
    }
}
