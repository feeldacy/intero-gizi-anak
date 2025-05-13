<?php

namespace App\Models;
use App\Models\Kecamatan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Children extends Model
{
    use HasFactory;

    protected $table = 'children';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'kecamatan_id',
        'name',
        'birth_date',
        'gender',
    ];

    protected static function booted()
    {
        static::creating(function ($child) {
            if (!$child->id) {
                $latest = self::latest('id')->first();
                if ($latest) {
                    $number = intval(substr($latest->id, 5)) + 1;
                } else {
                    $number = 1;
                }
                $child->id = 'CHILD' . str_pad($number, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    // Perbaiki relasi dengan kecamatan
    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id', 'id');
    }

    // Tambahkan relasi dengan nutrition records
    public function nutritionRecords()
    {
        return $this->hasMany(NutritionRecord::class, 'child_id', 'id');
    }
}