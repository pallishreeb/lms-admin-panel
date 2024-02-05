<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    protected $fillable = [
        'title',
        'description',
        'video_url',
        'video_type',
        'attachment_url',
        'position',
        'isPublished',
        'isFree',
        'course_id',
    ];

    protected $casts = [
        'isPublished' => 'boolean',
        'isFree' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    // public function userProgress()
    // {
    //     return $this->hasMany(UserProgress::class);
    // }
}
