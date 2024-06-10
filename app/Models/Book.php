<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'description', 'cover_pic', 'language', 'price', 'pdf_book', 'pages','category_id','is_published','is_free','video_url','status'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function videos()
   {
    return $this->hasMany(Video::class);
   } 

}
