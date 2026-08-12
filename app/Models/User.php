<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Pks;

class User extends Authenticatable
{
    protected $table = 'user';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'id_pks',
        'username',
        'password',
        'level_akses',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * No password hashing - old system uses plain text passwords.
     */
    protected function casts(): array
    {
        return [];
    }

    /**
     * Relationship: User has many Pengaliran
     */
    public function pengaliran()
    {
        return $this->hasMany(Pengaliran::class, 'id_pks', 'id_pks');
    }

    /**
     * Relationship: User has many Pemeliharaan
     */
    public function pemeliharaan()
    {
        return $this->hasMany(Pemeliharaan::class, 'id_pks', 'id_pks');
    }

    /**
     * Relationship: User has many Rencana
     */
    public function rencana()
    {
        return $this->hasMany(Rencana::class, 'id_pks', 'id_pks');
    }

    public function alatBerat()
    {
        return $this->hasMany(AlatBerat::class, 'id_pks', 'id_pks');
    }

    public function monitoringAlatBerat()
    {
        return $this->hasMany(MonitoringAlatBerat::class, 'id_pks', 'id_pks');
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->level_akses === 'admin';
    }

    /**
     * Check if user is unit
     */
    public function isUnit(): bool
    {
        return $this->level_akses === 'unit';
    }

    /**
     * Check if user is mandor
     */
    public function isMandor(): bool
    {
        return $this->level_akses === 'mandor';
    }

    /**
     * Check if user is operator
     */
    public function isOperator(): bool
    {
        return $this->level_akses === 'operator';
    }

    /**
     * Check if user is field user (operator or mandor)
     */
    public function isFieldUser(): bool
    {
        return $this->isOperator() || $this->isMandor();
    }

    /**
     * Check if user can manage heavy equipment master data & status
     */
    public function canManageAlatBerat(): bool
    {
        return $this->isAdmin() || $this->isUnit() || $this->isMandor();
    }

    public function pks()
    {
        return $this->belongsTo(Pks::class, 'id_pks', 'id_pks');
    }
}
