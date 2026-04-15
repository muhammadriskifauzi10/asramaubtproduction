<?php

namespace App\Http\Controllers\Dashboard\Tipecatering;

use App\Http\Controllers\Controller;
use App\Models\Harga;
use App\Models\Tipecatering;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function index()
    {
        $data = [
            'judul' => 'Tipe Catering',
        ];

        return view('contents.dashboard.tipecatering.main', $data);
    }
    public function datatabletipecatering()
    {
        $tipecatering = Tipecatering::orderby('id', 'DESC')->get();

        $output = [];
        $no = 1;
        foreach ($tipecatering as $row) {

            $aksi = '
            <div class="d-flex align-items-center justify-content-center gap-1">
                <a href="' . route('tipecatering.edit', encrypt($row->id)) . '" class="btn btn-warning fw-bold d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Edit Tipe Catering" style="width: 40px;">
                    <i class="fa fa-edit"></i>
                </a>
            </div>
            ';

            $output[] = [
                'aksi' => $aksi,
                'jenis_tagihan' => $row->harga->nama_tagihan,
                'nama' => $row->nama,
                'jumlah_porsi' => $row->jumlah_porsi,
                'pagi' => $row->pagi == 'Y' ? '✔' : 'X',
                'siang' => $row->siang == 'Y' ? '✔' : 'X',
                'malam' => $row->malam == 'Y' ? '✔' : 'X'
            ];
        }

        return response()->json([
            'data' => $output
        ]);
    }
    public function tambah()
    {
        $data = [
            'judul' => 'Tambah Tipe Catering',
        ];

        return view('contents.dashboard.tipecatering.tambah', $data);
    }
    public function create()
    {
        $validator = Validator::make(request()->all(), [
            'jenis_tagih' => [
                function ($attribute, $value, $fail) {
                    if (!Harga::where('id', (int)$value)->exists()) {
                        $fail('Kolom ini wajib dipilih');
                    }
                },
            ],
            'tipe_catering' => ['required'],
            'jumlah_porsi' => ['required', 'integer'],
        ], [
            'tipe_catering.required' => 'Kolom ini wajib diisi',
            'jumlah_porsi.required' => 'Kolom ini wajib diisi',
            'jumlah_porsi.integer' => 'Kolom ini harus berupa angka',
        ]);


        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $jenis_tagih = request()->input('jenis_tagih');
            $tipe_catering = request()->input('tipe_catering');
            $jumlah_porsi = request()->input('jumlah_porsi');
            $pagi = request()->input('pagi');
            $siang = request()->input('siang');
            $malam = request()->input('malam');

            $post = Tipecatering::create([
                'harga_id' => $jenis_tagih,
                'nama' => $tipe_catering,
                'jumlah_porsi' => $jumlah_porsi,
                'pagi' => $pagi,
                'siang' => $siang,
                'malam' => $malam,
                'operator_id' => auth()->user()->id
            ]);

            if ($post) {
                DB::commit();
                return redirect()->route('tipecatering')->with('messageSuccess', 'Tipe catering berhasil ditambahkan!');
            }
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }
    }
    public function edit($id)
    {
        $id = decrypt($id);

        $tipecatering = Tipecatering::findorfail($id);

        $data = [
            'judul' => 'Edit Tipe Catering',
            'datatipecatering' => $tipecatering
        ];

        return view('contents.dashboard.tipecatering.edit', $data);
    }
    public function update($id)
    {
        $id = decrypt($id);

        $validator = Validator::make(request()->all(), [
            'jenis_tagih' => [
                function ($attribute, $value, $fail) {
                    if (!Harga::where('id', (int)$value)->exists()) {
                        $fail('Kolom ini wajib dipilih');
                    }
                },
            ],
            'tipe_catering' => ['required'],
            'jumlah_porsi' => ['required', 'integer'],
        ], [
            'tipe_catering.required' => 'Kolom ini wajib diisi',
            'jumlah_porsi.required' => 'Kolom ini wajib diisi',
            'jumlah_porsi.integer' => 'Kolom ini harus berupa angka',
        ]);


        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $jenis_tagih = request()->input('jenis_tagih');
            $tipe_catering = request()->input('tipe_catering');
            $jumlah_porsi = request()->input('jumlah_porsi');
            $pagi = request()->input('pagi');
            $siang = request()->input('siang');
            $malam = request()->input('malam');

            Tipecatering::where('id', $id)->update([
                'harga_id' => $jenis_tagih,
                'nama' => $tipe_catering,
                'jumlah_porsi' => $jumlah_porsi,
                'pagi' => $pagi,
                'siang' => $siang,
                'malam' => $malam,
                'operator_id' => auth()->user()->id
            ]);

            DB::commit();
            return redirect()->route('tipecatering')->with('messageSuccess', 'Tipe catering berhasil diperbarui!');
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }
    }
}
