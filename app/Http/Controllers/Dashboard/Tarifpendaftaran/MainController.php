<?php

namespace App\Http\Controllers\Dashboard\Tarifpendaftaran;

use App\Http\Controllers\Controller;
use App\Models\Tarifpendaftaran;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function index()
    {
        $data = [
            'judul' => 'Tarif Pendaftaran',
        ];

        return view('contents.dashboard.tarifpendaftaran.main', $data);
    }
    public function datatabletarifpendaftaran()
    {
        $tarifpendaftaran = Tarifpendaftaran::orderby('id', 'DESC')->get();

        $output = [];
        $no = 1;
        foreach ($tarifpendaftaran as $row) {

            if ($row->status == 1) {
                $deskripsi = 'Apakah Anda benar-benar ingin nonaktifkan tarif?';

                $btn1 = '<button type="button" class="btn btn-danger fw-bold d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Nonaktifkan Tarif" style="width: 40px;" onclick="requestToggle(' . $row->id . ', \'' . $deskripsi . '\', 0)">
                <i class="fa fa-toggle-on"></i>
                </button>';
            } else {
                $deskripsi = 'Apakah Anda benar-benar ingin aktifkan tarif?';

                $btn1 = '<button type="button" class="btn btn-success fw-bold d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Aktifkan Tarif" style="width: 40px;" onclick="requestToggle(' . $row->id . ', \'' . $deskripsi . '\', 1)">
                    <i class="fa fa-toggle-off"></i>
                </button>';
            }

            $aksi = '
            <div class="d-flex align-items-center justify-content-center gap-1">
                ' . $btn1 . '
            </div>
            ';

            $output[] = [
                'aksi' => $aksi,
                'nama' => $row->nama,
                'jenis' => $row->jenis,
                'kategori' => $row->kategori,
                'nominal' => 'RP. ' . number_format($row->nominal, '0', '.', '.'),
                'status_tarif' => $row->status == 1 ? 'AKTIF' : '-',
            ];
        }

        return response()->json([
            'data' => $output
        ]);
    }
    public function tambah()
    {
        $data = [
            'judul' => 'Tambah Tarif Pendaftaran',
        ];

        return view('contents.dashboard.tarifpendaftaran.tambah', $data);
    }
    public function create()
    {
        $validator = Validator::make(request()->all(), [
            'nama' => ['required', 'string'],
            'jenis' => ['required', 'in:kip,nonkip'],
            'kategori' => ['required', 'in:Deposit,Perlengkapan'],
            'nominal' => ['required', 'numeric', 'min:0'],
        ], [
            'nama.required' => 'Nama tarif wajib diisi.',
            'nama.string' => 'Nama tarif harus berupa teks.',

            'jenis.required' => 'Jenis wajib dipilih.',
            'jenis.in' => 'Jenis yang dipilih tidak valid.',

            'kategori.required' => 'Kategori wajib dipilih.',
            'kategori.in' => 'Kategori yang dipilih tidak valid.',

            'nominal.required' => 'Nominal wajib diisi.',
            'nominal.numeric' => 'Nominal harus berupa angka.',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $nama = request()->input('nama');
            $jenis = request()->input('jenis');
            $kategori = request()->input('kategori');
            $nominal = request()->input('nominal');

            $post = Tarifpendaftaran::create([
                'nama' => $nama,
                'jenis' => $jenis,
                'kategori' => $kategori,
                'nominal' => $nominal ? str_replace('.', '', $nominal) : 0,
                'operator_id' => auth()->user()->id,
            ]);
            if ($post) {
                DB::commit();
                return redirect()->route('tarifpendaftaran')->with('messageSuccess', 'Tarif pendaftaran berhasil ditambahkan!');
            }
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }
    }
    public function toggle()
    {
        if (request()->all()) {
            $id = request()->input('id');
            $status = request()->input('status');
            try {
                DB::beginTransaction();

                Tarifpendaftaran::where('id', $id)->update([
                    'status' => $status
                ]);

                if ($status == 1) {
                    $message = 'Tarif berhasil diaktifkan!';
                } else {
                    $message = 'Tarif berhasil dinonaktifkan!';
                }

                DB::commit();
                return response()->json([
                    'status'  => 200,
                    'message'  => $message,
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
