<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        "uuid",
        "course_id",
        "user_id",
        "price",
        "status",
        "count",
    ];
    protected static function boot(){
        parent::boot();
        static::creating(function ($model){
            $model->uuid = (string)Str::uuid();
        });
    }
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
