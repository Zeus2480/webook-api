<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Profile;
use App\Models\Likes;
use App\Models\Category;
use App\Models\User;
use App\Models\Views;
use App\Models\Subscribe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use App\Events\PostPublished;
use App\Notifications\PostCreateNotification;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(User $user)
    {
        $post = Post::with('users')->withcount('likes', 'comments')->where('user_id', $user->id)->get();

        return response()->json($post);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'name' => 'required',
                'excerpt' => 'required',
                'body' => 'required',
                'image' => 'mimes:jpg,png,jpeg,webp|max:50480'

            ]
        );

        try {
            if (isset($request->image)) {
                $imagePath = time() . $request->name . '.'. $request->image->extension();
                $request->image->move(public_path('images'), $imagePath);
                
                $user = User::where('id', Auth::id())->first();
            }
           
            $posts = new Post();
            $posts->name = request('name');
            $posts->excerpt = request('excerpt');
            $posts->body = request('body');
            $posts->tags = strtolower(json_encode(request('tags')));
            
            $posts->image_path = $imagePath ?? null;
           
            $posts->user_id = Auth::id();
            $posts->category_id = request('category_id');
          
            $posts->save();
            $users = Subscribe::where('admin_id', $posts->user_id)->pluck('user_id');
            $users = User::whereIn('id', $users)->get();
        

            foreach ($users as $user) {
                $user->notify(new PostCreateNotification($posts));
            }

           
            
            return response()->json($posts);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(User $user, Post $post)
    {
        $views = Views::where('post_id', $post->id)->whereDate('created_at', Carbon::today()->toDateString())->first();

        if (!$views) {
            $views = Views::updateOrCreate([
                'post_id' => $post->id,
                'views' => 1
            ]);
        } else {
            $views->views = $views->views + 1;
            $views->save();
        }

        $user = User::where('slug', $user->slug)->first();
        $post = Post::with('users')->where('id', $post->id)->where('user_id', $user->id)->first();
        
        return response()->json(["post" => $post,
        "siteTitile" =>  $post->users['site']]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function user_all_post(Post $post, User $user)
    {
        $post = Post::where('user_id', $user->id)->get();
        return $post;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        $request->validate(
            [
                    'name' => 'required',
                    'excerpt' => 'required',
                    'body' => 'required',
                    'image' => 'mimes:jpg,png,jpeg,webp|max:50480'

                ]
        );
        try {
            $post->name = request('name');
            $post->excerpt = request('excerpt');
            $post->body = request('body');
            $post->category_id = request('category_id');


            if (request()->hasFile('image')) {
                $imagePath = time() . $request->name . '.' . $request->image->extension();
                $request->image->move(public_path('images'), $imagePath);
                $oldImagePath = public_path('images') . "\\" . $post->image_path;

                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }

                $post->image_path = $imagePath;
            }
            $post->save();

            return response()->json($post);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */

    public function delete(Post $post)
    {
        $post->delete();
        return "Post deleted successfully";
    }


 

    public function usersPost()
    {
        $post = Post::where('user_id', Auth::id())->get();
        return $post ;
    }

   
    public function category(Category $category)
    {
        $post = Post::where('category_id', $category->id)->get();
        return $post;
    }

   
    public function statusUpdateDraft(Request $request)
    {
        $request->validate(
            [
                'name' => 'required',
                'excerpt' => 'required',
                'body' => 'required',
                'image' => 'mimes:jpg,png,jpeg,webp|max:50480'

            ]
        );

      
        if (isset($request->image)) {
            $imagePath = time() . $request->name . '.'. $request->image->extension();
            $request->image->move(public_path('images'), $imagePath);
                
            $user = User::where('id', Auth::id())->first();
        }
           
        $posts = new Post();
        $posts->name = request('name');
        $posts->excerpt = request('excerpt');
        $posts->body = request('body');
        $posts->tags = strtolower(json_encode(request('tags')));
            
        $posts->image_path = $imagePath ?? null;
           
        $posts->user_id = Auth::id();
        $posts->category_id = request('category_id');
          
        $posts->save();
        $post =  $posts->update(['status' => "Draft"]);
        return response()->json([
            'message' => 'Post status updated successfully',
            'status' => "Draft",
           
        
        ]);
        

     
           
            
        return response()->json($posts);
    }

    
    public function statusUpdateArchive(Post $post)
    {
        $post = Post::withcount('likes', 'comments')->where('id', $post->id)->first();
        if ($post->status == "Draft") {
            $post->update(['status' => "published"]);
            return response()->json([
                'message' => 'Post status updated successfully',
                'status' => "published",
                'post' => $post
            
            ]);
        } elseif ($post->status == "published") {
            $post->update(['status' => "Archive"]);
            return response()->json([
                'message' => 'Post status updated successfully',
                'status' => "Archive",
                'post' => $post
            
            ]);
        } else {
            $post->update(['status' => "published"]);
            return response()->json([
                'message' => 'Post status updated successfully',
                'status' => "published",
                'post' => $post
            
            ]);
        }
    }

    
    public function post_by_tags(Request $request)
    {
        $post = Post::where('tags', 'like', '%' . $request->tags . '%')->get();
        return $post;
    }

    public function post_views()
    {
        $post = Post::where('user_id', Auth::id())->pluck('id');
        $date =   Carbon::today()->toDateString();
        $todaysviews = Views::whereIn('post_id', $post)->whereDate('created_at', $date)->sum('views');
        $weeklyViews = Views::whereIn('post_id', $post)->whereDate('created_at', '>=', Carbon::now()->subDays(7))->sum('views');
        $totalViews = Views::whereIn('post_id', $post)->sum('views');
        return response()->json([
            'todays_Views' => $todaysviews,
            'weekly_Views' => $weeklyViews,
            'total_Views' => $totalViews,
            
        ]);
    }
    
    public function all_tags(User $user)
    {
        $tags = Post::where('user_id', $user->id)->select('tags')->get();

        $result = $tags->map(function ($tag, $key) {
            return ($tag->tags);
        });
    
        $data = array_values(array_unique(Arr::flatten($result)));
    
        return response([

        'data' => $data
    ]);
    }

    public function publishedPost(User $user)
    {
        $user = User::where('slug', $user->slug)->first();
       
        if (!$user) {
            return "User not found";
        } else {
            $post = Post::where('status', 'Published')->where('user_id', $user->id)->latest()->get();
            return response()->json($post);
        }
    }

    //get all post with likes count views count of last 5 days

    public function postStats(User $user)
    {
        $user = User::where('slug', $user->slug)->first();
        $post = Post::where('user_id', $user->id)->with('views', 'likes', 'users')->withcount('likes', 'comments')->get();
        return response()->json([
        'post' => $post,
        
    ]);
    }
}
