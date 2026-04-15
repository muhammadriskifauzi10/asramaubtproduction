<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kamar extends Model
{
    use HasFactory;

    protected $table = 'kamar';
    protected $primaryKey = 'id';

    protected $fillable = [
        'token_listrik',
        'tipe_asrama_id',
        'lantai_id',
        'nomor_kamar',
        'kapasitas',
        'jumlah_penyewa',
        'status',
        'operator_id'
    ];

    public function lantai()
    {
        return $this->hasOne(Lantai::class, 'id', 'lantai_id');
    }

    public function type()
    {
        return $this->hasOne(Tipeasrama::class, 'id', 'tipe_asrama_id');
    }
}
