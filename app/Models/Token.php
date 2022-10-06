<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    use HasFactory;

    protected $primaryKey = 'clientToken';
    public $incrementing = false;
    protected $table = 'token';
    public $timestamps = false;

    protected $fillable = [
        'clientToken',
        'baseDomain',
        'accessToken',
        'refreshToken',
        'expires'
    ];
}
