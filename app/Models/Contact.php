<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $table = 'contact';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'first_name',
        'last_name',
        'responsible_user_id',
        'group_id',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'closest_task_at',
        'account_id',
        'is_unsorted',
        'company_id',
        'custom_fields_values'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'closest_task_at' => 'datetime'
    ];
}
