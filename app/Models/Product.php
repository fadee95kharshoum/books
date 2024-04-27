<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'age',
        'about',
        'image_path',
        'arabic_file_path',
        'english_file_path',
        'exercises_file_path',
        'short_Story_file_path',
        'category_id',
    ];


    public function cards(): HasMany
    {
        return $this->hasMany(Card::class, 'product_id', 'id');
    }

    public function ebooks()
    {
        return $this->hasMany(Ebook::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}
