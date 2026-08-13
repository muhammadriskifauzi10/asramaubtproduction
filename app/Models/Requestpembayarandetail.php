<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requestpembayarandetail extends Model
{
    use HasFactory;

    protected $table = 'request_pembayaran_detail';
    protected $primaryKey = 'id';

    protected $fillable = [
        'no_request',
        'harga_id',
        'jenissewa',
        'harga',
        'qty',
        'jumlah_pembayaran',
        'potongan_harga',
        'status',
    ];

    public function hargas()
    {
        return $this->belongsTo(Harga::class, 'harga_id');
    }
}
