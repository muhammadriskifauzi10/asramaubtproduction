<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Penyewa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function index()
    {

        if (auth()->user()->role_id == 8) {
            $penyewa = Penyewa::where('nim', auth()->user()->identifier)->first();

            if ($penyewa) {
                $tagihan = Pembayaran::from('pembayaran as p')
                    ->join('penyewa', 'penyewa.id', '=', 'p.penyewa_id')
                    ->leftJoin('pembayaran_detail as d', 'd.no_invoice', '=', 'p.no_invoice')
                    ->select(
                        'p.no_invoice',
                        'penyewa.id as penyewa_id',
                        'p.nama_bill_to',

                        DB::raw("MAX(CASE
                        WHEN d.jenissewa = 'asrama' THEN d.jumlah_pembayaran
                        ELSE 0
                    END) as asrama"),

                        DB::raw("MAX(CASE
                        WHEN d.jenissewa = 'catering' THEN d.jumlah_pembayaran
                        ELSE 0
                    END) as catering"),

                        DB::raw("SUM(CASE
                        WHEN d.jenissewa = 'asrama' THEN d.potongan_harga
                        ELSE 0
                    END) as potongan_asrama"),

                        DB::raw("SUM(CASE
                        WHEN d.jenissewa = 'catering' THEN d.potongan_harga
                        ELSE 0
                    END) as potongan_catering"),

                        'p.kamar_id',
                        'p.durasi',
                        'p.total_potongan_harga',
                        'p.total_bayar',

                        DB::raw("(
                        MAX(CASE WHEN d.jenissewa = 'asrama' THEN d.jumlah_pembayaran ELSE 0 END) +
                        MAX(CASE WHEN d.jenissewa = 'catering' THEN d.jumlah_pembayaran ELSE 0 END)
                    ) as tagihan"),

                        DB::raw("(p.total_tagihan - p.total_potongan_harga) as total_tagihan")
                    )
                    ->groupBy(
                        'p.no_invoice',
                        'penyewa.id',
                        'p.nama_bill_to',
                        'p.total_tagihan',
                        'p.kamar_id',
                        'p.durasi',
                        'p.total_potongan_harga',
                        'p.total_bayar',
                    )
                    ->where('status_pembayaran', 'pending')
                    ->where('penyewa_id', $penyewa->id)
                    ->orderBy('no_invoice', 'DESC')
                    ->get();
            } else {
                $tagihan = collect();
            }

            $data = [
                'judul' => 'Dasbor',
                'penyewa' => $penyewa,
                'tagihan' => $tagihan,
            ];


            return view('contents.dashboard_mahasiswa.main', $data);
        } else {
            $data = [
                'judul' => 'Dasbor',
            ];
            return view('contents.dashboard.main', $data);
        }
    }
    public function logout(Request $request)
    {
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        $logoutLink = config('services.sidara.url') . '/sso/logout?redirect_uri=' . urlencode(url('/'));

        return redirect()->away($logoutLink);
    }
}
