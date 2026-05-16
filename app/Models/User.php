<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\Hashidable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, Hashidable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [

        // User Details
        'first_name',
        'last_name',
        'email',
        'mobile',

        // Society Details
        'society_name',
        'address',

        // Role
        'role',

        // Login
        'password',

        // Other
        'status',
        'device_id',
        'parent_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'parent_id' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Children Users
    |--------------------------------------------------------------------------
    */

    public function getchildrens()
    {
        $currentUserId = $this->id;

        return User::whereRaw(
            "JSON_CONTAINS(parent_id, '\"$currentUserId\"')"
        )->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Get All Descendants
    |--------------------------------------------------------------------------
    */

    public function getDescendantIds()
    {
        $ids = [$this->id];

        $children = $this->getchildrens();

        foreach ($children as $child) {

            if (!in_array($child->id, $ids)) {

                $ids = array_merge(
                    $ids,
                    $child->getDescendantIds()
                );
            }
        }

        return $ids;
    }

    /*
    |--------------------------------------------------------------------------
    | Full Name Accessor
    |--------------------------------------------------------------------------
    */

    public function getFullNameAttribute()
    {
        return trim(
            $this->first_name . ' ' . $this->last_name
        );
    }

    public function societyWings(): HasMany
    {
        return $this->hasMany(SocietyWing::class);
    }

    public function societyFlats(): HasMany
    {
        return $this->hasMany(SocietyFlat::class);
    }
}