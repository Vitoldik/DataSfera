<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    use HasFactory;

    protected $primaryKey = 'baseDomain';
    public $incrementing = false;
    protected $table = 'token';
    public $timestamps = false;

    protected $fillable = [
        'baseDomain',
        'accessToken',
        'refreshToken',
        'expires'
    ];
}
