<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajuanasrama extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_asrama';

    protected $primaryKey = 'id';

    protected $fillable = [
        'no_pengajuan',
        'pendaftaran_dari',
        'nim',
        'nomor_pendaftaran',
        'nama',
        'no_ktp',
        'alamat',
        'jenis_kelamin',
        'agama',
        'prodi',
        'no_hp',
        'nama_orangtua1',
        'hubungan_orangtua1',
        'no_hp_orangtua1',
        'alamat_orangtua1',
        'nama_orangtua2',
        'hubungan_orangtua2',
        'no_hp_orangtua2',
        'alamat_orangtua2',
        'tipe_asrama',
        'kebutuhan_khusus',
        'metode_persetujuan',
        'foto_ktp',
        'foto_selfi',
        'jangka_pembayaran',
        'pembayaran_bulan',
        'biaya_sewa_per_bulan',
        'total_sewa',
        'total_tarif',
        'total_pembayaran',
        'bank_id',
        'nama_bank',
        'nomor_rekening',
        'nama_pemilik',
        'tanggal_pembayaran',
        'bukti_pembayaran',
        'tanggal_persetujuan_kontrak_asrama',
        'persetujuan_kontrak_asrama',
        'status_pengajuan',
    ];

    // Relasi ke detail tarif
    public function tarif()
    {
        return $this->hasMany(
            Pengajuantarifasrama::class,
            'no_pengajuan',
            'no_pengajuan'
        );
    }
}
