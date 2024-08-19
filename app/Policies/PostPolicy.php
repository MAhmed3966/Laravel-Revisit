<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function update(User $user, Post $post)
    {
        return $user->id === $post->user_id ? Response::allow()
            : Response::deny('You do not own this post.');
    }
}
