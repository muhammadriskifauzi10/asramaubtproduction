<?php

namespace App\Http\Controllers\Dashboard\Pengguna\Requestkamar\Permintaan;

use App\Http\Controllers\Controller;
use App\Models\Penyewa;
use App\Models\Requestpembayaran;
use App\Models\Requestpembayarandetail;
use App\Models\Requesttransaksi;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function index($penyewa_id)
    {
        $penyewa_id = decrypt($penyewa_id);
        $penyewa = Penyewa::where('id', $penyewa_id)->first();

        $data = [
            'judul' => 'Permintaan Kamar',
            'penyewa' => $penyewa
        ];

        return view('contents.dashboard_mahasiswa.requestkamar.permintaan.main', $data);
    }
    public function datatablepermintaankamar()
    {
        $penyewa_id = request()->input('penyewa_id');

        $pembayaran = Requestpembayaran::where('penyewa_id', $penyewa_id)->where('status_verifikasi', 0)->orderby('created_at', 'DESC')->get();

        $output = [];
        $no = 1;
        foreach ($pembayaran as $row) {
            $net_tagihan = $row->total_tagihan - $row->total_potongan_harga;
            $hutang = ($row->total_tagihan - $row->total_potongan_harga) - $row->total_bayar;

            if ($row->status_pembayaran == 'completed') {
                $btnbayar = '';
                $status_pembayaran = '<strong class="text-success">Completed</strong>';
            } else if ($row->status_pembayaran == 'pending') {
                $btnbayar = '
                    <button type="button" class="btn btn-success fw-bold d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Bayar Tagihan" style="width: 40px;" onclick="openModalPay(\'' . ($row->penyewa->id) . '\', \'' . $row->no_request . '\', \'' . intval($hutang) . '\')">
                        <i class="fa fa-credit-card"></i>
                    </button>
                ';

                $status_pembayaran = '<strong class="text-warning">Pending</strong>';
            } else {
                $btnbayar = '';

                $status_pembayaran = '<strong class="text-danger">Failed</strong>';
            }

            $aksi = '
            <div class="d-flex align-items-center justify-content-center gap-1">
                ' . $btnbayar . '
                <a href="' . route('pengguna.permintaankamar.detail', encrypt($row->no_request)) . '" class="btn btn-info fw-bold d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Detail Tagihan" style="width: 40px;">
                    <i class="fa fa-eye"></i>
                </a>
            </div>
            ';

            $output[] = [
                'aksi' => $aksi,
                'tanggal_permintaan' => Carbon::parse($row->created_at)->format('d M Y H:i:s'),
                'no_request' => $row->no_request,
                'status_pembayaran' => $status_pembayaran,
                'tanggal_masuk' => Carbon::parse($row->tanggal_masuk)->format('d M Y'),
                'tanggal_keluar' => Carbon::parse($row->tanggal_keluar)->format('d M Y'),
                'durasi' => $row->durasi . ' Bulan',
                'nama' => $row->penyewa->namalengkap,
                'nim' => $row->penyewa->nim,
                'nama_bill_to' => $row->nama_bill_to,
                'kamar' => $row->kamar->nomor_kamar ?? '',
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
    public function bayar()
    {
        if (request()->ajax()) {
            try {
                DB::beginTransaction();

                $no_invoice = request()->input('no_invoice');
                $tanggal_bayar = request()->input('tanggal_bayar');
                $tgl_bayar = Carbon::createFromFormat('d/m/Y H:i', $tanggal_bayar);
                $jumlah_uang = request()->input('jumlah_uang') ? str_replace('.', '', request()->input('jumlah_uang')) : 0;
                $metode_pembayaran = request()->input('metode_pembayaran');

                $pembayaran = Requestpembayaran::where('no_request', $no_invoice)->first();

                $hutang = ($pembayaran->total_tagihan - $pembayaran->total_potongan_harga) - $pembayaran->total_bayar;

                if ($jumlah_uang > $hutang) {
                    return response()->json([
                        'status' => 500,
                        'message' => 'Jumlah uang yg diinput lebih besar daripada Total Tagihan!',
                        'icon' => 'info'
                    ]);
                }

                if ($jumlah_uang >= $hutang) {
                    $status = 'completed';
                } else {
                    $status = 'pending';
                }

                $update = Requestpembayaran::where('no_request', $no_invoice)->update([
                    'tanggal_pembayaran' => $tgl_bayar,
                    'total_bayar' => $pembayaran->total_bayar + $jumlah_uang,
                    'status_pembayaran' => $status
                ]);

                if ($update) {
                    $file_bukti = null;
                    if (request()->file('file_bukti')) {
                        $file_bukti = 'file_bukti' . time() . '.' . request()->file('file_bukti')->getClientOriginalExtension();
                        $file = request()->file('file_bukti');
                        $tujuan_upload = $_SERVER['DOCUMENT_ROOT'] . '/img/bukti_pembayaran/' . $no_invoice;
                        $file->move($tujuan_upload, $file_bukti);
                    }

                    // Generate no transaksi
                    $tahun = date('Y');
                    $bulan = date('m');
                    $tanggal = date('d');
                    $infoterakhir = Requesttransaksi::orderBy('created_at', 'DESC')->first();

                    if ($infoterakhir) {
                        $tahunterakhir = Carbon::parse($infoterakhir->created_at)->format('Y') ?? 0;
                        $bulanterakhir = Carbon::parse($infoterakhir->created_at)->format('m') ?? 0;
                        $tanggalterakhir = Carbon::parse($infoterakhir->created_at)->format('d') ?? 0;
                        $nomor = substr($infoterakhir->no_transaksi, 6);

                        if ($tahun != $tahunterakhir || $bulan != $bulanterakhir || $tanggal != $tanggalterakhir) {
                            $nomor = 0;
                        }
                    } else {
                        $nomor = 0;
                    }

                    // yymmddxxxxxx
                    $no_transaksi = sprintf('%02d%02d%02d%06d', date('y'), $bulan, $tanggal, $nomor + 1);

                    Requesttransaksi::create([
                        'no_request' => $no_invoice,
                        'no_transaksi' => $no_transaksi,
                        'tanggal_transaksi' => $tgl_bayar,
                        'jumlah_uang' => $jumlah_uang,
                        'metode_pembayaran' => $metode_pembayaran,
                        'file_bukti' => $file_bukti,
                        'operator_id' => auth()->user()->id
                    ]);

                    DB::commit();
                    return response()->json([
                        'status' => 200,
                        'message' => 'Pembayaran berhasil!',
                        'icon' => 'success',
                        'no_transaksi' => encrypt($no_transaksi)
                    ]);
                }
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
    public function detail($no_request)
    {
        $no_request = decrypt($no_request);

        $tagihan = Requestpembayaran::where('no_request', $no_request)->first();
        $tagihandetail = Requestpembayarandetail::where('no_request', $no_request)->get();

        $data = [
            'judul' => 'Tagihan Detail',
            'tagihan' => $tagihan,
            'tagihandetail' => $tagihandetail,
        ];

        return view('contents.dashboard_mahasiswa.requestkamar.permintaan.detail', $data);
    }
}
