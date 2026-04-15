<?php

namespace App\Http\Controllers\Dashboard\Harga;

use App\Http\Controllers\Controller;
use App\Models\Harga;
use App\Models\Tagih;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function index()
    {
        $data = [
            'judul' => 'Harga',
        ];

        return view('contents.dashboard.harga.main', $data);
    }
    public function datatableharga()
    {
        $harga = Harga::orderby('id', 'DESC')->get();

        $output = [];
        $no = 1;
        foreach ($harga as $row) {

            $aksi = '
            <div class="d-flex align-items-center justify-content-center gap-1">
                <a href="' . route('harga.edit', encrypt($row->id)) . '" class="btn btn-warning fw-bold d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Edit Harga" style="width: 40px;">
                    <i class="fa fa-edit"></i>
                </a>
            </div>
            ';

            $output[] = [
                'aksi' => $aksi,
                'tagihan' => $row->tagihan->nama,
                'nama_tagihan' => $row->nama_tagihan,
                'harga' => 'Rp ' . number_format($row->harga, 2, ',', '.'),
                'operator' => $row->user->name
            ];
        }

        return response()->json([
            'data' => $output
        ]);
    }
    public function tambah()
    {
        $data = [
            'judul' => 'Tambah Harga',
        ];

        return view('contents.dashboard.harga.tambah', $data);
    }
    public function create()
    {
        $validator = Validator::make(request()->all(), [
            'tagih' => [
                function ($attribute, $value, $fail) {
                    if (!Tagih::where('id', (int)$value)->exists()) {
                        $fail('Kolom ini wajib dipilih');
                    }
                },
            ],
            'nama_tagihan' => ['required'],
        ], [
            'nama_tagihan.required' => 'Kolom ini wajib diisi',
        ]);


        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $tagih = request()->input('tagih');
            $nama_tagihan = request()->input('nama_tagihan');
            $harga = request()->input('harga');

            $post = Harga::create([
                'tagih_id' => $tagih,
                'nama_tagihan' => $nama_tagihan,
                'harga' => $harga ? str_replace('.', '', $harga) : 0,
                'operator_id' => auth()->user()->id
            ]);

            if ($post) {
                DB::commit();
                return redirect()->route('harga')->with('messageSuccess', 'Harga berhasil ditambahkan!');
            }
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }
    }
    public function edit($id)
    {
        $id = decrypt($id);

        $harga = Harga::findorfail($id);

        $data = [
            'judul' => 'Edit Harga',
            'dataharga' => $harga
        ];

        return view('contents.dashboard.harga.edit', $data);
    }
    public function update($id)
    {
        $id = decrypt($id);

        $validator = Validator::make(request()->all(), [
            'tagih' => [
                function ($attribute, $value, $fail) {
                    if (!Tagih::where('id', (int)$value)->exists()) {
                        $fail('Kolom ini wajib dipilih');
                    }
                },
            ],
            'nama_tagihan' => ['required'],
        ], [
            'nama_tagihan.required' => 'Kolom ini wajib diisi',
        ]);


        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $tagih = request()->input('tagih');
            $nama_tagihan = request()->input('nama_tagihan');
            $harga = request()->input('harga');

            $post = Harga::where('id', $id)->update([
                'tagih_id' => $tagih,
                'nama_tagihan' => $nama_tagihan,
                'harga' => $harga ? str_replace('.', '', $harga) : 0,
                'operator_id' => auth()->user()->id
            ]);

            if ($post) {
                DB::commit();
                return redirect()->route('harga')->with('messageSuccess', 'Harga berhasil diperbarui!');
            }
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }
    }
}
