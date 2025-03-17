<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\MA\MA_Usuarios;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
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
        'state',
        'userRomaID'
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
        'password' => 'hashed',
        'state' => 'boolean',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isRole(['user', 'admin', 'marketing', 'salvin', 'analista']);
    }

    /*public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return str_ends_with($this->email, '@yourdomain.com') && $this->hasVerifiedEmail();
        } else if ($panel->getId() === 'employer') {
            return str_ends_with($this->email, '@yourdomain.com') && $this->hasVerifiedEmail();
        }

        return false; // default case, in case $panel->getId() is neither 'admin' nor 'owner'
    }*/


    public function management()
    {
        return $this->belongsTo( Management::class, 'brand_id', 'ID' );
    }

    public function usuarioroma(){
        return $this->hasOne(MA_Usuarios::class, 'ID', 'userRomaID');
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
