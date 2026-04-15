<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Mahasiswa extends Model
{
    protected $connection = 'mysql_sidara';
    protected $table = 'users';
    protected $primaryKey = 'id';

    public static function getAllMahasiswa()
    {
        $token = $_COOKIE['si-dara-token'] ?? null;

        if (!$token) return collect();

        $response = Http::withToken($token)->get(
            'https://sidara.ubtsu.ac.id/api/users_with_profiles',
            ['with_trashed' => 1]
        );

        if (!$response->successful()) return collect();

        return collect($response->json('data'))
            ->where('type', 3); // filter mahasiswa
    }
}
