<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depositpembayaran extends Model
{
    protected $table = 'deposit_pembayaran';

    protected $fillable = [
        'deposit_id',
        'parent_id',
        'nim',
        'no_invoice',
        'jumlah_digunakan',
        'jenis_pembayaran',
        'status',
        'operator_id'
    ];

    public function deposit()
    {
        return $this->belongsTo(Deposit::class, 'deposit_id');
    }

    public function pembayaran()
    {
        return $this->belongsTo(Pembayaran::class, 'no_invoice', 'no_invoice');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }
}
