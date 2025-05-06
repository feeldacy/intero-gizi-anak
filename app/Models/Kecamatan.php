<?php

namespace App\Models;
use App\Models\Children;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Kecamatan extends Model
{
    // use HasFactory, Notifiable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $table = 'kecamatan';
    protected $fillable = [
        'name',
    ];

    protected $guard_name = 'api';


    public function posyandu()
    {
        return $this->hasOne(UnitPosyandu::class, 'kecamatan_id', 'id');
    }

    public function children()
    {
        return $this->hasMany(Children::class, 'kecamatan_id', 'id');
    }

}
