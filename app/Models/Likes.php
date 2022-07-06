<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Likes extends Model
{
    use HasFactory;
    protected $fillable = [
      
        'user_id',
        'post_id',
        'like'
        
    ];

    public function post()
        {
            return $this->belongsTo(Post::class,'post_id');
        }
    public function user()
        {
            return $this->belongsTo(User::class,'user_id',);
        }

        protected $casts = [
        
       
            'created_at'  => 'date:d M',
            'updated_at'  => 'date:d M',
            
            
        ];

}
