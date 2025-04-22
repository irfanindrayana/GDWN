<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class userlogin extends Model
{
    protected $table = 'userlogin';
    protected $fillable = ['email', 'password'];
}
