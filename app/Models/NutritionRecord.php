<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NutritionRecord extends Model
{
    use HasFactory;

    protected $table = 'nutrition_records';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'child_id',
        'user_id',
        'height_cm',
        'weight_kg',
        'bmi',
        'nutrition_status',
    ];

    protected static function booted()
    {
        static::creating(function ($nutritionRecord) {
            $latest = self::latest('id')->first();
            if ($latest) {
                $number = intval(substr($latest->id, 6)) + 1; // Ambil angka setelah prefix 'NUTRI_'
            } else {
                $number = 1;
            }
            $nutritionRecord->id = 'NUTRI' . str_pad($number, 4, '0', STR_PAD_LEFT);
        });
    }
    
    /**
     * Get the child that owns the nutrition record.
     */
    public function child(): BelongsTo
    {
        return $this->belongsTo(Children::class, 'child_id', 'id');
    }
    
    /**
     * Get the user who created the nutrition record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}