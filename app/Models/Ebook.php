<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ebook extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'ebook_file_path',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
