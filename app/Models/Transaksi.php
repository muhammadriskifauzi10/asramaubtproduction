<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';
    protected $primaryKey = 'id';

    protected $fillable = [
        'penyewa_id',
        'parent_id',
        'no_invoice',
        'no_transaksi',
        'tanggal_transaksi',
        'jumlah_uang',
        'metode_pembayaran',
        'file_bukti',
        'jenis_transaksi',
        'operator_id'
    ];

    public function penyewa()
    {
        return $this->hasOne(Penyewa::class, 'id', 'penyewa_id');
    }

    public function tagihan()
    {
        return $this->hasOne(Pembayaran::class, 'no_invoice', 'no_invoice');
    }

    public function parent()
    {
        return $this->hasOne(Transaksi::class, 'id', 'parent_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'operator_id');
    }
}
