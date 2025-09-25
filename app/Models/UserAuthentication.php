<?php
// app/Models/UserAuthentication.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class UserAuthentication extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'user_authentications';

    protected $fillable = [
        'full_name',
        'age',
        'birthday',
        'username',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'birthday' => 'date',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}