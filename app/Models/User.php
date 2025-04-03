<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = ['name', 'email', 'password'];

    /**
     * Relación: Un usuario puede tener varios tickets.
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Relación: Un usuario puede hacer varios comentarios en los tickets.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    // Asignar rol de usuario por defecto
    //booted significa que se ejecuta cuando se crea un nuevo usuario
    protected static function booted()
    {
        static::created(function ($user) {
            $user->assignRole('usuario');
        });
    }
}
