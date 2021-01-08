<?php

namespace App;

use App\Scopes\ReverseScope;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'body',
        'image'
    ];

    protected static function boot(){
        parent::boot();
        static::addGlobalScope(new ReverseScope()); 
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function likes() {
        return $this->belongsToMany(User::class, 'likes', 'post_id', 'user_id');
    }

    public function comments() {
        return $this->hasMany(Comment::class, 'post_id', 'id');
    }
}
