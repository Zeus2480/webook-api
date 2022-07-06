<?php

namespace App\Http\Controllers;

use App\Models\Comments;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Comments $comments, Post $post)
    {
        $comments = Comments::with('users')->where('post_id', $post->id)->get();
        return response()->json($comments);
    }
   

    public function store(Comments $comments, Post $post)
    {
        request()->validate([
            'body' => 'required'
            
        ]);
       
        
        $comments = Comments::create([
       
     'user_id' => Auth::id(),
     "post_id" => $post->id,
     'body' => request('body')
   ]);
   
   
        return response()->json($comments->load('users'));
    }

   
    public function show(Comments $comments, Post $post)
    {
        $comments = Comments::where("post_id", $post->id)->first();
        return response()->json($comments);
    }

    
    public function delete(Comments $comments)
    {
        $user = User::Where('id', Auth::id())->first();
        if ($user->id == $comments->user_id || $user->is_admin == 1) {
            $comments->delete();
            return response()->json([
            'message' => 'Comment deleted successfully',
            'data' => $comments]);
        }
        return "you are not authorized to delete this comment";
    }

    public function dasboardComments(User $user)
    {
        $post = Post::where('user_id', $user->id)->with('comments')->get();
        return response()->json($post);
    }
}
