<?php

namespace App\Http\Controllers\Dashboard\Tagihan;

use App\Http\Controllers\Controller;
use App\Models\Harga;
use App\Models\Kamar;
use App\Models\Pembayaran;
use App\Models\Pembayarandetail;
use App\Models\Penyewa;
use App\Models\Potonganharga;
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
            'judul' => 'Tagihan',
        ];

        return view('contents.dashboard.tagihan.main', $data);
    }
    public function datatabletagihan()
    {
        $dari_tanggal = request()->input('dari_tanggal');
        $sampai_tanggal = request()->input('sampai_tanggal');
        $penyewa = request()->input('penyewa');
        $status_pembayaran = request()->input('status_pembayaran');

        $pembayaran = Pembayaran::when($dari_tanggal && $sampai_tanggal, function ($query) use ($dari_tanggal, $sampai_tanggal) {
            $query->whereDate('tanggal_masuk', '>=', $dari_tanggal)
                ->whereDate('tanggal_masuk', '<=', $sampai_tanggal);
        })
            ->when($penyewa, function ($query) use ($penyewa) {
                $query->where('penyewa_id', $penyewa);
            })
            ->when($status_pembayaran != "", function ($query) use ($status_pembayaran) {
                $query->where('status_pembayaran', $status_pembayaran);
            })
            ->orderby('created_at', 'DESC')->get();

        $output = [];
        $no = 1;
        foreach ($pembayaran as $row) {
            // hitung hutang
            $net_tagihan = $row->total_tagihan - $row->total_potongan_harga;
            $hutang = ($row->total_tagihan - $row->total_potongan_harga) - $row->total_bayar;

            if ($row->status_pembayaran == 'completed') {
                $status_pembayaran = '<strong class="text-success">Completed</strong>';

                $btnbayar = '';
                $btninvoice = '';
            } else if ($row->status_pembayaran == 'pending') {
                $status_pembayaran = '<strong class="text-warning">Pending</strong>';

                $btnbayar = '
                    <button type="button" class="btn btn-success fw-bold d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Bayar Tagihan" style="width: 40px;" onclick="openModalPay(\'' . $row->no_invoice . '\')">
                        <i class="fa fa-credit-card"></i>
                    </button>
                ';
                $btninvoice = '
                    <a href="' . route('tagihan.invoice', encrypt($row->no_invoice)) . '" class="btn btn-success fw-bold d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Cetak Invoice" style="width: 40px;" target="_blank">
                        <i class="fa fa-file-invoice"></i>
                    </a>
                ';
            } else {
                $status_pembayaran = '<strong class="text-danger">Failed</strong>';

                $btnbayar = '';
                $btninvoice = '';
            }


            $aksi = '
            <div class="d-flex align-items-center justify-content-center gap-1">
                ' . $btnbayar . '
                ' . $btninvoice . '
                <a href="' . route('tagihan.detail', encrypt($row->no_invoice)) . '" class="btn btn-info fw-bold d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Detail Tagihan" style="width: 40px;">
                    <i class="fa fa-eye"></i>
                </a>
            </div>
            ';

            $output[] = [
                'aksi' => $aksi,
                'no_invoice' => $row->no_invoice,
                'status_pembayaran' => $status_pembayaran,
                'tanggal_masuk' => Carbon::parse($row->tanggal_masuk)->format('d M Y'),
                'tanggal_keluar' => Carbon::parse($row->tanggal_keluar)->format('d M Y'),
                'durasi' => $row->durasi . ' Bulan',
                'nama' => $row->penyewa->namalengkap,
                'nim' => $row->penyewa->nim,
                'kamar' => $row->kamar->nomor_kamar,
                'total_tagihan' => 'RP. ' . number_format($row->total_tagihan, '0', '.', '.'),
                'total_potongan_harga' => 'RP. ' . number_format($row->total_potongan_harga, '0', '.', '.'),
                'net_tagihan' => 'RP. ' . number_format($net_tagihan, '0', '.', '.'),
                'hutang' => 'RP. ' . number_format($hutang, '0', '.', '.'),
                'total_bayar' => 'RP. ' . number_format($row->total_bayar, '0', '.', '.'),
                'operator' => $row->user->name,
                'status_row' => $row->status_pembayaran,
            ];
        }

        return response()->json([
            'data' => $output
        ]);
    }
    public function detail($no_invoice)
    {
        $no_invoice = decrypt($no_invoice);

        $tagihan = Pembayaran::where('no_invoice', $no_invoice)->first();
        $tagihandetail = Pembayarandetail::where('no_invoice', $no_invoice)->get();

        $data = [
            'judul' => 'Tagihan Detail',
            'tagihan' => $tagihan,
            'tagihandetail' => $tagihandetail
        ];

        return view('contents.dashboard.tagihan.detail', $data);
    }
    public function bayar()
    {
        if (request()->ajax()) {
            try {
                DB::beginTransaction();

                $now = date('Y-m-d H:i:s');

                $no_invoice = request()->input('no_invoice');
                $jumlah_uang = request()->input('jumlah_uang') ? str_replace('.', '', request()->input('jumlah_uang')) : 0;
                $metode_pembayaran = request()->input('metode_pembayaran');

                $pembayaran = Pembayaran::where('no_invoice', $no_invoice)->first();

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

                $update = Pembayaran::where('no_invoice', $no_invoice)->update([
                    'tanggal_pembayaran' => $now,
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
                    $infoterakhir = Transaksi::orderBy('created_at', 'DESC')->first();

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

                    Transaksi::create([
                        'no_invoice' => $no_invoice,
                        'no_transaksi' => $no_transaksi,
                        'tanggal_transaksi' => $now,
                        'jumlah_uang' => $jumlah_uang,
                        'metode_pembayaran' => $metode_pembayaran,
                        'file_bukti' => $file_bukti,
                        'operator_id' => auth()->user()->id
                    ]);
                }

                DB::commit();
                return response()->json([
                    'status' => 200,
                    'message' => 'Pembayaran berhasil ditambahkan!',
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
    public function cancelitem()
    {
        if (request()->ajax()) {
            try {
                DB::beginTransaction();

                $no_invoice = request()->input('no_invoice');
                $id = request()->input('id');

                $item = Pembayarandetail::where('id', $id)->first();
                Pembayarandetail::where('id', $id)->update([
                    'status' => 0
                ]);

                $pembayaran = Pembayaran::where('no_invoice', $no_invoice)->first();
                $total_tagihan = $pembayaran->total_tagihan - $item->jumlah_pembayaran;
                $total_potongan_harga = $pembayaran->total_potongan_harga - $item->potongan_harga;

                if (Pembayarandetail::where('no_invoice', $no_invoice)->where('status', 1)->count() > 0) {
                    if ($total_potongan_harga >= $total_tagihan) {
                        $status = 'completed';
                    } else {
                        $status = 'pending';
                    }
                } else {
                    $status = 'failed';
                }

                Pembayaran::where('no_invoice', $no_invoice)->update([
                    'total_tagihan' => $total_tagihan,
                    'total_potongan_harga' => $total_potongan_harga,
                    'status_pembayaran' => $status
                ]);

                if ($status == 'failed') {
                    // asrama
                    if (Pembayarandetail::where('no_invoice', $no_invoice)->where('jenissewa', 'asrama')->exists()) {
                        Kamar::where('id', $pembayaran->kamar_id)->decrement('jumlah_penyewa');

                        Penyewa::where('id', $pembayaran->penyewa_id)->update([
                            'status_asrama' => 0
                        ]);
                    }

                    // catering
                    if (Pembayarandetail::where('no_invoice', $no_invoice)->where('jenissewa', 'catering')->exists()) {
                        Penyewa::where('id', $pembayaran->penyewa_id)->update([
                            'status_catering' => 0
                        ]);
                    }
                }

                DB::commit();
                return response()->json([
                    'status' => 200,
                    'message' => 'Item berhasil dibatalkan!',
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
    public function potongan_harga()
    {
        if (request()->ajax()) {
            try {
                DB::beginTransaction();

                $no_invoice = request()->input('no_invoice');
                $id = request()->input('id');
                $potongan_harga = request()->input('potongan_harga') ? str_replace('.', '', request()->input('potongan_harga')) : 0;

                $item = Pembayarandetail::where('id', $id)->first();

                $jumlah_pembayaran = $item->jumlah_pembayaran - $item->potongan_harga;
                $total_item_potongan_harga = $item->potongan_harga + $potongan_harga;

                if ($total_item_potongan_harga >= $jumlah_pembayaran) {
                    return response()->json([
                        'status' => 500,
                        'message' => 'Potongan harga yg diinput lebih besar daripada Jumlah Tagihan!',
                        'icon' => 'info'
                    ]);
                }

                Pembayarandetail::where('id', $id)->update([
                    'potongan_harga' => $total_item_potongan_harga
                ]);

                Pembayaran::where('no_invoice', $no_invoice)->update([
                    'total_potongan_harga' => Pembayarandetail::where('no_invoice', $no_invoice)->where('status', 1)->sum('potongan_harga')
                ]);

                $pembayaran = Pembayaran::where('no_invoice', $no_invoice)->first();
                $total_tagihan = $pembayaran->total_tagihan;
                $total_potongan_harga = $pembayaran->total_potongan_harga;

                if ($total_potongan_harga >= $total_tagihan) {
                    $status = 'completed';
                } else {
                    $status = 'pending';
                }

                Pembayaran::where('no_invoice', $no_invoice)->update([
                    'status_pembayaran' => $status
                ]);

                Potonganharga::create([
                    'no_invoice' => $no_invoice,
                    'pembayaran_detail_id' => $id,
                    'potongan_harga' => $potongan_harga,
                    'operator_id' => auth()->user()->id
                ]);

                DB::commit();
                return response()->json([
                    'status' => 200,
                    'message' => 'Potongan harga berhasil ditambahkan!',
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
    public function tambah()
    {
        $data = [
            'judul' => 'Buat Tagihan',
        ];

        return view('contents.dashboard.tagihan.tambah', $data);
    }
    public function create()
    {
        $tanggal_masuk = request()->input('tanggal_masuk');
        $jumlah_bulan = (int) request()->input('jumlah_bulan');
        $penyewa = request()->input('penyewa');
        $kamar = request()->input('kamar');
        $harga_asrama = request()->input('harga_asrama');
        $potongan_harga_asrama = request()->input('potongan_harga_asrama');
        $catering = request()->input('catering');
        $harga_catering = request()->input('harga_catering');
        $potongan_harga_catering = request()->input('potongan_harga_catering');

        $validator = Validator::make(request()->all(), [
            'tanggal_masuk' => ['required'],

            'jumlah_bulan' => ['required', 'integer', 'min:1'],

            'penyewa' => ['required', 'exists:penyewa,id'],
            'kamar' => ['required', 'exists:kamar,id'],

            'harga_asrama' => ['required', 'exists:harga,id'],

            'potongan_harga_asrama' => ['nullable'],

            'catering' => ['required', 'in:Y,T'],

            'harga_catering' => [
                'required_if:catering,Y',
                'nullable',
                'exists:harga,id',
            ],
        ], [
            'tanggal_masuk.required' => 'Kolom tanggal masuk wajib diisi',
            'tanggal_masuk.date' => 'Format tanggal tidak valid',

            'jumlah_bulan.required' => 'Jumlah bulan wajib diisi',
            'jumlah_bulan.integer' => 'Harus berupa angka',
            'jumlah_bulan.min' => 'Minimal 1 bulan',

            'penyewa.required' => 'Penyewa wajib dipilih',
            'penyewa.exists' => 'Penyewa tidak valid',

            'kamar.required' => 'Kamar wajib dipilih',
            'kamar.exists' => 'Kamar tidak valid',

            'harga_asrama.required' => 'Harga asrama wajib dipilih',
            'harga_asrama.exists' => 'Harga asrama tidak valid',

            'catering.required' => 'Pilih catering',
            'catering.in' => 'Pilihan catering tidak valid',

            'harga_catering.required_if' => 'Harga catering wajib dipilih jika catering aktif',
            'harga_catering.exists' => 'Harga catering tidak valid',
        ]);


        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $data_penyewa = Penyewa::where('id', $penyewa)->first();

            // generate no invoice
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

            // dd($no_invoice);
            $data_harga_asrama = Harga::where('id', $harga_asrama)->first();
            $data_harga_catering = Harga::where('id', $harga_catering)->first();

            // tanggal masuk
            $tgl_masuk = Carbon::createFromFormat('d/m/Y', $tanggal_masuk);
            $tgl_keluar = $tgl_masuk->copy()->addMonthsNoOverflow($jumlah_bulan);

            // asrama
            $harga_per_bulan_asrama = $data_harga_asrama->harga;
            $total_tagihan_asrama = $harga_per_bulan_asrama * $jumlah_bulan;
            $potongan_asrama = $potongan_harga_asrama ? str_replace('.', '', $potongan_harga_asrama) : 0;

            if ($potongan_asrama > $total_tagihan_asrama) {
                DB::rollBack();
                return back()
                    ->withInput()
                    ->with('messageFailed', 'Potongan harga asrama tidak boleh melebihi total tagihan asrama!');
            } else if ($potongan_asrama < 0) {
                DB::rollBack();
                return back()
                    ->withInput()
                    ->with('messageFailed', 'Potongan harga asrama tidak valid!!');
            }

            $harga_per_bulan_catering = 0;
            $total_tagihan_catering = 0;
            $potongan_catering = 0;

            if ($catering == "Y") {
                // catering
                $harga_per_bulan_catering = $data_harga_catering->harga;
                $total_tagihan_catering = $harga_per_bulan_catering * $jumlah_bulan;
                $potongan_catering = $potongan_harga_catering ? str_replace('.', '', $potongan_harga_catering) : 0;

                if ($potongan_catering > $total_tagihan_catering) {
                    DB::rollBack();
                    return back()
                        ->withInput()
                        ->with('messageFailed', 'Potongan harga catering tidak boleh melebihi total tagihan catering!');
                } else if ($potongan_catering < 0) {
                    DB::rollBack();
                    return back()
                        ->withInput()
                        ->with('messageFailed', 'Potongan harga catering tidak valid!!');
                }
            }

            // total
            $total_tagihan = $total_tagihan_asrama + $total_tagihan_catering;
            $total_potongan_harga = $potongan_asrama + $potongan_catering;

            if ($total_potongan_harga >= $total_tagihan) {
                $status = 'completed';
            } else {
                $status = 'pending';
            }

            $post = Pembayaran::create([
                'no_invoice' => $no_invoice,
                'tanggal_masuk' => $tgl_masuk,
                'tanggal_keluar' => $tgl_keluar,
                'durasi' => $jumlah_bulan,
                'penyewa_id' => $data_penyewa->id,
                'nama_bill_to' => $data_penyewa->nama_bill_to,
                'kamar_id' => $kamar,
                'total_tagihan' => $total_tagihan,
                'total_potongan_harga' => $total_potongan_harga,
                'total_bayar' => 0,
                'status_pembayaran' => $status,
                'operator_id' => auth()->user()->id
            ]);

            if ($post) {
                $postasrama = Pembayarandetail::create([
                    'no_invoice' => $no_invoice,
                    'harga_id' => $harga_asrama,
                    'jenissewa' => 'asrama',
                    'harga' => $harga_per_bulan_asrama,
                    'qty' => $jumlah_bulan,
                    'jumlah_pembayaran' => $total_tagihan_asrama,
                    'potongan_harga' => $potongan_asrama,
                ]);

                Penyewa::where('id', $data_penyewa->id)->update([
                    'status_asrama' => 1,
                    'status' => 1,
                ]);

                Pembayaran::where('id', $post->id)->update([
                    'status_asrama' => 1,
                ]);

                // potongan harga asrama
                if ($potongan_asrama > 0) {
                    Potonganharga::create([
                        'no_invoice' => $no_invoice,
                        'pembayaran_detail_id' => $postasrama->id,
                        'potongan_harga' => $potongan_asrama,
                        'operator_id' => auth()->user()->id
                    ]);
                }

                if ($catering == "Y") {
                    $postcatering = Pembayarandetail::create([
                        'no_invoice' => $no_invoice,
                        'harga_id' => $harga_catering,
                        'jenissewa' => 'catering',
                        'harga' => $harga_per_bulan_catering,
                        'qty' => $jumlah_bulan,
                        'jumlah_pembayaran' => $total_tagihan_catering,
                        'potongan_harga' => $potongan_catering,
                    ]);

                    Penyewa::where('id', $data_penyewa->id)->update([
                        'status_catering' => 1,
                    ]);

                    Pembayaran::where('id', $post->id)->update([
                        'status_catering' => 1,
                    ]);

                    // potongan harga catering
                    if ($potongan_catering > 0) {
                        Potonganharga::create([
                            'no_invoice' => $no_invoice,
                            'pembayaran_detail_id' => $postcatering->id,
                            'potongan_harga' => $potongan_catering,
                            'operator_id' => auth()->user()->id
                        ]);
                    }
                }

                Kamar::where('id', $kamar)->increment('jumlah_penyewa');

                DB::commit();
                return redirect()->back()->with('messageSuccess', 'Tagihan berhasil ditambahkan!');
            }
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }
    }
    // tambah catering
    public function tambahcatering()
    {
        $data = [
            'judul' => 'Buat Tagihan Catering',
        ];

        return view('contents.dashboard.tagihan.tambahcatering', $data);
    }
    public function postcatering()
    {
        $tanggal_masuk = request()->input('tanggal_masuk');
        $jumlah_bulan = (int) request()->input('jumlah_bulan');
        $penyewa = request()->input('penyewa');
        $harga_catering = request()->input('harga_catering');
        $potongan_harga_catering = request()->input('potongan_harga_catering');

        $validator = Validator::make(request()->all(), [
            'tanggal_masuk' => ['required'],

            'jumlah_bulan' => ['required', 'integer', 'min:1'],

            'penyewa' => ['required', 'exists:penyewa,id'],

            'harga_catering' => ['required', 'exists:harga,id'],

            'potongan_harga_catering' => ['nullable'],
        ], [
            'tanggal_masuk.required' => 'Kolom tanggal masuk wajib diisi',
            'tanggal_masuk.date' => 'Format tanggal tidak valid',

            'jumlah_bulan.required' => 'Jumlah bulan wajib diisi',
            'jumlah_bulan.integer' => 'Harus berupa angka',
            'jumlah_bulan.min' => 'Minimal 1 bulan',

            'penyewa.required' => 'Penyewa wajib dipilih',
            'penyewa.exists' => 'Penyewa tidak valid',

            'harga_catering.required' => 'Harga catering wajib dipilih',
            'harga_catering.exists' => 'Harga catering tidak valid',
        ]);


        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $data_penyewa = Penyewa::where('id', $penyewa)->first();

            $pembayaran = Pembayaran::where('penyewa_id', $penyewa)->latest()->first();

            // generate no invoice
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

            // dd($no_invoice);
            $data_harga_catering = Harga::where('id', $harga_catering)->first();

            // tanggal masuk
            $tgl_masuk = Carbon::createFromFormat('d/m/Y', $tanggal_masuk);
            $tgl_keluar = $tgl_masuk->copy()->addMonthsNoOverflow($jumlah_bulan);

            // catering
            $harga_per_bulan_catering = $data_harga_catering->harga;
            $total_tagihan_catering = $harga_per_bulan_catering * $jumlah_bulan;
            $potongan_catering = $potongan_harga_catering ? str_replace('.', '', $potongan_harga_catering) : 0;

            if ($potongan_catering > $total_tagihan_catering) {
                DB::rollBack();
                return back()
                    ->withInput()
                    ->with('messageFailed', 'Potongan harga catering tidak boleh melebihi total tagihan catering!');
            } else if ($potongan_catering < 0) {
                DB::rollBack();
                return back()
                    ->withInput()
                    ->with('messageFailed', 'Potongan harga catering tidak valid!!');
            }

            // total
            $total_tagihan = $total_tagihan_catering;
            $total_potongan_harga = $potongan_catering;

            if ($total_potongan_harga >= $total_tagihan) {
                $status = 'completed';
            } else {
                $status = 'pending';
            }

            $post = Pembayaran::create([
                'no_invoice' => $no_invoice,
                'tanggal_masuk' => $tgl_masuk,
                'tanggal_keluar' => $tgl_keluar,
                'durasi' => $jumlah_bulan,
                'penyewa_id' => $data_penyewa->id,
                'nama_bill_to' => $data_penyewa->nama_bill_to,
                'kamar_id' => $pembayaran->kamar_id,
                'total_tagihan' => $total_tagihan,
                'total_potongan_harga' => $total_potongan_harga,
                'total_bayar' => 0,
                'status_pembayaran' => $status,
                'operator_id' => auth()->user()->id
            ]);

            if ($post) {
                $postcatering = Pembayarandetail::create([
                    'no_invoice' => $no_invoice,
                    'harga_id' => $harga_catering,
                    'jenissewa' => 'catering',
                    'harga' => $harga_per_bulan_catering,
                    'qty' => $jumlah_bulan,
                    'jumlah_pembayaran' => $total_tagihan_catering,
                    'potongan_harga' => $potongan_catering,
                ]);

                Penyewa::where('id', $data_penyewa->id)->update([
                    'status_catering' => 1,
                ]);

                Pembayaran::where('id', $post->id)->update([
                    'status_catering' => 1,
                ]);

                // potongan harga catering
                if ($potongan_catering > 0) {
                    Potonganharga::create([
                        'no_invoice' => $no_invoice,
                        'pembayaran_detail_id' => $postcatering->id,
                        'potongan_harga' => $potongan_catering,
                        'operator_id' => auth()->user()->id
                    ]);
                }

                DB::commit();
                return redirect()->back()->with('messageSuccess', 'Tagihan berhasil ditambahkan!');
            }
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }
    }
    // invoice
    public function invoice($no_invoice)
    {
        $no_invoice = decrypt($no_invoice);

        if (!Pembayaran::where('no_invoice', $no_invoice)->exists()) {
            abort(404);
        }

        $tagihan = Pembayaran::where('no_invoice', $no_invoice)->first();
        $tagihandetail = Pembayarandetail::where('no_invoice', $no_invoice)->get();

        $data = [
            'judul' => 'Cetak Invoice',
            'tagihan' => $tagihan,
            'tagihandetail' => $tagihandetail,
            'terbilang' => $this->terbilang($tagihan->total_tagihan - $tagihan->total_potongan_harga)
        ];

        // Generate PDF
        $pdf = Pdf::loadView('contents.dashboard.tagihan.export.invoice', $data);
        return $pdf->stream('cetakinvoice-' . $tagihan->no_invoice . '.pdf');
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
