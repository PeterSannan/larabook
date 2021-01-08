<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    public $guarded = [];
    public $dates = [
        'confirmed_at'
    ];

    public static function friendship($user_id) {
        return (new static ())
        ->where(function($query) use ($user_id){
            $query->where('user_id', auth()->id())
                ->where('friend_id',$user_id);
        })
        ->orWhere(function($query) use ($user_id){
            $query->where('friend_id', auth()->id())
                ->where('user_id',$user_id);
        })->first(); 
    }

    public static function friendships(){
        return (new static())
            ->whereNotNull('confirmed_at')
            ->where(function($query){
                $query->where('user_id', auth()->id())
                ->orWhere('friend_id', auth()->id());
            })
            ->get();
    }
}
