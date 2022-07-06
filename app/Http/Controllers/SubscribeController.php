<?php

namespace App\Http\Controllers;

use App\Models\Subscribe;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscribeController extends Controller
{
    public function subscribe(User $user)
    {
        $user = User::Where('slug', $user->slug)->first();

        $auth = Auth::id();
        $subscriber = Subscribe::where('user_id', $auth)->where('admin_id', $user->id)->first();
        if (!$subscriber) {
            $subscribe = Subscribe::create([
            'user_id' => $auth,
            'admin_id' => $user->id
        ]);
            return response()->json([
            'subscribe' => $subscribe
        ]);
        } else {
            $subscribe = Subscribe::where('user_id', $auth)->where('admin_id', $user->id)->delete();
            return response()->json([
            'message' => 'unsubscribe'
        ]);
        }
    }
    public function is_subscribe(User $user)
    {
        $user = User::Where('slug', $user->slug)->first();
        $auth = Auth::id();
        
        $subscriber = Subscribe::where('user_id', $auth)->where('admin_id', $user->id)->first();
        if (!$subscriber) {
            return response()->json([
                'is_subscribe' => 0
              ]);
        } else {
            return response()->json([
                'is_subscribe' => 1,
                "data" => $subscriber

              ]);
        }
    }
}
