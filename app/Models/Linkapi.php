<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Linkapi extends Model
{
    use HasFactory;

    protected $table = 'api_link';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'jenis',
        'kategori',
        'link',
        'nama',
    ];
}
