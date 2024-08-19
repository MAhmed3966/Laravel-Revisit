<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    public function getPosts()
    {
        $posts = Post::orderBy("created_at", "desc")->paginate(10);
        return response()->json($posts);
    }

    public function updatePost($id, Request $request)
    {
        $result_response = [];
        $post = Post::find($id);
        $response = Gate::inspect('update', $post);
        $user =  Auth::user();
        if ($response->allowed()) {
            $post->title = $request->title;
            $post->content = $request->content;
            $post->save();
            $result_response = ["message" => "Post Updated Successfully", "status" => 200];
        } else {
            $result_response = ["message"=> "Your not authorized to update this post","status"=> 500];
        }
        return response()->json($result_response);
    }
}
