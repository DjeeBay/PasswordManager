<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class Keepass extends Model
{
    use SoftDeletes,
        Userstamps;

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

    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }
}
