<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requesttransaksi extends Model
{
    use HasFactory;

    protected $table = 'request_transaksi';
    protected $primaryKey = 'id';

    protected $fillable = [
        'no_request',
        'no_transaksi',
        'tanggal_transaksi',
        'jumlah_uang',
        'metode_pembayaran',
        'file_bukti',
        'operator_id'
    ];

    public function tagihan()
    {
        return $this->hasOne(Pembayaran::class, 'no_invoice', 'no_invoice');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'operator_id');
    }
}
