<?php

namespace App\Http\Controllers\Dashboard\Requestkamar\Verifikasi;

use App\Http\Controllers\Controller;
use App\Models\Requestpembayaran;
use Carbon\Carbon;

class MainController extends Controller
{
    public function index()
    {
        $data = [
            'judul' => 'Verifikasi Permintaan Kamar',
        ];

        return view('contents.dashboard.requestkamar.verifikasi.main', $data);
    }
    public function datatableverifikasipermintaankamar()
    {
        $pembayaran = Requestpembayaran::where('status_verifikasi', 1)->orderby('created_at', 'DESC')->get();

        $output = [];
        $no = 1;
        foreach ($pembayaran as $row) {
            $net_tagihan = $row->total_tagihan - $row->total_potongan_harga;
            $hutang = ($row->total_tagihan - $row->total_potongan_harga) - $row->total_bayar;

            if ($row->status_pembayaran == 'completed') {
                $status_pembayaran = '<strong class="text-success">Completed</strong>';
            } else if ($row->status_pembayaran == 'pending') {
                $status_pembayaran = '<strong class="text-warning">Pending</strong>';
            } else {
                $status_pembayaran = '<strong class="text-danger">Failed</strong>';
            }

            $aksi = '
            <div class="d-flex align-items-center justify-content-center gap-1">
                <a href="" class="btn btn-info fw-bold d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Detail Request" style="width: 40px;">
                    <i class="fa fa-eye"></i>
                </a>
            </div>
            ';

            $output[] = [
                'aksi' => $aksi,
                'tanggal_permintaan' => Carbon::parse($row->created_at)->format('d M Y H:i:s'),
                'tanggal_verifikasi' => $row->tanggal_verifikasi ? Carbon::parse($row->tanggal_verifikasi)->format('d M Y H:i:s') : '',
                'no_request' => $row->no_request,
                'status_pembayaran' => $status_pembayaran,
                'tanggal_masuk' => Carbon::parse($row->tanggal_masuk)->format('d M Y'),
                'tanggal_keluar' => Carbon::parse($row->tanggal_keluar)->format('d M Y'),
                'durasi' => $row->durasi . ' Bulan',
                'nama' => $row->penyewa->namalengkap,
                'nim' => $row->penyewa->nim,
                'nama_bill_to' => $row->nama_bill_to,
                'kamar' => $row->kamar->nomor_kamar,
                'total_tagihan' => 'RP. ' . number_format($row->total_tagihan, '0', '.', '.'),
                'total_potongan_harga' => 'RP. ' . number_format($row->total_potongan_harga, '0', '.', '.'),
                'net_tagihan' => 'RP. ' . number_format($net_tagihan, '0', '.', '.'),
                'piutang' => 'RP. ' . number_format($hutang, '0', '.', '.'),
                'total_bayar' => 'RP. ' . number_format($row->total_bayar, '0', '.', '.'),
                'status_row' => $row->status_pembayaran,
            ];
        }

        return response()->json([
            'data' => $output
        ]);
    }
}
