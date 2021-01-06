<?php

namespace App\Http\Controllers;

use App\Friend;
use App\Http\Resources\FriendRequestResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class FriendRequestsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    { 
        $request->validate([
            'friend_id' => 'required|exists:users,id'
        ]);

        auth()->user()->friends()->attach($request['friend_id']);

        $friend_request = Friend::where('user_id',auth()->id())
                         ->where('friend_id', $request['friend_id'])
                         ->first();
                         
        return new FriendRequestResource($friend_request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Friend $friend_request)
    {   $request->validate([
            'state' => 'required'
        ]);
        if($request['state']){ 
            $friend_request->confirmed_at = Carbon::now();
            $friend_request->state = $request['state'];
        }
        $this->authorize('update', $friend_request);
        $friend_request->update(); 
        return new FriendRequestResource($friend_request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Friend $friend_request)
    {
        $this->authorize('update', $friend_request);
        $friend_request->delete();
        return response([], Response::HTTP_NO_CONTENT);
    }
}
