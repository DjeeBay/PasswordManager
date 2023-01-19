<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class PrivateCategory extends Model
{
    use SoftDeletes,
        Userstamps;

    protected $fillable = [
        'name',
        'description',
        'color',
    ];
}
