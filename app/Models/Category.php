<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['name','image','type','price']; 

    public function books()
    {
        return $this->hasMany(Book::class);
    }
    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
