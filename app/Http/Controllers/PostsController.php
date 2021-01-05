<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Http\Resources\PostResourceCollection;
use App\Post;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    public function store(Request $request) {
        $validate_data = $request->validate([
            'data.attributes.body'=> 'required' 
        ]);
        $post = auth()->user()->posts()->create($validate_data['data']['attributes']);
        return new PostResource($post);
    }

    public function index() { 
        return PostResource::collection(auth()->user()->posts);
    }
}
