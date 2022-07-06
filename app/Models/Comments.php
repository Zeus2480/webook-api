<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Comments extends Model
{
    use HasFactory;
    protected $fillable = [
        'body',
        'user_id',
        'post_id',
        'user_name',
        'image_path'
    ];
    protected $casts = [
        
       
        'created_at'  => 'date:d M',
        'updated_at'  => 'date:d M',
        
        
    ];
    //append user name
    public $table = "comments";
    protected $appends = [
        'user_name',
        
    ];
    //append user name
    public function getUserNameAttribute()
    {
        return $this->belongsTo(User::class, 'user_id')->first()->name;
    }
    
   

    public function posts()
    {
        return $this->belongsTo(Post::class, );
    }
    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', );
    }
    //get created at date diffForHumans();
    public function getCreatedAtAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->diffForHumans();
    }
}
