<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordNotification;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles , HasSlug;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
       
            'created_at'  => 'date:d M Y',
            'updated_at'  => 'date:d M Y',
            
        
    ];

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('site')
            ->saveSlugsTo('slug');
    }

   
    public function bookmarks()
    {
        return $this->belongsToMany(Post::class, 'bookmarks', 'post_id', 'user_id');
    }



    public function providers()
    {
        return $this->hasMany(Provider::class, 'user_id', 'id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id', 'id');
    }

    public function profiles()
    {
        return $this->hasOne(Profile::class);
    }


    // public function following() {
    //     return $this->belongsToMany(User::class, 'followers', 'follower_id', 'following_id');
    // }

    // users that follow this user
    public function getImagePathAttribute($value)
    {
        if (!$value) {
            return asset('https://i.ibb.co/TPmLQyP/user.png');
        }
        return asset('images/' . $value);
    }

    public function views()
    {
        return $this->hasManyThrough(
            Views::class,
            Post::class,
            'user_id', // Foreign key on the environments table...
            'post_id', // Foreign key on the deployments table...
            'id', // Local key on the projects table...
            'id' // Local key on the environments table...
        );
    }
}
