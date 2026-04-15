<?php

namespace App\Http\Controllers\Dashboard\Transaksi;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class MainController extends Controller
{
    public function index()
    {
        $data = [
            'judul' => 'Transaksi',
        ];

        return view('contents.dashboard.transaksi.main', $data);
    }
    public function datatabletransaksi()
    {
        $dari_tanggal = request()->input('dari_tanggal');
        $sampai_tanggal = request()->input('sampai_tanggal');
        $no_invoice = request()->input('no_invoice');
        $metode_pembayaran = request()->input('metode_pembayaran');

        $transaksi = Transaksi::when($dari_tanggal && $sampai_tanggal, function ($query) use ($dari_tanggal, $sampai_tanggal) {
            $query->whereDate('tanggal_transaksi', '>=', $dari_tanggal)
                ->whereDate('tanggal_transaksi', '<=', $sampai_tanggal);
        })
            ->when($no_invoice, function ($query) use ($no_invoice) {
                $query->where('no_invoice', $no_invoice);
            })
            ->when($metode_pembayaran, function ($query) use ($metode_pembayaran) {
                $query->where('metode_pembayaran', $metode_pembayaran);
            })->orderby('no_transaksi', 'DESC')->get();

        $output = [];
        $no = 1;
        foreach ($transaksi as $row) {

            $aksi = '
            <div class="d-flex align-items-center justify-content-center gap-1">
                <a href="' . route('transaksi.kwitansi', encrypt($row->no_transaksi)) . '" class="btn btn-success fw-bold d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Cetak Kwitansi" style="width: 40px;" target="_blank">
                    <i class="fa fa-receipt"></i>
                </a>
            </div>
            ';

            $file_bukti = '<a href="' . asset('img/bukti_pembayaran/' . $row->no_invoice . '/' . $row->file_bukti) . '" target="_blank" class="text-primary text-decoration-none fw-bold">FILE BUKTI</a>';

            $output[] = [
                'aksi' => $aksi,
                'no_invoice' => $row->no_invoice,
                'no_transaksi' => $row->no_transaksi,
                'tanggal_transaksi' => Carbon::parse($row->tanggal_transaksi)->format('Y-m-d H:i'),
                'jumlah_uang' => 'RP. ' . number_format($row->jumlah_uang, '0', '.', '.'),
                'metode_pembayaran' => $row->metode_pembayaran,
                'file_bukti' => $file_bukti,
                'operator' => $row->user->name,
            ];
        }

        return response()->json([
            'data' => $output
        ]);
    }
    public function kwitansi($no_transaksi)
    {
        $no_transaksi = decrypt($no_transaksi);

        if (!Transaksi::where('no_transaksi', $no_transaksi)->exists()) {
            abort(404);
        }

        $transaksi = Transaksi::where('no_transaksi', $no_transaksi)->first();
        $tagihan = Pembayaran::where('no_invoice', $transaksi->no_invoice)->first();

        $data = [
            'judul' => 'Cetak Invoice',
            'tagihan' => $tagihan,
            'transaksi' => $transaksi,
            'terbilang' => $this->terbilang($tagihan->total_bayar)
        ];

        // Generate PDF
        $pdf = Pdf::loadView('contents.dashboard.transaksi.export.kwitansi', $data);
        return $pdf->stream('cetakkwitansi-' . $tagihan->no_invoice . '.pdf');
    }
    private function terbilang($angka)
    {
        $angka = abs((int)$angka);
        $bilangan = [
            "",
            "Satu",
            "Dua",
            "Tiga",
            "Empat",
            "Lima",
            "Enam",
            "Tujuh",
            "Delapan",
            "Sembilan",
            "Sepuluh",
            "Sebelas"
        ];

        if ($angka < 12) {
            return $bilangan[$angka];
        } elseif ($angka < 20) {
            return $this->terbilang($angka - 10) . " Belas";
        } elseif ($angka < 100) {
            return $this->terbilang(intdiv($angka, 10)) . " Puluh " . $this->terbilang($angka % 10);
        } elseif ($angka < 200) {
            return "Seratus " . $this->terbilang($angka - 100);
        } elseif ($angka < 1000) {
            return $this->terbilang(intdiv($angka, 100)) . " Ratus " . $this->terbilang($angka % 100);
        } elseif ($angka < 2000) {
            return "Seribu " . $this->terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            return $this->terbilang(intdiv($angka, 1000)) . " Ribu " . $this->terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            return $this->terbilang(intdiv($angka, 1000000)) . " Juta " . $this->terbilang($angka % 1000000);
        }

        return "Angka Terlalu Besar";
    }
}
