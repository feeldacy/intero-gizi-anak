<?php

namespace App\Models;
use App\Models\Kecamatan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Children extends Model
{
    use HasFactory, SoftDeletes;

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
                $latest = self::withTrashed()->orderBy('id', 'desc')->first();
                if ($latest) {
                    $number = intval(substr($latest->id, 5)) + 1;
                } else {
                    $number = 1;
                }
                $child->id = 'CHILD' . str_pad($number, 3, '0', STR_PAD_LEFT);
            }
        });

        static::deleting(function ($child) {
            if (!$child->isForceDeleting()) {
                $child->nutritionRecords()->each(function ($record) {
                    $record->delete();
                });
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
