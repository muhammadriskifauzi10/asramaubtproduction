<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Harga extends Model
{
    use HasFactory;

    protected $table = 'harga';
    protected $primaryKey = 'id';

    protected $fillable = [
        'tagih_id',
        'nama_tagihan',
        'harga',
        'operator_id',
    ];

    public function tagihan()
    {
        return $this->hasOne(Tagih::class, 'id', 'tagih_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'operator_id');
    }
}
