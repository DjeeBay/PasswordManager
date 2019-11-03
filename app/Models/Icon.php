<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class Icon extends Model
{
    use SoftDeletes,
        Userstamps;

    protected $fillable = [
        'path',
        'filename',
        'title'
    ];
}
