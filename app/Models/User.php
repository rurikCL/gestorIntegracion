<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'API_users';
    protected $connection = 'mysql-pompeyo';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'brand_id',
        'password',
        'brand_id',
        'state'
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
        'state' => 'boolean',
    ];


    public function canAccessFilament(): bool
    {
        return $this->isRole(['user', 'admin', 'marketing', 'salvin']);
    }

    public function management()
    {
        return $this->belongsTo( Management::class, 'brand_id', 'ID' );
    }

    public function brand()
    {
        return $this->belongsTo( Brand::class, 'brand_id', 'ID' );
    }

    public function isAdmin(): bool
    {
        return Auth::user()->role === 'admin';
    }

    public function isMarketing(): bool
    {
        return Auth::user()->role === 'marketing';
    }

    public function isRole($role):bool
    {
        if (is_array($role)) {
            foreach ($role as $k=>$r) {
                if (Auth::user()->role === $r) {
                    return true;
                }
            }
            return false;
        }else{
            return Auth::user()->role === $role;
        }
    }

}
