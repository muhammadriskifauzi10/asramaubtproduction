<?php

namespace App\Http\Controllers\Dashboard\Booking\Pengajuankamar;

use App\Http\Controllers\Controller;
use App\Models\Pengajuanasrama;
use App\Models\Pengajuantarifasrama;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index()
    {
        $data = [
            'judul' => 'Pengajuan Kamar',
        ];

        return view('contents.dashboard.booking.pengajuankamar.main', $data);
    }
    public function datatablepengajuankamar()
    {
        $dari_tanggal = request()->input('dari_tanggal');
        $sampai_tanggal = request()->input('sampai_tanggal');
        $pendaftaran_dari = request()->input('pendaftaran_dari');
        $status_pengajuan = request()->input('status_pengajuan');

        $pengajuankamar = Pengajuanasrama::when($dari_tanggal && $sampai_tanggal, function ($query) use ($dari_tanggal, $sampai_tanggal) {
            $query->whereDate('created_at', '>=', $dari_tanggal)
                ->whereDate('created_at', '<=', $sampai_tanggal);
        })
            ->when($pendaftaran_dari, function ($query) use ($pendaftaran_dari) {
                $query->where('pendaftaran_dari', $pendaftaran_dari);
            })
            ->when($status_pengajuan, function ($query) use ($status_pengajuan) {
                $query->where('status_pengajuan', $status_pengajuan);
            })
            ->orderby('created_at', 'DESC')->get();

        $output = [];
        $no = 1;
        foreach ($pengajuankamar as $row) {
            $aksi = '
            <div class="d-flex align-items-center justify-content-center gap-1">
                <a href="' . route('pengajuankamar.detail', encrypt($row->no_pengajuan)) . '" class="btn btn-info fw-bold d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Detail Pengajuan" style="width: 40px;">
                    <i class="fa fa-eye"></i>
                </a>
            </div>
            ';

            $output[] = [
                'aksi' => $aksi,
                'tanggal_pengajuan' => Carbon::parse($row->created_at)->format('d M Y H:i'),
                'no_pengajuan' => $row->no_pengajuan,
                'pendaftaran_dari' => $row->pendaftaran_dari,
                'nim' => $row->nim,
                'no_simaba' => $row->nomor_pendaftaran,
                'nama' => $row->nama,
                'no_ktp' => $row->no_ktp,
                'jenis_kelamin' => $row->jenis_kelamin,
                'prodi' => $row->prodi,
                'permintaan_kamar' => $row->tipe_asrama,
                'status' => '<strong>' . $row->status_pengajuan . '</strong>',
            ];
        }

        return response()->json([
            'data' => $output
        ]);
    }
    public function detail($no_pengajuan)
    {
        $no_pengajuan = decrypt($no_pengajuan);

        $pengajuanasrama = Pengajuanasrama::where('no_pengajuan', $no_pengajuan)->first();
        $pengajuantarifasrama = Pengajuantarifasrama::where('no_pengajuan', $no_pengajuan)->get();

        $data = [
            'judul' => 'Pengajuan Kamar Detail',
            'pengajuanasrama' => $pengajuanasrama,
            'pengajuantarifasrama' => $pengajuantarifasrama,
        ];

        return view('contents.dashboard.booking.pengajuankamar.detail', $data);
    }
}
