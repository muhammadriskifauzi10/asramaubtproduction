<?php

namespace App\Http\Controllers\Dashboard\Perpanjang;

use App\Http\Controllers\Controller;
use App\Models\Harga;
use App\Models\Pembayaran;
use App\Models\Pembayarandetail;
use App\Models\Penyewa;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function index()
    {
        $data = [
            'judul' => 'Perpanjang Tagihan',
        ];

        return view('contents.dashboard.perpanjang.main', $data);
    }
    public function datatableperpanjang()
    {
        $dari_tanggal = request()->input('dari_tanggal');
        $sampai_tanggal = request()->input('sampai_tanggal');
        $penyewa = request()->input('penyewa');

        $pembayaran = Pembayaran::when($dari_tanggal && $sampai_tanggal, function ($query) use ($dari_tanggal, $sampai_tanggal) {
            $query->whereDate('tanggal_masuk', '>=', $dari_tanggal)
                ->whereDate('tanggal_masuk', '<=', $sampai_tanggal);
        })->when($penyewa, function ($query) use ($penyewa) {
            $query->where('penyewa_id', $penyewa);
        })
            ->whereDate('tanggal_keluar', '<', Carbon::now())
            ->whereNull('tanggal_perpanjang')
            ->where(function ($query) {
                $query->where('status_asrama', '<>', 0)
                    ->orWhere('status_catering', '<>', 0);
            })
            ->where('status_pembayaran', '<>', 'failed')
            ->orderBy('no_invoice', 'ASC')->get();

        $output = [];
        $no = 1;
        foreach ($pembayaran as $row) {
            $aksi = '
            <div class="d-flex align-items-center justify-content-center gap-1">
                <input type="checkbox" class="row-check" value="' . $row->id . '">
            </div>
            ';

            $detail = '<a href="' . route('tagihan.detail', encrypt($row->no_invoice)) . '" class="text-decoration-none fw-bold">
                Lihat Detail
            </a>';

            // hitung hutang
            $net_tagihan = $row->total_tagihan - $row->total_potongan_harga;
            $hutang = ($row->total_tagihan - $row->total_potongan_harga) - $row->total_bayar;

            if ($row->status_pembayaran == 'completed') {
                $status_pembayaran = '<strong class="text-success">Completed</strong>';
            } else if ($row->status_pembayaran == 'pending') {
                $status_pembayaran = '<strong class="text-warning">Pending</strong>';
            } else {
                $status_pembayaran = '<strong class="text-danger">Failed</strong>';
            }

            $output[] = [
                'aksi' => $aksi,
                'no_invoice' => $row->no_invoice,
                'status_pembayaran' => $status_pembayaran,
                'tanggal_masuk' => Carbon::parse($row->tanggal_masuk)->format('d M Y'),
                'tanggal_keluar' => Carbon::parse($row->tanggal_keluar)->format('d M Y'),
                'durasi' => $row->durasi . ' Bulan',
                'nama' => $row->penyewa->namalengkap .
                    '<br>' . $detail,
                'nim' => $row->penyewa->nim,
                'kamar' => $row->kamar->nomor_kamar,
                'total_tagihan' => 'RP. ' . number_format($row->total_tagihan, '0', '.', '.'),
                'total_potongan_harga' => 'RP. ' . number_format($row->total_potongan_harga, '0', '.', '.'),
                'net_tagihan' => 'RP. ' . number_format($net_tagihan, '0', '.', '.'),
                'hutang' => 'RP. ' . number_format($hutang, '0', '.', '.'),
                'total_bayar' => 'RP. ' . number_format($row->total_bayar, '0', '.', '.'),
                'status_row' => $row->status_pembayaran,
            ];
        }

        return response()->json([
            'data' => $output
        ]);
    }
    public function perpanjangmassal()
    {
        if (request()->all()) {
            $ids = explode(',', request()->input('ids'));

            try {
                DB::beginTransaction();

                $berhasil = [];
                foreach ($ids as $id) {
                    $master = Pembayaran::find($id);

                    if ($master) {
                        $data_penyewa = Penyewa::where('id', $master->penyewa_id)->first();

                        $tanggal = Carbon::now();
                        $year  = $tanggal->format('y');
                        $month = $tanggal->format('m');
                        $day   = $tanggal->format('d');
                        $lastPn = Pembayaran::whereDate('created_at', $tanggal->toDateString())
                            ->lockForUpdate()
                            ->orderBy('id', 'desc')
                            ->first();
                        if ($lastPn) {
                            $lastNumber = intval(substr($lastPn->no_invoice, -3));
                            $newNumber  = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
                        } else {
                            $newNumber = '001';
                        }
                        $no_invoice = $year . '' . $month . '' . $day . '-' . $newNumber;

                        $tanggalKeluar = Carbon::parse($master->tanggal_keluar)->startOfMonth();
                        $bulanSekarang = Carbon::now()->startOfMonth();
                        $jumlahbulan = $tanggalKeluar->diffInMonths($bulanSekarang);
                        $jumlahbulan = max(1, $jumlahbulan);

                        // date
                        $tanggalmasuk = Carbon::parse($master->tanggal_keluar);
                        $tenggatwaktu = $tanggalmasuk->copy()->addMonthsNoOverflow($jumlahbulan);

                        Pembayaran::create([
                            'no_invoice' => $no_invoice,
                            'tanggal_masuk' => $tanggalmasuk,
                            'tanggal_keluar' => $tenggatwaktu,
                            'durasi' => $jumlahbulan,
                            'penyewa_id' => $data_penyewa->id,
                            'nama_bill_to' => $data_penyewa->nama_bill_to,
                            'kamar_id' => $master->kamar_id,
                            'total_potongan_harga' => 0,
                            'total_bayar' => 0,
                            'status_pembayaran' => 'pending',
                            'operator_id' => auth()->user()->id
                        ]);

                        // detail
                        $total_tagihan = 0;
                        foreach (Pembayarandetail::where('no_invoice', $master->no_invoice)->get() as $detail) {
                            $tagihan = $detail->harga * $jumlahbulan;
                            $total_tagihan += $tagihan;

                            Pembayarandetail::create([
                                'no_invoice' => $no_invoice,
                                'harga_id' => $detail->harga_id,
                                'jenissewa' => $detail->jenissewa,
                                'harga' => $detail->harga,
                                'qty' => $jumlahbulan,
                                'jumlah_pembayaran' => $tagihan,
                                'potongan_harga' => 0,
                            ]);
                        }

                        Pembayaran::where('no_invoice', $no_invoice)->update([
                            'total_tagihan' => $total_tagihan,
                            'status_asrama' => $master->status_asrama,
                            'status_catering' => $master->status_catering,
                        ]);

                        Pembayaran::where('id', $id)->update([
                            'tanggal_perpanjang' => $tanggalmasuk
                        ]);
                    }
                    $berhasil[] = $id;
                }

                DB::commit();
                return response()->json([
                    'status'  => 200,
                    'message'  => 'Perpanjang tagihan berhasil diproses sebanyak ' . count($berhasil) . ' Data',
                    'icon' => 'success'
                ]);
            } catch (Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => 500,
                    'message' => $e->getMessage(),
                    'icon' => 'error'
                ]);
            }
        }
    }
}
