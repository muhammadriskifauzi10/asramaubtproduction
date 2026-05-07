<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scanmakan extends Model
{
    protected $table = 'scanmakan';
    protected $primaryKey = 'id';

    protected $fillable = [
        'waktu_absensi',
        'no_invoice',
        'penyewa_id',
        'nim',
        'harga_id',
        'waktu_makan'
    ];

    public function penyewa()
    {
        return $this->hasOne(Penyewa::class, 'id', 'penyewa_id');
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'id', 'pembayaran_id');
    }
}
