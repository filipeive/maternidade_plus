<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    // Campos que podem ser preenchidos via create/update
    protected $fillable = [
        'name',
        'email',
        'password',
        'especialidade',
        'telefone',
        'crm',
        'email_verified_at',
    ];

    // Campos ocultos (ex: password)
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Casts para tipos de dados
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relacionamento: Um usuário pode ter muitas consultas
     */
    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    /**
     * Acessor customizado para mostrar se o usuário está ativo (email verificado)
     */
    protected function isActive(): Attribute
    {
        return Attribute::make(
            get: fn () => !is_null($this->email_verified_at)
        );
    }
    /**
     * Relacionamento: Um usuário pode ter muitas vacinas administradas
     *  */
    public function vaccines()
    {
        return $this->hasMany(Vaccine::class);
    }

    /**
     * Relacionamento: Um usuário pode ter muitas visitas domiciliares
     */
     public function homeVisits()
    {
        return $this->hasMany(HomeVisit::class);
    }
}
