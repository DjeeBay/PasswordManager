<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keepass extends Model
{
    protected $fillable = [
        'title',
        'category_id',
        'is_folder',
        'parent_id',
        'login',
        'password',
        'url',
        'notes',
    ];
}
