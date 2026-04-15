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
        'no_invoice',
        'no_transaksi',
        'tanggal_transaksi',
        'jumlah_uang',
        'metode_pembayaran',
        'file_bukti',
        'operator_id'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'operator_id');
    }
}
