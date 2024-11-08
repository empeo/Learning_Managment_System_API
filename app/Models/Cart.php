<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Cart extends Model
{
    use HasFactory;
    protected $fillable = [
        'uuid',
        'user_id',
        'course_id',
        'count',
    ];
    protected static function boot(){
        parent::boot();
        static::creating(function($model){
            $model->uuid = (string)Str::uuid();
        });
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
