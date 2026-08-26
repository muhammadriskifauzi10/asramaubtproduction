<?php

namespace App\Http\Controllers\Dashboard\Transaksi;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Depositpembayaran;
use App\Models\Pembayaran;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class MainController extends Controller
{
    public function index()
    {
        $data = [
            'judul' => 'Pembayaran',
        ];

        return view('contents.dashboard.transaksi.main', $data);
    }
    public function datatabletransaksi()
    {
        $dari_tanggal = request()->input('dari_tanggal');
        $sampai_tanggal = request()->input('sampai_tanggal');
        $no_invoice = request()->input('no_invoice');
        $nim = request()->input('nim');
        $metode_pembayaran = request()->input('metode_pembayaran');
        $jenis_pembayaran = request()->input('jenis_pembayaran');

        $transaksi = Transaksi::when($dari_tanggal && $sampai_tanggal, function ($query) use ($dari_tanggal, $sampai_tanggal) {
            $query->whereDate('tanggal_transaksi', '>=', $dari_tanggal)
                ->whereDate('tanggal_transaksi', '<=', $sampai_tanggal);
        })
            ->when($no_invoice, function ($query) use ($no_invoice) {
                $query->where('no_invoice', $no_invoice);
            })
            ->when($nim, function ($query) use ($nim) {
                $query->where('nim', $nim);
            })
            ->when($metode_pembayaran, function ($query) use ($metode_pembayaran) {
                $query->where('metode_pembayaran', $metode_pembayaran);
            })
            ->when($jenis_pembayaran, function ($query) use ($jenis_pembayaran) {
                $query->where('jenis_transaksi', $jenis_pembayaran);
            })
            // ->orderByRaw("
            //     COALESCE(
            //         (
            //             SELECT parent.no_transaksi
            //             FROM transaksi AS parent
            //             WHERE parent.id = transaksi.parent_id
            //         ),
            //         transaksi.no_transaksi
            //     ) DESC
            // ")
            // ->orderByRaw("
            //     CASE
            //         WHEN transaksi.parent_id IS NULL THEN 0
            //         ELSE 1
            //     END ASC
            // ")
            ->orderBy('created_at', 'DESC')
            ->get();

        $output = [];
        $no = 1;
        foreach ($transaksi as $row) {
            if ($row->jenis_transaksi == "Refund") {
                $jumlah_uang = '<span style="color: red; font-weight: bold">RP. ' . number_format($row->jumlah_uang, '0', '.', '.') . '</span>';
            } else {
                $jumlah_uang = 'RP. ' . number_format($row->jumlah_uang, '0', '.', '.');
            }

            if ($row->file_bukti) {
                if ($row->jenis_transaksi == 'Deposit') {
                    $file_bukti = '<a href="' . asset('img/deposit/' . $row->no_transaksi . '/' . $row->file_bukti) . '" target="_blank" class="text-primary text-decoration-none fw-bold no-cursor">Lihat File</a>';
                } else {
                    $file_bukti = '<a href="' . asset('img/bukti_pembayaran/' . $row->no_invoice . '/' . $row->file_bukti) . '" target="_blank" class="text-primary text-decoration-none fw-bold no-cursor">Lihat File</a>';
                }
            } else {
                $file_bukti = '';
            }

            $output[] = [
                // 'aksi' => $aksi,
                'no' => $no++,
                'no_invoice' => $row->no_invoice,
                'no_transaksi' => '<button
                    class="btn btn-link p-0 lihat-detail"
                    data-no-transaksi="' . $row->no_transaksi . '">
                    ' . $row->no_transaksi . '
                </button>',
                'nama' => $row->penyewa->namalengkap ?? '',
                'tanggal_transaksi' => Carbon::parse($row->created_at)->format('Y-m-d H:i'),
                'tanggal_referensi_bayar' => Carbon::parse($row->tanggal_transaksi)->format('Y-m-d H:i'),
                'jumlah_uang' => $jumlah_uang,
                'metode_pembayaran' => $row->metode_pembayaran,
                'file_bukti' => $file_bukti,
                'jenis_pembayaran' => $row->jenis_transaksi,
                'operator' => $row->user->name,
            ];
        }

        return response()->json([
            'data' => $output
        ]);
    }
    public function detail()
    {
        if (request()->ajax()) {
            $no_transaksi = request()->input('no_transaksi');
            $transaksi = Transaksi::where('no_transaksi', $no_transaksi)->first();

            $deposit = Deposit::where('no_transaksi', $transaksi->no_transaksi)->first();

            if ($deposit) {
                if ($transaksi->jenis_transaksi == 'Refund') {
                    $tbody = [];
                    $tbody[] = '
                            <tr>
                                <td>1</td>
                                <td>' . $transaksi->no_invoice . '</td>
                                <td>' . $transaksi->no_transaksi . '</td>
                                <td>' . Carbon::parse($transaksi->created_at)->format('Y-m-d H:i') . '</td>
                                <td>RP. ' . number_format($transaksi->jumlah_uang, '0', '.', '.') . '</td>
                                <td>' . $transaksi->metode_pembayaran . '</td>
                                <td>' . Carbon::parse($transaksi->tanggal_transaksi)->format('Y-m-d H:i') . '</td>
                                <td>' . $transaksi->user->name . '</td>
                            </tr>
                            ';

                    $dataHTML = '
                    <div class="modal-content" autocomplete="off">
                        <div class="modal-header">
                            <h5 class="modal-title">Detail Pembayaran</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="mb-2">
                                    <tbody>
                                        <tr>
                                            <td>Parent No Kuitansi</td>
                                            <td width="20" class="text-right">:</td>
                                            <td>' . $transaksi->parent->no_transaksi . '</td>
                                        </tr>
                                        <tr>
                                            <td>Jumlah Uang</td>
                                            <td width="20" class="text-right">:</td>
                                            <td>RP. ' . number_format($transaksi->parent->jumlah_uang, '0', '.', '.') . '</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="table m-0" style="width: 100%">
                                    <thead class="bg-dark text-light">
                                        <tr>
                                            <th scope="col" width="50">NO</th>
                                            <th scope="col">NO INVOICE</th>
                                            <th scope="col">NO KUITANSI</th>
                                            <th scope="col">TANGGAL PEMBAYARAN</th>
                                            <th scope="col">JUMLAH UANG</th>
                                            <th scope="col">METODE PEMBAYARAN</th>
                                            <th scope="col">TANGGAL REFERENSI BAYAR</th>
                                            <th scope="col">OPERATOR</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ' . implode('', $tbody) . '
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    ';
                } else {
                    $depositpembayaran = Depositpembayaran::where('deposit_id', $deposit->id)->orderBy('created_at', 'DESC')->get();

                    $tbodydeposit = [];
                    if ($depositpembayaran->count() > 0) {
                        $no = 1;
                        foreach ($depositpembayaran as $row) {
                            $detail = '<a href="' . route('tagihan.detail', encrypt($row->no_invoice)) . '" class="btn btn-link" target="_blank">
                            ' . $row->no_invoice . '
                            </a>';

                            if ($row->jenis_pembayaran == 'Pengembalian') {
                                $jumlah_pembayaran = '<span style="color: red; font-weight: bold">
                                                        RP. ' . number_format($row->jumlah_digunakan, '0', '.', '.') . '
                                                    </span>';
                            } else {
                                $jumlah_pembayaran = '<span>
                                                        RP. ' . number_format($row->jumlah_digunakan, '0', '.', '.') . '
                                                    </span>';
                            }

                            $tbodydeposit[] = '
                            <tr>
                                <td>' . $no++ . '</td>
                                <td>' . $row->deposit->no_transaksi . '</td>
                                <td>' . Carbon::parse($row->created_at)->format('Y-m-d H:i') . '</td>
                                <td>' . $jumlah_pembayaran . '</td>
                                <td>' . $detail . '</td>
                                <td>' . $row->jenis_pembayaran . '</td>
                                <td>' . $row->user->name . '</td>
                            </tr>
                            ';
                        }
                    } else {
                        $tbodydeposit[] = '
                            <tr>
                                <td class="text-center" colspan="7">Tidak ada data</td>
                            </tr>
                            ';
                    }

                    $transaksirefund = Transaksi::where('parent_id', $transaksi->id)->get();
                    $tbody = [];
                    if ($transaksirefund->count() > 0) {
                        $no = 1;
                        foreach ($transaksirefund as $row) {
                            $jumlah_uang = '<span style="color: red; font-weight: bold">RP. ' . number_format($row->jumlah_uang, '0', '.', '.') . '</span>';

                            $tbody[] = '
                            <tr>
                                <td>' . $no++ . '</td>
                                <td>' . $row->parent->no_invoice . '</td>
                                <td>' . $row->no_transaksi . '</td>
                                <td>' . Carbon::parse($row->created_at)->format('Y-m-d H:i') . '</td>
                                <td>' . $jumlah_uang . '</td>
                                <td>' . $row->metode_pembayaran . '</td>
                                <td>' . Carbon::parse($row->tanggal_transaksi)->format('Y-m-d H:i') . '</td>
                                <td>' . $row->user->name . '</td>
                            </tr>
                            ';
                        }
                    } else {
                        $tbody[] = '
                            <tr>
                                <td class="text-center" colspan="8">Tidak ada data</td>
                            </tr>
                            ';
                    }

                    $dataHTML = '
                    <div class="modal-content" autocomplete="off">
                        <div class="modal-header">
                            <h5 class="modal-title">Detail Pembayaran</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="table-responsive mb-3">
                                <h6>Penggunaan Deposit</h6>
                                <table class="table m-0" style="width: 100%">
                                    <thead class="bg-dark text-light">
                                        <tr>
                                            <th scope="col" width="50">NO</th>
                                            <th scope="col">NO KUITANSI</th>
                                            <th scope="col">TANGGAL PENGGUNAAN</th>
                                            <th scope="col">JUMLAH DIGUNAKAN</th>
                                            <th scope="col">NO INVOICE</th>
                                            <th scope="col">JENIS PEMBAYARAN</th>
                                            <th scope="col">OPERATOR</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ' . implode('', $tbodydeposit) . '
                                    </tbody>
                                </table>
                            </div>
                            <div class="table-responsive">
                                <h6>Refund Deposit</h6>
                                <table class="table m-0" style="width: 100%">
                                    <thead class="bg-dark text-light">
                                        <tr>
                                            <th scope="col" width="50">NO</th>
                                            <th scope="col">NO INVOICE</th>
                                            <th scope="col">NO KUITANSI</th>
                                            <th scope="col">TANGGAL PEMBAYARAN</th>
                                            <th scope="col">JUMLAH UANG</th>
                                            <th scope="col">METODE PEMBAYARAN</th>
                                            <th scope="col">TANGGAL REFERENSI BAYAR</th>
                                            <th scope="col">OPERATOR</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ' . implode('', $tbody) . '
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    ';
                }
            } else {
                if ($transaksi->jenis_transaksi == 'Refund') {
                    $tbody = [];
                    $tbody[] = '
                            <tr>
                                <td>1</td>
                                <td>' . $transaksi->no_invoice . '</td>
                                <td>' . $transaksi->no_transaksi . '</td>
                                <td>' . Carbon::parse($transaksi->created_at)->format('Y-m-d H:i') . '</td>
                                <td>RP. ' . number_format($transaksi->jumlah_uang, '0', '.', '.') . '</td>
                                <td>' . $transaksi->metode_pembayaran . '</td>
                                <td>' . Carbon::parse($transaksi->tanggal_transaksi)->format('Y-m-d H:i') . '</td>
                                <td>' . $transaksi->user->name . '</td>
                            </tr>
                            ';

                    $dataHTML = '
                    <div class="modal-content" autocomplete="off">
                        <div class="modal-header">
                            <h5 class="modal-title">Detail Pembayaran</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="mb-2">
                                    <tbody>
                                        <tr>
                                            <td>Parent No Kuitansi</td>
                                            <td width="20" class="text-right">:</td>
                                            <td>' . $transaksi->parent->no_transaksi . '</td>
                                        </tr>
                                        <tr>
                                            <td>Jumlah Uang</td>
                                            <td width="20" class="text-right">:</td>
                                            <td>RP. ' . number_format($transaksi->parent->jumlah_uang, '0', '.', '.') . '</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="table m-0" style="width: 100%">
                                    <thead class="bg-dark text-light">
                                        <tr>
                                            <th scope="col" width="50">NO</th>
                                            <th scope="col">NO INVOICE</th>
                                            <th scope="col">NO KUITANSI</th>
                                            <th scope="col">TANGGAL PEMBAYARAN</th>
                                            <th scope="col">JUMLAH UANG</th>
                                            <th scope="col">METODE PEMBAYARAN</th>
                                            <th scope="col">TANGGAL REFERENSI BAYAR</th>
                                            <th scope="col">OPERATOR</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ' . implode('', $tbody) . '
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    ';
                } else {
                    $transaksirefund = Transaksi::where('parent_id', $transaksi->id)->get();
                    $tbody = [];
                    if ($transaksirefund->count() > 0) {
                        $no = 1;
                        foreach ($transaksirefund as $row) {
                            $tbody[] = '
                            <tr>
                                <td>' . $no++ . '</td>
                                <td>' . $row->no_invoice . '</td>
                                <td>' . $row->no_transaksi . '</td>
                                <td>' . Carbon::parse($row->created_at)->format('Y-m-d H:i') . '</td>
                                <td>RP. ' . number_format($row->jumlah_uang, '0', '.', '.') . '</td>
                                <td>' . $row->metode_pembayaran . '</td>
                                <td>' . Carbon::parse($row->tanggal_transaksi)->format('Y-m-d H:i') . '</td>
                                <td>' . $row->user->name . '</td>
                            </tr>
                            ';
                        }
                    } else {
                        $tbody[] = '
                            <tr>
                                <td class="text-center" colspan="8">Tidak ada data</td>
                            </tr>
                            ';
                    }

                    $dataHTML = '
                    <div class="modal-content" autocomplete="off">
                        <div class="modal-header">
                            <h5 class="modal-title">Detail Pembayaran</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="table-responsive">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td>Parent No Kuitansi</td>
                                            <td width="20" class="text-right">:</td>
                                            <td>' . $transaksi->no_transaksi . '</td>
                                        </tr>
                                        <tr>
                                            <td>Jumlah Uang</td>
                                            <td width="20" class="text-right">:</td>
                                            <td>RP. ' . number_format($transaksi->jumlah_uang, '0', '.', '.') . '</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="table m-0" style="width: 100%">
                                    <thead class="bg-dark text-light">
                                        <tr>
                                            <th scope="col" width="50">NO</th>
                                            <th scope="col">NO INVOICE</th>
                                            <th scope="col">NO KUITANSI</th>
                                            <th scope="col">TANGGAL PEMBAYARAN</th>
                                            <th scope="col">JUMLAH UANG</th>
                                            <th scope="col">METODE PEMBAYARAN</th>
                                            <th scope="col">TANGGAL REFERENSI BAYAR</th>
                                            <th scope="col">OPERATOR</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ' . implode('', $tbody) . '
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    ';
                }
            }

            return response()->json([
                'status' => 200,
                'dataHTML' => $dataHTML
            ]);
        }
    }
    public function refund()
    {
        if (request()->ajax()) {
            try {
                DB::beginTransaction();

                $transaksi_id = request()->input('id');
                $tanggal_bayar = request()->input('tanggal_bayar');
                $tgl_refund = Carbon::createFromFormat('d/m/Y H:i', $tanggal_bayar);
                $jumlah_uang = request()->input('jumlah_uang') ? str_replace('.', '', request()->input('jumlah_uang')) : 0;
                $metode_pembayaran = request()->input('metode_pembayaran');

                $transaksi = Transaksi::where('id', $transaksi_id)->first();
                $pembayaran = Pembayaran::where('no_invoice', $transaksi->no_invoice)->first();

                $refund = Transaksi::where('parent_id', $transaksi->id)
                    ->sum('jumlah_uang');

                $sisa_uang_transaksi = $transaksi->jumlah_uang + $refund;

                if ($jumlah_uang > intval($sisa_uang_transaksi)) {
                    return response()->json([
                        'status' => 500,
                        'message' => 'Jumlah uang yg diinput lebih besar daripada jumlah uang Transaksi!',
                        'icon' => 'info'
                    ]);
                }

                // Generate no transaksi
                $tahun = date('Y');
                $bulan = date('m');
                $tanggal = date('d');
                $infoterakhir = Transaksi::where('jenis_transaksi', 'Refund')->orderBy('created_at', 'DESC')->first();

                if ($infoterakhir) {
                    $tahunterakhir = Carbon::parse($infoterakhir->created_at)->format('Y') ?? 0;
                    $bulanterakhir = Carbon::parse($infoterakhir->created_at)->format('m') ?? 0;
                    $tanggalterakhir = Carbon::parse($infoterakhir->created_at)->format('d') ?? 0;
                    $nomor = substr($infoterakhir->no_transaksi, 7);

                    if ($tahun != $tahunterakhir || $bulan != $bulanterakhir || $tanggal != $tanggalterakhir) {
                        $nomor = 0;
                    }
                } else {
                    $nomor = 0;
                }

                // yymmddxxxxxx
                $no_refund = sprintf('%02d%02d%02d%06d', date('y'), $bulan, $tanggal, $nomor + 1);

                $post = Transaksi::create([
                    'parent_id' => $transaksi->id,
                    'no_invoice' => $pembayaran->no_invoice,
                    'nim' => $pembayaran->penyewa->nim,
                    'no_transaksi' => 'R' . $no_refund,
                    'tanggal_transaksi' => $tgl_refund,
                    'jumlah_uang' => -$jumlah_uang,
                    'metode_pembayaran' => $metode_pembayaran,
                    'jenis_transaksi' => 'Refund',
                    'operator_id' => auth()->user()->id
                ]);

                if ($post) {
                    $pembayaran->decrement('total_bayar', $jumlah_uang);

                    DB::commit();
                    return response()->json([
                        'status' => 200,
                        'message' => 'Refund berhasil!',
                        'icon' => 'success',
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
    public function kwitansi($no_transaksi)
    {
        $no_transaksi = decrypt($no_transaksi);

        if (!Transaksi::where('no_transaksi', $no_transaksi)->exists()) {
            abort(404);
        }

        $transaksi = Transaksi::where('no_transaksi', $no_transaksi)->where('jenis_transaksi', 'Asrama')->first();
        $tagihan = Pembayaran::where('no_invoice', $transaksi->no_invoice)->first();
        $data = [
            'judul' => 'Cetak Kwitansi',
            'tagihan' => $tagihan,
            'transaksi' => $transaksi,
            'terbilang' => $this->terbilang($tagihan->total_bayar)
        ];

        // Generate PDF
        $pdf = Pdf::loadView('contents.dashboard.transaksi.export.kwitansi', $data);
        return $pdf->stream('cetakkwitansi-' . $tagihan->no_invoice . '.pdf');
    }
    public function refundkwitansi($no_transaksi)
    {
        $no_transaksi = decrypt($no_transaksi);

        if (!Transaksi::where('no_transaksi', $no_transaksi)->exists()) {
            abort(404);
        }

        $transaksi = Transaksi::where('no_transaksi', $no_transaksi)->where('jenis_transaksi', 'Refund')->first();
        $tagihan = Pembayaran::where('no_invoice', $transaksi->no_invoice)->first();
        $data = [
            'judul' => 'Cetak Refund',
            'tagihan' => $tagihan,
            'transaksi' => $transaksi,
            'terbilang' => $this->terbilang($tagihan->total_bayar)
        ];

        // Generate PDF
        $pdf = Pdf::loadView('contents.dashboard.transaksi.export.refund', $data);
        return $pdf->stream('cetakrefund-' . $tagihan->no_invoice . '.pdf');
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
