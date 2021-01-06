<?php

namespace App\Policies;

use App\Friend;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RespondFriendRequest
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function update(User $user, Friend $friend_request)  {
        return $user->id == $friend_request->friend_id;
    }
}
