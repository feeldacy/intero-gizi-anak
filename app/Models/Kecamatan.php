<?php

namespace App\Models;
use App\Models\Children;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    use HasFactory;

    protected $table = 'kecamatan';
    public $incrementing = false; // Karena ID kecamatan bukan autoincrement
    protected $keyType = 'string'; // Karena ID kecamatan adalah string

    // Jika timestamps tidak digunakan di tabel kecamatan
    public $timestamps = false;

    protected $fillable = [
        'id', // Tambahkan id di sini jika itu fillable
        'name',
    ];

    // Tetapkan relasi dengan children
    public function children()
    {
        return $this->hasMany(Children::class, 'kecamatan_id', 'id');
    }
}