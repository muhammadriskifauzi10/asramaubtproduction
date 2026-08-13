<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksirefund extends Model
{
    use HasFactory;

    protected $table = 'transaksi_refund';
    protected $primaryKey = 'id';

    protected $fillable = [
        'transaksi_id',
        'no_invoice',
        'nim',
        'no_transaksi',
        'tanggal_transaksi',
        'jumlah_uang',
        'metode_pembayaran',
        'file_bukti',
        'operator_id'
    ];

    public function penyewa()
    {
        return $this->hasOne(Penyewa::class, 'nim', 'nim');
    }

    public function tagihan()
    {
        return $this->hasOne(Pembayaran::class, 'no_invoice', 'no_invoice');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'operator_id');
    }
}
