<?php

namespace App\Http\Controllers\Dashboard\Piutang;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Penyewa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function index()
    {
        $data = [
            'judul' => 'Piutang',
        ];

        return view('contents.dashboard.piutang.main', $data);
    }
    public function datatablepiutang()
    {
        $dari_tanggal = request()->input('dari_tanggal');
        $sampai_tanggal = request()->input('sampai_tanggal');
        $penyewa = request()->input('penyewa');

        $data = Pembayaran::from('pembayaran as p')
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

                'p.total_potongan_harga',
                'p.total_bayar',

                DB::raw("(
                    MAX(CASE WHEN d.jenissewa = 'asrama' THEN d.jumlah_pembayaran ELSE 0 END) +
                    MAX(CASE WHEN d.jenissewa = 'catering' THEN d.jumlah_pembayaran ELSE 0 END)
                ) as omset"),

                DB::raw("(p.total_tagihan - p.total_potongan_harga) as net_omset")
            )
            ->when($dari_tanggal && $sampai_tanggal, function ($query) use ($dari_tanggal, $sampai_tanggal) {
                $query->whereDate('p.created_at', '>=', $dari_tanggal)
                    ->whereDate('p.created_at', '<=', $sampai_tanggal);
            })
            ->when($penyewa, function ($query) use ($penyewa) {
                $query->where('p.penyewa_id', $penyewa);
            })
            ->where('p.status_pembayaran', 'pending')
            ->groupBy(
                'p.no_invoice',
                'penyewa.id',
                'p.nama_bill_to',
                'p.total_tagihan',
                'p.total_potongan_harga',
                'p.total_bayar',
            )
            ->orderBy('no_invoice', 'DESC')
            ->get();

        $output = [];
        $no = 1;
        foreach ($data as $row) {
            $penyewa = Penyewa::find($row->penyewa_id);

            $hutang = $row->net_omset - $row->total_bayar;

            $output[] = [
                'nomor' => $no++,
                'no_invoice' => $row->no_invoice,
                'nama' => $penyewa->namalengkap,
                'nim' => $penyewa->nim,
                'nama_bill_to' => $row->nama_bill_to,
                'asrama' => 'RP. ' . number_format($row->asrama, '0', '.', '.'),
                'catering' => 'RP. ' . number_format($row->catering, '0', '.', '.'),
                'omset' => 'RP. ' . number_format($row->omset, '0', '.', '.'),
                'potongan_asrama' => 'RP. ' . number_format($row->potongan_asrama, '0', '.', '.'),
                'potongan_catering' => 'RP. ' . number_format($row->potongan_catering, '0', '.', '.'),
                'total_potongan_harga' => 'RP. ' . number_format($row->total_potongan_harga, '0', '.', '.'),
                'net_omset' => 'RP. ' . number_format($row->net_omset, '0', '.', '.'),
                'total_bayar' => 'RP. ' . number_format($row->total_bayar, '0', '.', '.'),
                'piutang' => 'RP. ' . number_format($hutang, '0', '.', '.'),
            ];
        }

        return response()->json([
            'data' => $output
        ]);
    }
}
