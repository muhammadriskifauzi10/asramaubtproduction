<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depositrefund extends Model
{
    protected $table = 'deposit_refund';

    protected $fillable = [
        'deposit_id',
        'nim',
        'no_refund',
        'tanggal_refund',
        'jumlah_refund',
        'metode_pembayaran',
        'operator_id'
    ];

    public function deposit()
    {
        return $this->belongsTo(Deposit::class, 'deposit_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }
}
