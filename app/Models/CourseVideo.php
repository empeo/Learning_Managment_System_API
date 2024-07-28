<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CourseVideo extends Model
{
    use HasFactory;
    protected $fillable = [
        'uuid',
        'course_id',
        'path',
        'clean_video'
    ];
    protected static function boot(){
        parent::boot();
        static::creating(function($model){
            $model->uuid = (string)Str::uuid();
        });
    }
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
