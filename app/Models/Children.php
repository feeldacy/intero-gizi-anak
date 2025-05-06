<?php

namespace App\Models;
use App\Models\Kecamatan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Children extends Model
{
    use HasFactory;

    protected $table = 'children';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kecamatan_id',
        'name',
        'birth_date',
        'gender',
    ];

    protected $guard_name = 'api';

    protected static function booted()
    {
        static::creating(function ($child) {
            $latest = self::latest('id')->first();
            if ($latest) {
                $number = intval(substr($latest->id, 5)) + 1;
            } else {
                $number = 1;
            }
            $child->id = 'CHILD' . str_pad($number, 3, '0', STR_PAD_LEFT);
        });
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id', 'id');
    }

}
