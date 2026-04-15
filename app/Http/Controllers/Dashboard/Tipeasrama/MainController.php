<?php

namespace App\Http\Controllers\Dashboard\Tipeasrama;

use App\Http\Controllers\Controller;
use App\Models\Tipeasrama;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function index()
    {
        $data = [
            'judul' => 'Tipe Asrama',
        ];

        return view('contents.dashboard.tipeasrama.main', $data);
    }
    public function datatabletipeasrama()
    {
        $tipeasrama = Tipeasrama::orderby('id', 'ASC')->get();

        $output = [];
        $no = 1;
        foreach ($tipeasrama as $row) {

            $aksi = '
            <div class="d-flex align-items-center justify-content-center gap-1">
                <a href="' . route('tipeasrama.edit', encrypt($row->id)) . '" class="btn btn-warning fw-bold d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Edit Tipe Asrama" style="width: 40px;">
                    <i class="fa fa-edit"></i>
                </a>
            </div>
            ';

            $output[] = [
                'aksi' => $aksi,
                'nama' => $row->nama,
            ];
        }

        return response()->json([
            'data' => $output
        ]);
    }
    public function tambah()
    {
        $data = [
            'judul' => 'Tambah Tipe Asrama',
        ];

        return view('contents.dashboard.tipeasrama.tambah', $data);
    }
    public function create()
    {
        $validator = Validator::make(request()->all(), [
            'nama' => ['required'],
        ], [
            'nama.required' => 'Kolom ini wajib diisi',
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

            $post = Tipeasrama::create([
                'nama' => $nama,
            ]);

            if ($post) {
                DB::commit();
                return redirect()->route('tipeasrama')->with('messageSuccess', 'Tipe asrama berhasil ditambahkan!');
            }
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }
    }
    public function edit($id)
    {
        $id = decrypt($id);

        $tipeasrama = Tipeasrama::findorfail($id);

        $data = [
            'judul' => 'Edit Tipe Asrama',
            'datatipeasrama' => $tipeasrama
        ];

        return view('contents.dashboard.tipeasrama.edit', $data);
    }
    public function update($id)
    {
        $id = decrypt($id);

        $validator = Validator::make(request()->all(), [
            'nama' => ['required'],
        ], [
            'nama.required' => 'Kolom ini wajib diisi',
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

            Tipeasrama::where('id', $id)->update([
                'nama' => $nama,
            ]);

            DB::commit();
            return redirect()->route('tipeasrama')->with('messageSuccess', 'Tipe asrama berhasil diperbarui!');
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }
    }
}
