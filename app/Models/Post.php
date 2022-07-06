<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\Bookmark;

class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'excerpt',
        'body',
        'tags',
        'image_path',
        'status',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
       
            'created_at'  => 'date:d M Y',
            'updated_at'  => 'date:d M Y',
            
            
        ];
    protected $appends = [
            'views',
            
        ];
        

    public function comments()
    {
        return $this->hasMany(Comments::class, 'post_id');
    }

    public function likes()
    {
        return $this->hasMany(Likes::class, 'post_id');
    }
    public function views()
    {
        return $this->hasMany(Views::class);
    }
    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function bookmarks()
    {
        return $this->belongsToMany(User::class, 'bookmarks', 'user_id', 'post_id');
    }

    public function is_bookmarked(User $user)
    {
        return $this->bookmark->contains($user);
    }
    
    public function getImagePathAttribute($value)
    {
        return asset('images/' . $value);
    }

    public function getTagsAttribute($value)
    {
        return json_decode($value);
    }
//
    public function getViewsAttribute()
    {
        return $this->hasMany(Views::class, 'post_id')->sum('views');
    }
}
