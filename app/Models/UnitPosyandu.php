<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class UnitPosyandu extends Model
{
    // use HasFactory, Notifiable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'unit_posyandu';
    protected $fillable = [
        'kecamatan_id',
        'name',
    ];

    protected $guard_name = 'api';

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id', 'id');
    }
}
