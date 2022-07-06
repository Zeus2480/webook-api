<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Views extends Model
{
    use HasFactory;
    public $table = "views";

    protected $fillable = [
        'views',
        'post_id',
        
    ];

    //date format to be used
    protected $casts = [
        
       
        'created_at'  => 'date:d M',
        'updated_at'  => 'date:d M',
        
        
    ];

    
    public function post()
    {
        return $this->belongsTo(Post::class,'post_id');
    }


}
