<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Course extends Model
{
    use HasFactory;
    protected $fillable = [
            "uuid",
            "name",
            "description",
            "image",
            "category_id",
            "level_id",
            "price",
            "duration",
            "status",
    ];
    protected static function boot(){
        parent::boot();
        static::creating(function($model){
            $model->uuid = (string)Str::uuid();
        });
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function level(){
        return $this->belongsTo(Level::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    public function videos()
    {
        return $this->hasMany(CourseVideo::class);
    }
    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function averageReview()
    {
        return $this->reviews()->avg('rating');
    }
}
