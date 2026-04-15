<?php

namespace App\Http\Controllers\Dashboard\Kamar;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\Lantai;
use App\Models\Tipeasrama;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function index()
    {
        $data = [
            'judul' => 'Kamar',
        ];

        return view('contents.dashboard.kamar.main', $data);
    }
    public function datatablekamar()
    {
        $kamar = Kamar::orderby('lantai_id', 'ASC')->get();

        $output = [];
        $no = 1;
        foreach ($kamar as $row) {

            $aksi = '
            <div class="d-flex align-items-center justify-content-center gap-1">
                <a href="' . route('kamar.edit', encrypt($row->id)) . '" class="btn btn-warning fw-bold d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Edit Kamar" style="width: 40px;">
                    <i class="fa fa-edit"></i>
                </a>
            </div>
            ';

            $output[] = [
                'aksi' => $aksi,
                'token_listrik' => $row->token_listrik,
                'type' => $row->type->nama ?? '',
                'lantai' => $row->lantai->nama,
                'nomor_kamar' => $row->nomor_kamar,
                'kapasitas' => $row->kapasitas,
                'jumlah_penyewa' => $row->jumlah_penyewa
            ];
        }

        return response()->json([
            'data' => $output
        ]);
    }
    public function tambah()
    {
        $data = [
            'judul' => 'Tambah Kamar',
        ];

        return view('contents.dashboard.kamar.tambah', $data);
    }
    public function create()
    {
        $validator = Validator::make(request()->all(), [
            // 'token_listrik' => ['required'],
            'tipeasrama' => [
                function ($attribute, $value, $fail) {
                    if (!Tipeasrama::where('id', (int)$value)->exists()) {
                        $fail('Kolom ini wajib dipilih');
                    }
                },
            ],
            'lantai' => [
                function ($attribute, $value, $fail) {
                    if (!Lantai::where('id', (int)$value)->exists()) {
                        $fail('Kolom ini wajib dipilih');
                    }
                },
            ],
            // 'nomor_kamar' => ['required', 'unique:kamar,nomor_kamar'],
            'nomor_kamar' => ['required'],
            'kapasitas' => ['required', 'integer'],
        ], [
            // 'token_listrik.required' => 'Kolom ini wajib diisi',
            'nomor_kamar.required' => 'Kolom ini wajib diisi',
            'nomor_kamar.unique' => 'Kolom sudah terdaftar',
            'kapasitas.required' => 'Kolom ini wajib diisi',
            'kapasitas.integer' => 'Kolom ini harus berupa angka',
        ]);


        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $token_listrik = request()->input('token_listrik');
            $tipeasrama = request()->input('tipeasrama');
            $lantai = request()->input('lantai');
            $nomor_kamar = request()->input('nomor_kamar');
            $kapasitas = request()->input('kapasitas');

            $post = Kamar::create([
                'token_listrik' => $token_listrik,
                'tipe_asrama_id' => $tipeasrama,
                'lantai_id' => $lantai,
                'nomor_kamar' => $nomor_kamar,
                'kapasitas' => $kapasitas,
                'operator_id' => auth()->user()->id
            ]);

            if ($post) {
                DB::commit();
                return redirect()->route('kamar')->with('messageSuccess', 'Kamar berhasil ditambahkan!');
            }
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }
    }
    public function edit($id)
    {
        $id = decrypt($id);

        $kamar = Kamar::findorfail($id);

        $data = [
            'judul' => 'Edit kamar',
            'datakamar' => $kamar
        ];

        return view('contents.dashboard.kamar.edit', $data);
    }
    public function update($id)
    {
        $id = decrypt($id);
        $kamar = Kamar::findorfail($id);

        // if ($kamar->nomor_kamar == request()->input('nomor_kamar')) {
        //     $rule_nomor_kamar = ['required'];
        // } else {
        //     $rule_nomor_kamar = ['required', 'unique:kamar,nomor_kamar'];
        // }

        $validator = Validator::make(request()->all(), [
            // 'token_listrik' => ['required'],
            'tipeasrama' => [
                function ($attribute, $value, $fail) {
                    if (!Tipeasrama::where('id', (int)$value)->exists()) {
                        $fail('Kolom ini wajib dipilih');
                    }
                },
            ],
            'lantai' => [
                function ($attribute, $value, $fail) {
                    if (!Lantai::where('id', (int)$value)->exists()) {
                        $fail('Kolom ini wajib dipilih');
                    }
                },
            ],
            'nomor_kamar' => ['required'],
            // 'nomor_kamar' => $rule_nomor_kamar,
            'kapasitas' => ['required', 'integer'],
        ], [
            // 'token_listrik.required' => 'Kolom ini wajib diisi',
            'nomor_kamar.required' => 'Kolom ini wajib diisi',
            'nomor_kamar.unique' => 'Kolom sudah terdaftar',
            'kapasitas.required' => 'Kolom ini wajib diisi',
            'kapasitas.integer' => 'Kolom ini harus berupa angka',
        ]);


        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $token_listrik = request()->input('token_listrik');
            $tipeasrama = request()->input('tipeasrama');
            $lantai = request()->input('lantai');
            $nomor_kamar = request()->input('nomor_kamar');
            $kapasitas = request()->input('kapasitas');

            Kamar::where('id', $id)->update([
                'token_listrik' => $token_listrik,
                'tipe_asrama_id' => $tipeasrama,
                'lantai_id' => $lantai,
                'nomor_kamar' => $nomor_kamar,
                'kapasitas' => $kapasitas,
                'operator_id' => auth()->user()->id
            ]);

            DB::commit();
            return redirect()->route('kamar')->with('messageSuccess', 'Kamar berhasil diperbarui!');
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }
    }
}
