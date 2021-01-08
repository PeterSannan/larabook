<?php

namespace App\Http\Controllers;

use App\Friend;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostResourceCollection;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostsController extends Controller
{
    public function store(Request $request) {
        $validate_data = $request->validate([
            'body'=> 'required',
            'image' => ''
        ]);
        $validate_data['image'] = $validate_data['image']->store('posts-images', 's3');
        
        $post = auth()->user()->posts()->create(
            $validate_data
        );
        return new PostResource($post);
    }

    public function index() { 
        $friendships = Friend::friendships();

        if($friendships->isEmpty()) {
            return PostResource::collection(auth()->user()->posts);
        } 
        return PostResource::collection(
            Post::whereIn('user_id',[
                $friendships->pluck('user_id'),$friendships->pluck('friend_id')
            ])->get()
        );
    }
}
