<?php

namespace App;

use App\Models\Keepass;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    public function keepass()
    {
        return $this->hasOne(Keepass::class, 'id', 'keepass_id');
    }
}
