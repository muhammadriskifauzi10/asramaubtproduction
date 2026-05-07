<?php

namespace App\Http\Controllers;

use App\Models\Harga;
use App\Models\Pembayaran;
use App\Models\Pembayarandetail;
use App\Models\Penyewa;
use App\Models\Scanmakan;
use App\Models\Tipecatering;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ScanmakanController extends Controller
{
    public function index()
    {
        $jenis_kelamin = '';
        $waktu_makan = '';

        if (isset($_GET['jenis_kelamin']) && in_array(strtolower($_GET['jenis_kelamin']), ['l', 'p'])) {
            $jenis_kelamin = $_GET['jenis_kelamin'];
        }

        if (isset($_GET['waktu_makan']) && in_array(strtolower($_GET['waktu_makan']), ['pagi', 'siang', 'malam'])) {
            $waktu_makan = $_GET['waktu_makan'];
        }

        $data = [
            'judul' => 'Scan Makan',
            'jenis_kelamin' => $jenis_kelamin,
            'waktu_makan' => $waktu_makan,
        ];

        return view('contents.scanmakan', $data);
    }
    public function datatablescanmakan()
    {
        $jenis_kelamin = request()->input('jenis_kelamin');
        $waktu_makan = request()->input('waktu_makan');

        $scanmakan = Scanmakan::with('penyewa')
            ->when($jenis_kelamin, function ($query) use ($jenis_kelamin) {
                $query->whereHas('penyewa', function ($q) use ($jenis_kelamin) {
                    $q->whereRaw('LOWER(jenis_kelamin) = ?', [strtolower($jenis_kelamin)]);
                });
            })
            ->when(
                $waktu_makan,
                fn($query) =>
                $query->whereRaw('LOWER(waktu_makan) = ?', [strtolower($waktu_makan)])
            )
            ->whereDate('created_at', Carbon::now())
            ->orderBy('created_at', 'DESC')
            ->get();

        $output = [];
        $no = 1;
        foreach ($scanmakan as $row) {
            $output[] = [
                'nomor' => "<strong>" . $no++ . "</strong>",
                'waktu_absensi' => "<span>" . Carbon::parse($row->waktu_absensi)->format("d/m/Y H:i:s") . "</span>",
                'nim' => $row->nim ? $row->nim : '-',
                'nama_lengkap' => $row->penyewa->namalengkap ? $row->penyewa->namalengkap : '-',
                'waktu_makan' => Str::upper($row->waktu_makan) ?? '',
            ];
        }

        return response()->json([
            'data' => $output
        ]);
    }
    public function scan()
    {
        if (request()->ajax()) {
            try {
                DB::beginTransaction();

                $nim = request()->input('nim');
                $penyewa = Penyewa::where('nim', $nim)->first();

                // validasi
                if (!$penyewa) {
                    return response()->json([
                        'status' => 422,
                        'message' => 'Anda bukan mahasiswa',
                        'icon' => 'info',
                    ]);
                }

                $startOfMonth = Carbon::now()->startOfMonth();
                $endOfMonth   = Carbon::now()->endOfMonth();

                $pembayaran = Pembayaran::where('penyewa_id', $penyewa->id)
                    ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                        $query->where('tanggal_masuk', '<=', $endOfMonth)
                            ->where('tanggal_keluar', '>=', $startOfMonth);
                    })
                    ->where('status_catering', 1)
                    ->where('status_pembayaran', '<>', 'failed')
                    ->latest()
                    ->first();

                dd($pembayaran);
                // validasi
                if (!$pembayaran || !Pembayarandetail::where('no_invoice', $pembayaran->no_invoice)->where('jenissewa', 'catering')->where('status', 1)->exists()) {
                    return response()->json([
                        'status' => 422,
                        'message' => 'Anda bukan peserta catering',
                        'icon' => 'info',
                    ]);
                }


                $now = Carbon::now();
                $start = null;
                $end = null;
                $waktu_makan = '';

                // Tentukan jadwal
                if ($now->between(Carbon::today()->setTime(6, 30), Carbon::today()->setTime(12, 0))) {
                    $start = Carbon::today()->setTime(6, 30);
                    $end = Carbon::today()->setTime(12, 0);
                    $waktu_makan = 'pagi';
                } elseif ($now->between(Carbon::today()->setTime(11, 30), Carbon::today()->setTime(15, 30))) {
                    $start = Carbon::today()->setTime(11, 30);
                    $end = Carbon::today()->setTime(15, 30);
                    $waktu_makan = 'siang';
                } elseif ($now->between(Carbon::today()->setTime(17, 30), Carbon::today()->setTime(21, 0))) {
                    $start = Carbon::today()->setTime(17, 30);
                    $end = Carbon::today()->setTime(21, 0);
                    $waktu_makan = 'malam';
                } else {
                    return response()->json([
                        'status' => 422,
                        'message' => 'Belum waktunya scan makan',
                        'icon' => 'info',
                    ]);
                }

                $pembayarandetail = Pembayarandetail::where('no_invoice', $pembayaran->no_invoice)->where('jenissewa', 'catering')->where('status', 1)->first();
                $tipecatering = Tipecatering::where('harga_id', $pembayarandetail->harga_id)->where($waktu_makan, 'Y')->first();

                // validasi
                if (!$tipecatering) {
                    return response()->json([
                        'status' => 422,
                        'message' => 'Maaf, ' . $waktu_makan . ' ini tidak ada porsi Anda',
                        'icon' => 'info',
                    ]);
                }

                // Cek apakah sudah scan di waktu makan ini
                $sudahScan = Scanmakan::where('nim', $nim)
                    ->whereBetween('waktu_absensi', [$start, $end])
                    ->exists();

                if ($sudahScan) {
                    return response()->json([
                        'status' => 422,
                        'message' => "Anda sudah scan untuk makan $waktu_makan",
                        'icon' => 'info',
                    ]);
                }

                // Simpan data scan
                Scanmakan::create([
                    'waktu_absensi' => $now,
                    'no_invoice' => $pembayaran->no_invoice,
                    'penyewa_id' => $penyewa->id,
                    'nim' => $nim,
                    'harga_id' => $pembayarandetail->harga_id,
                    'waktu_makan' => $waktu_makan
                ]);

                DB::commit();
                return response()->json([
                    'status' => 200,
                    'message' => "<p>SELAMAT BERSANTAP <h3>" . $penyewa->namalengkap . '</h3></p>',
                    'icon' => 'success',
                ]);
            } catch (Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => 500,
                    'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage(),
                    'icon' => 'error',
                ]);
            }
        }
    }
}
