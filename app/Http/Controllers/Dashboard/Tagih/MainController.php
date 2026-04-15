<?php

namespace App\Http\Controllers\Dashboard\Tagih;

use App\Http\Controllers\Controller;
use App\Models\Tagih;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function index()
    {
        $data = [
            'judul' => 'Tagih',
        ];

        return view('contents.dashboard.tagih.main', $data);
    }
    public function datatabletagih()
    {
        $tagih = Tagih::orderby('id', 'DESC')->get();

        $output = [];
        $no = 1;
        foreach ($tagih as $row) {

            $aksi = '
            <div class="d-flex align-items-center justify-content-center gap-1">
                <a href="' . route('tagih.edit', encrypt($row->id)) . '" class="btn btn-warning fw-bold d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Edit Tagih" style="width: 40px;">
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
            'judul' => 'Tambah Tagih',
        ];

        return view('contents.dashboard.tagih.tambah', $data);
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

            $post = Tagih::create([
                'nama' => $nama,
            ]);

            if ($post) {
                DB::commit();
                return redirect()->route('tagih')->with('messageSuccess', 'Tagih berhasil ditambahkan!');
            }
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }
    }
    public function edit($id)
    {
        $id = decrypt($id);

        $tagih = Tagih::findorfail($id);

        $data = [
            'judul' => 'Edit Tagih',
            'datatagih' => $tagih
        ];

        return view('contents.dashboard.tagih.edit', $data);
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

            Tagih::where('id', $id)->update([
                'nama' => $nama,
            ]);

            DB::commit();
            return redirect()->route('tagih')->with('messageSuccess', 'Tagih berhasil diperbarui!');
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }
    }
}
