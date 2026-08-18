<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;

    protected $table = 'deposit';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nim',
        'no_transaksi',
        'tanggal_transaksi',
        'jumlah_uang',
        'saldo',
        'metode_pembayaran',
        'status',
        'operator_id',
    ];

    public function pembayaran()
    {
        return $this->hasMany(Depositpembayaran::class, 'deposit_id');
    }

    public function pembayaranPenggunaan()
    {
        return $this->hasMany(Depositpembayaran::class, 'deposit_id')
            ->where('jenis_pembayaran', 'Penggunaan')
            ->where('status', 1);
    }

    public function refund()
    {
        return $this->hasMany(Depositrefund::class, 'deposit_id');
    }

    public function penyewa()
    {
        return $this->hasOne(Penyewa::class, 'nim', 'nim');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'operator_id');
    }
}
