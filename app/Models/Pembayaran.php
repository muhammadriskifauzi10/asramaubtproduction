<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';
    protected $primaryKey = 'id';

    protected $fillable = [
        'no_invoice',
        'tanggal_pembayaran',
        'tanggal_masuk',
        'tanggal_keluar',
        'durasi',
        'penyewa_id',
        'nama_bill_to',
        'kamar_id',
        'total_tagihan',
        'total_potongan_harga',
        'total_bayar',
        'status_pembayaran',
        'status_asrama',
        'status_catering',
        'operator_id'
    ];

    public function penyewa()
    {
        return $this->belongsTo(Penyewa::class, 'penyewa_id');
    }

    public function kamar()
    {
        return $this->belongsTo(Kamar::class, 'kamar_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'operator_id');
    }
}
