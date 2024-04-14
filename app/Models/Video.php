<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'video_url',
        'video_type',
        'attachment_url',
        'position',
        'isPublished',
        'isFree',
        'book_id',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function likesDislikes()
    {
        return $this->hasMany(LikesDislike::class);
    }

    public function getLikesCountAttribute()
    {
        return $this->likesDislikes()->where('is_like', true)->count();
    }

    public function getDislikesCountAttribute()
    {
        return $this->likesDislikes()->where('is_like', false)->count();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // Define the relationship with likes
    public function likes()
    {
        return $this->belongsToMany(User::class, 'likes_dislikes', 'video_id', 'user_id')->where('is_like', true);
    }
    // Define the relationship with dislikes
    public function dislikes()
    {
        return $this->belongsToMany(User::class, 'likes_dislikes', 'video_id', 'user_id')->where('is_like', false);
    }
}
