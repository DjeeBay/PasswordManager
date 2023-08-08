<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laragear\TwoFactor\Contracts\TwoFactorAuthenticatable;
use Laragear\TwoFactor\TwoFactorAuthentication;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;
use Wildside\Userstamps\Userstamps;

class User extends Authenticatable implements TwoFactorAuthenticatable
{
    use HasRoles,
        Notifiable,
        SoftDeletes,
        TwoFactorAuthentication,
        Userstamps;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'firstname', 'lastname', 'is_admin'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean'
    ];

    public function categories()
    {
        return $this->hasManyThrough(Category::class, UserCategory::class, 'user_id', 'id', 'id', 'category_id');
    }
}
