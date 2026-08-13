<?php

namespace App\Http\Controllers\Dashboard\Pengguna\Tagihan;

use App\Http\Controllers\Controller;
use App\Models\Harga;
use App\Models\Kamar;
use App\Models\Penyewa;
use App\Models\Requestpembayaran;
use App\Models\Requestpembayarandetail;
use App\Models\Requesttransaksi;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MainController extends Controller
{
    public function index()
    {
        $kamar = Kamar::whereColumn('jumlah_penyewa', '<', 'kapasitas')
            ->orderBy('tipe_asrama_id')
            ->orderBy(
                'lantai'
            )->orderBy('nomor_kamar', 'ASC')
            ->first();

        $data = [
            'judul' => 'Permintaan Kamar Asrama',
            'kamar' => $kamar
        ];

        return view('contents.dashboard_mahasiswa.tagihan.tambah', $data);
    }
    public function create()
    {
        $tanggal_masuk = request()->input('tanggal_masuk');
        $jumlah_bulan = (int) request()->input('jumlah_bulan');
        // $kamar = request()->input('kamar');
        $harga_asrama = request()->input('harga_asrama');
        // $file_bukti = request()->file('file_bukti');
        // $jumlah_uang = str_replace('.', '', request()->input('jumlah_uang'));

        // request()->merge([
        //     'jumlah_uang' => $jumlah_uang,
        // ]);

        $validator = Validator::make(request()->all(), [
            'tanggal_masuk' => ['required'],

            'jumlah_bulan' => ['required', 'integer', 'min:1'],

            // 'kamar' => ['required'],

            'harga_asrama' => ['required', 'exists:harga,id'],

            // 'tanggal_bayar' => ['required'],

            // 'jumlah_uang' => ['required', 'numeric', 'min:1'],

            // 'file_bukti' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ], [
            'tanggal_masuk.required' => 'Kolom tanggal masuk wajib diisi',

            'jumlah_bulan.required' => 'Jumlah bulan wajib diisi',
            'jumlah_bulan.integer' => 'Harus berupa angka',
            'jumlah_bulan.min' => 'Minimal 1 bulan',

            // 'kamar.required' => 'Kamar wajib dipilih',

            'harga_asrama.required' => 'Harga asrama wajib dipilih',
            'harga_asrama.exists' => 'Harga asrama tidak valid',

            // 'tanggal_bayar.required' => 'Kolom tanggal bayar wajib diisi',

            // 'jumlah_uang.required' => 'Jumlah uang wajib diisi',
            // 'jumlah_uang.numeric' => 'Jumlah uang harus berupa angka',
            // 'jumlah_uang.min' => 'Jumlah uang wajib diisi',

            // 'file_bukti.required' => 'File bukti wajib diunggah',
            // 'file_bukti.file' => 'File bukti tidak valid',
            // 'file_bukti.mimes' => 'File bukti hanya boleh berformat PDF, JPG, JPEG, atau PNG',
            // 'file_bukti.max' => 'Ukuran file maksimal 2 MB',

        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // $tanggal_bayar = request()->input('tanggal_bayar');
            // $tgl_bayar = Carbon::createFromFormat('d/m/Y H:i', $tanggal_bayar);
            // $metode_pembayaran = request()->input('metode_pembayaran');

            $data_penyewa = Penyewa::where('nim', auth()->user()->identifier)->first();
            // $kamar = Kamar::whereColumn('jumlah_penyewa', '<', 'kapasitas')
            //     ->orderBy('tipe_asrama_id')
            //     ->orderBy(
            //         'lantai'
            //     )->orderBy('nomor_kamar', 'ASC')
            //     ->first();

            // generate no request
            $tanggal = Carbon::now();
            $year  = $tanggal->format('y');
            $month = $tanggal->format('m');
            $day   = $tanggal->format('d');
            $lastrp = Requestpembayaran::whereDate('created_at', $tanggal->toDateString())
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();
            if ($lastrp) {
                $lastNumber = intval(substr($lastrp->no_request, -3));
                $newNumber  = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $newNumber = '001';
            }
            $no_request = $year . '' . $month . '' . $day . '-' . $newNumber;
            // end generate no request

            $data_harga_asrama = Harga::where('id', $harga_asrama)->first();

            // tanggal masuk
            $tgl_masuk = Carbon::createFromFormat('d/m/Y', $tanggal_masuk);
            $tgl_keluar = $tgl_masuk->copy()->addMonthsNoOverflow($jumlah_bulan);

            // asrama
            $harga_per_bulan_asrama = $data_harga_asrama->harga;
            $total_tagihan_asrama = $harga_per_bulan_asrama * $jumlah_bulan;

            $total_tagihan = $total_tagihan_asrama;
            // if ($jumlah_uang >= $total_tagihan) {
            //     $status = 'completed';
            // } else {
            //     $status = 'pending';
            // }

            $post = Requestpembayaran::create([
                'no_request' => $no_request,
                // 'tanggal_pembayaran' => $tgl_bayar,
                'tanggal_masuk' => $tgl_masuk,
                'tanggal_keluar' => $tgl_keluar,
                'durasi' => $jumlah_bulan,
                'penyewa_id' => $data_penyewa->id,
                'nama_bill_to' => $data_penyewa->nama_bill_to,
                // 'kamar_id' => $kamar->id,
                'total_tagihan' => $total_tagihan,
                // 'total_bayar' => $jumlah_uang,
                'status_pembayaran' => 'pending',
                'operator_id' => $data_penyewa->id
            ]);

            if ($post) {
                Requestpembayarandetail::create([
                    'no_request' => $no_request,
                    'harga_id' => $harga_asrama,
                    'jenissewa' => 'asrama',
                    'harga' => $harga_per_bulan_asrama,
                    'qty' => $jumlah_bulan,
                    'jumlah_pembayaran' => $total_tagihan_asrama,
                ]);

                // transaksi
                // $file_bukti = null;
                // if (request()->file('file_bukti')) {
                //     $file_bukti = 'file_bukti' . time() . '.' . request()->file('file_bukti')->getClientOriginalExtension();
                //     $file = request()->file('file_bukti');
                //     $tujuan_upload = $_SERVER['DOCUMENT_ROOT'] . '/img/bukti_pembayaran/request/' . $no_request;
                //     $file->move($tujuan_upload, $file_bukti);
                // }

                // // Generate no transaksi
                // $tahun = date('Y');
                // $bulan = date('m');
                // $tanggal = date('d');
                // $infoterakhir = Requesttransaksi::orderBy('created_at', 'DESC')->first();
                // if ($infoterakhir) {
                //     $tahunterakhir = Carbon::parse($infoterakhir->created_at)->format('Y') ?? 0;
                //     $bulanterakhir = Carbon::parse($infoterakhir->created_at)->format('m') ?? 0;
                //     $tanggalterakhir = Carbon::parse($infoterakhir->created_at)->format('d') ?? 0;
                //     $nomor = substr($infoterakhir->no_transaksi, 6);

                //     if ($tahun != $tahunterakhir || $bulan != $bulanterakhir || $tanggal != $tanggalterakhir) {
                //         $nomor = 0;
                //     }
                // } else {
                //     $nomor = 0;
                // }

                // // yymmddxxxxxx
                // $no_transaksi = sprintf('%02d%02d%02d%06d', date('y'), $bulan, $tanggal, $nomor + 1);

                // Requesttransaksi::create([
                //     'no_request' => $no_request,
                //     'no_transaksi' => $no_transaksi,
                //     'tanggal_transaksi' => $tgl_bayar,
                //     'jumlah_uang' => $jumlah_uang,
                //     'metode_pembayaran' => $metode_pembayaran,
                //     'file_bukti' => $file_bukti,
                //     'operator_id' => auth()->user()->id
                // ]);
            }

            DB::commit();
            return redirect()->route('dasbor')->with('messageSuccess', 'Permintaan kamar berhasil ditambahkan!');
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }
    }
}
