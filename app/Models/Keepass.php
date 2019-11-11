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
        'icon_id',
    ];

    protected $appends = ['icon'];

    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    public function icon()
    {
        return $this->hasOne(Icon::class, 'id', 'icon_id');
    }

    public function getIconAttribute()
    {
        return $this->icon()->first();
    }

    public function getFullpathAttribute()
    {
        $path = '/';
        if (!$this->parent_id) return $path;
        $hasParent = true;
        $parentID = $this->parent_id;
        $i = 0;
        while ($hasParent) {
            $parent = Keepass::where('id', '=', $parentID)->select('title', 'parent_id')->first();
            if ($parent) {
                $path .= $parent->title.($parent->parent_id ? '/' : null);
                if (!$parent->parent_id) {
                    $hasParent = false;
                } else {
                    $parentID = $parent->parent_id;
                }
            }
            $i++;
            if ($i > 30) dd($parent);
        }

        return $path;
    }
}
