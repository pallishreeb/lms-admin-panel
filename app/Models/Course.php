<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'title',
        'description',
        'cover_pic',
        'price',
        'isPublished',
        'isFree',
        'category_id',
        'chapters',
        'purchases',
    ];

    protected $casts = [
        'isPublished' => 'boolean',
        'isFree' => 'boolean',
        'chapters' => 'array',
        'purchases' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    
    public function chapters()
    {
        return $this->hasMany(Chapter::class, 'course_id');
    }
}