<?php

namespace App\Http\Controllers\Dashboard\Lantai;

use App\Http\Controllers\Controller;
use App\Models\Lantai;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function index()
    {
        $data = [
            'judul' => 'Lantai',
        ];

        return view('contents.dashboard.lantai.main', $data);
    }
    public function datatablelantai()
    {
        $lantai = Lantai::orderby('id', 'ASC')->get();

        $output = [];
        $no = 1;
        foreach ($lantai as $row) {

            $aksi = '
            <div class="d-flex align-items-center justify-content-center gap-1">
                <a href="' . route('lantai.edit', encrypt($row->id)) . '" class="btn btn-warning fw-bold d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Edit Lantai" style="width: 40px;">
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
            'judul' => 'Tambah Lantai',
        ];

        return view('contents.dashboard.lantai.tambah', $data);
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

            $post = Lantai::create([
                'nama' => $nama,
            ]);

            if ($post) {
                DB::commit();
                return redirect()->route('lantai')->with('messageSuccess', 'Lantai berhasil ditambahkan!');
            }
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }
    }
    public function edit($id)
    {
        $id = decrypt($id);

        $lantai = Lantai::findorfail($id);

        $data = [
            'judul' => 'Edit Lantai',
            'datalantai' => $lantai
        ];

        return view('contents.dashboard.lantai.edit', $data);
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

            Lantai::where('id', $id)->update([
                'nama' => $nama,
            ]);

            DB::commit();
            return redirect()->route('lantai')->with('messageSuccess', 'Lantai berhasil diperbarui!');
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }
    }
}
