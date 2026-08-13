<?php

namespace App\Http\Controllers\Dashboard\Deposit;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Depositpembayaran;
use App\Models\Pembayaran;
use App\Models\Transaksi;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class MainController extends Controller
{
    public function index()
    {
        $data = [
            'judul' => 'Deposit',
        ];

        return view('contents.dashboard.deposit.main', $data);
    }
    public function datatabledeposit()
    {
        $dari_tanggal = request()->input('dari_tanggal');
        $sampai_tanggal = request()->input('sampai_tanggal');
        $nim = request()->input('nim');
        $metode_pembayaran = request()->input('metode_pembayaran');
        $status = request()->input('status');

        $deposit = Deposit::when($dari_tanggal && $sampai_tanggal, function ($query) use ($dari_tanggal, $sampai_tanggal) {
            $query->whereDate('tanggal_transaksi', '>=', $dari_tanggal)
                ->whereDate('tanggal_transaksi', '<=', $sampai_tanggal);
        })
            ->when($nim, function ($query) use ($nim) {
                $query->where('nim', $nim);
            })
            ->when($metode_pembayaran, function ($query) use ($metode_pembayaran) {
                $query->where('metode_pembayaran', $metode_pembayaran);
            })
            ->when($status != "", function ($query) use ($status) {
                $query->where('status', $status);
            })
            // ->orderby('created_at', 'DESC')
            ->get();

        $output = [];
        foreach ($deposit as $row) {
            if ($row->status == 1) {
                $btn1 = '<button type="button" class="btn btn-primary fw-bold d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Refund Deposit" style="width: 40px;" onclick="openModalRefund(\'' . $row->id . '\', \'' . $row->no_transaksi . '\')">
                    <i class="fa fa-rotate-left"></i>
                </button>';
            } else {
                $btn1 = '';
            }

            $aksi = '
            <div class="d-flex align-items-center justify-content-center gap-1">
                ' . $btn1 . '
                <a href="' . route('deposit.kwitansi', encrypt($row->no_transaksi)) . '" class="btn btn-success fw-bold d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Cetak Kwitansi Deposit" style="width: 40px;" target="_blank">
                    <i class="fa fa-receipt"></i>
                </a>
            </div>
            ';

            $output[] = [
                'aksi' => $aksi,
                'namalengkap' => $row->penyewa->namalengkap,
                'nim' => $row->penyewa->nim,
                'nama_bill_to' => $row->penyewa->nama_bill_to,
                'tanggal_transaksi' => Carbon::parse($row->created_at)->format('Y-m-d H:i'),
                'tanggal_referensi_bayar' => Carbon::parse($row->tanggal_transaksi)->format('Y-m-d H:i'),
                'no_transaksi' => '<button
                    class="btn btn-link p-0 lihat-detail"
                    data-no-transaksi="' . $row->no_transaksi . '">
                    ' . $row->no_transaksi . '
                </button>',
                'jumlah_uang' => 'RP. ' . number_format($row->jumlah_uang, '0', '.', '.'),
                'saldo' => 'RP. ' . number_format($row->saldo, '0', '.', '.'),
                'metode_pembayaran' => $row->metode_pembayaran,
                'status' => $row->status == 1 ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Habis</span>',
                'operator' => $row->user->name,
            ];
        }

        return response()->json([
            'data' => $output
        ]);
    }
    public function tambah()
    {
        $data = [
            'judul' => 'Tambah Deposit',
        ];

        return view('contents.dashboard.deposit.tambah', $data);
    }
    public function create()
    {
        $validator = Validator::make(request()->all(), [
            'nim' => ['required', 'exists:penyewa,nim'],
            'tanggal_bayar' => ['required'],
            'jumlah_uang' => ['required'],
        ], [
            'nim.required' => 'Penyewa wajib dipilih',
            'nim.exists' => 'Penyewa tidak valid',
            'tanggal_bayar.required' => 'Kolom tanggal bayar referensi wajib diisi',
            'jumlah_uang.required' => 'Kolom jumlah uang wajib diisi',
        ]);


        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $nim = request()->input('nim');
            $tanggal_bayar = request()->input('tanggal_bayar');
            $tgl_bayar = Carbon::createFromFormat('d/m/Y H:i', $tanggal_bayar);
            $jumlah_uang = request()->input('jumlah_uang') ? str_replace('.', '', request()->input('jumlah_uang')) : 0;
            $metode_pembayaran = request()->input('metode_pembayaran');

            // Generate no transaksi
            $tahun = date('Y');
            $bulan = date('m');
            $tanggal = date('d');
            $infoterakhir = Transaksi::where('jenis_transaksi', 'Deposit')->orderBy('created_at', 'DESC')->first();

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
            $no_transaksi = sprintf('%02d%02d%02d%06d', date('y'), $bulan, $tanggal, $nomor + 1);

            $post = Deposit::create([
                'nim' => $nim,
                'no_transaksi' => 'D' . $no_transaksi,
                'tanggal_transaksi' => $tgl_bayar,
                'jumlah_uang' => $jumlah_uang,
                'saldo' => $jumlah_uang,
                'metode_pembayaran' => $metode_pembayaran,
                'operator_id' => auth()->user()->id
            ]);

            if ($post) {
                Transaksi::create([
                    'nim' => $nim,
                    'no_transaksi' => 'D' . $no_transaksi,
                    'tanggal_transaksi' => $tgl_bayar,
                    'jumlah_uang' => $jumlah_uang,
                    'metode_pembayaran' => $metode_pembayaran,
                    'jenis_transaksi' => 'Deposit',
                    'operator_id' => auth()->user()->id
                ]);

                DB::commit();
                return redirect()->route('deposit')->with('messageSuccess', 'Deposit berhasil ditambahkan!');
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
    public function use()
    {
        if (request()->ajax()) {
            try {
                DB::beginTransaction();

                $no_invoice = request()->input('no_invoice');
                $nim = request()->input('nim');
                $deposit_id = request()->input('deposit_id');

                $deposit = Deposit::where('nim', $nim)->where('id', $deposit_id)->first();
                $pembayaran = Pembayaran::where('no_invoice', $no_invoice)->first();
                $piutang = ($pembayaran->total_tagihan - $pembayaran->total_potongan_harga) - $pembayaran->total_bayar;
                $dari_deposit = (int) $deposit->saldo;
                $deposit_terpakai = min($dari_deposit, $piutang);
                $sisa_saldo_deposit = $dari_deposit - $deposit_terpakai;

                if ($sisa_saldo_deposit > 0) {
                    $deposit->decrement('saldo', $deposit_terpakai);
                } else {
                    $deposit->decrement('saldo', $deposit_terpakai, [
                        'status' => 0,
                    ]);
                }

                if ($deposit_terpakai >= $piutang) {
                    $status = 'completed';
                } else {
                    $status = 'pending';
                }

                $total_bayar = $pembayaran->total_bayar + $deposit_terpakai;
                Pembayaran::where('no_invoice', $no_invoice)->update([
                    'total_bayar' => $total_bayar,
                    'status_pembayaran' => $status
                ]);

                Depositpembayaran::create([
                    'deposit_id' => $deposit_id,
                    'nim' => $nim,
                    'no_invoice' => $no_invoice,
                    'jumlah_digunakan' => $deposit_terpakai,
                    'operator_id' => auth()->user()->id
                ]);

                DB::commit();
                return response()->json([
                    'status' => 200,
                    'message' => 'Deposit berhasil digunakan!',
                    'icon' => 'success',
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
    public function kwitansi($no_transaksi)
    {
        $no_transaksi = decrypt($no_transaksi);

        if (!Transaksi::where('no_transaksi', $no_transaksi)->exists()) {
            abort(404);
        }

        $transaksi = Transaksi::where('no_transaksi', $no_transaksi)->where('jenis_transaksi', 'Deposit')->first();
        $deposit = Deposit::where('no_transaksi', $no_transaksi)->first();
        $depositpembayaran = Depositpembayaran::where('deposit_id', $deposit->id)->get();

        $data = [
            'judul' => 'Cetak Kwitansi Deposit',
            'transaksi' => $transaksi,
            'deposit' => $deposit,
            'depositpembayaran' => $depositpembayaran,
            'terbilang' => $this->terbilang($transaksi->total_bayar)
        ];

        // Generate PDF
        $pdf = Pdf::loadView('contents.dashboard.deposit.export.kwitansi', $data);
        return $pdf->stream('cetakkwitansi-' . $deposit->no_transaksi . '.pdf');
    }
    public function refund()
    {
        if (request()->ajax()) {
            try {
                DB::beginTransaction();

                $deposit_id = request()->input('id');
                $tanggal_bayar = request()->input('tanggal_bayar');
                $tgl_refund = Carbon::createFromFormat('d/m/Y H:i', $tanggal_bayar);
                $jumlah_uang = request()->input('jumlah_uang') ? str_replace('.', '', request()->input('jumlah_uang')) : 0;
                $metode_pembayaran = request()->input('metode_pembayaran');

                $deposit = Deposit::where('id', $deposit_id)->first();
                $transaksi = Transaksi::where('no_transaksi', $deposit->no_transaksi)->first();

                if ($jumlah_uang > intval($deposit->saldo)) {
                    return response()->json([
                        'status' => 500,
                        'message' => 'Jumlah uang yg diinput lebih besar daripada Total Saldo deposit!',
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
                    'nim' => $deposit->nim,
                    'no_transaksi' => 'R' . $no_refund,
                    'tanggal_transaksi' => $tgl_refund,
                    'jumlah_uang' => -$jumlah_uang,
                    'metode_pembayaran' => $metode_pembayaran,
                    'jenis_transaksi' => 'Refund',
                    'operator_id' => auth()->user()->id
                ]);

                if ($post) {
                    if ($jumlah_uang >= intval($deposit->saldo)) {
                        $deposit->decrement('saldo', $jumlah_uang, [
                            'status' => 0
                        ]);
                    } else {
                        $deposit->decrement('saldo', $jumlah_uang);
                    }

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
    public function movesaldo()
    {
        if (request()->ajax()) {
            try {
                DB::beginTransaction();

                $depositpembayaran_id = request()->input('id');

                $jumlah_uang = request()->input('jumlah_uang')
                    ? (int) str_replace('.', '', request()->input('jumlah_uang'))
                    : 0;

                $depositpembayaran = Depositpembayaran::find($depositpembayaran_id);
                $pembayaran = Pembayaran::where('no_invoice', $depositpembayaran->no_invoice)->first();
                $deposit = Deposit::find($depositpembayaran->deposit_id);

                $jumlah_digunakan = (int) $depositpembayaran->jumlah_digunakan;
                if ($jumlah_uang <= 0) {
                    return response()->json([
                        'status' => 400,
                        'message' => 'Jumlah uang harus lebih besar dari 0!',
                        'icon' => 'info'
                    ]);
                }

                if ($jumlah_uang > $jumlah_digunakan) {
                    return response()->json([
                        'status' => 500,
                        'message' => 'Jumlah uang yang diinput lebih besar daripada jumlah deposit yang digunakan!',
                        'icon' => 'info'
                    ]);
                }

                // simpan ID deposit sebelum pembayaran dihapus
                $deposit_id = $depositpembayaran->deposit_id;

                // kurangi saldo yang digunakan
                $depositpembayaran->decrement('jumlah_digunakan', $jumlah_uang);
                // kurangi total bayar pembayaran
                $pembayaran->decrement('total_bayar', $jumlah_uang);
                // tambah saldo deposit
                $deposit->increment('saldo', $jumlah_uang);

                // jika jumlah digunakan sudah habis, hapus pembayaran
                if ($jumlah_uang >= $jumlah_digunakan) {
                    $depositpembayaran->delete();
                }

                // ambil ulang deposit
                $newdeposit = Deposit::find($deposit_id);

                if ($newdeposit && $newdeposit->saldo != 0) {
                    $newdeposit->update([
                        'status' => 1
                    ]);
                }

                DB::commit();
                return response()->json([
                    'status' => 200,
                    'message' => 'Kembalikan saldo berhasil!',
                    'icon' => 'success',
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
    // shorcut
    public function getbynim($nim)
    {
        $deposit = Deposit::where('nim', $nim)
            ->where('status', 1)
            ->select('id', 'no_transaksi', 'saldo')
            ->get();

        return response()->json($deposit);
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
