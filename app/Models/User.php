<?php

namespace App\Models;

use App\Models\Role;
use App\Models\Ticket;           // ← Importar el modelo Ticket
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
     * Relaciones con Role
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Comprueba si el usuario tiene un rol concreto
     */
    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }

    /**
     * Relación uno a muchos: un usuario tiene muchos tickets
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
