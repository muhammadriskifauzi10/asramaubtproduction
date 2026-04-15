<?php

namespace App\Http\Controllers\Dashboard\Penyewa;

use App\Http\Controllers\Controller;
use App\Models\Kabupaten;
use App\Models\Linkapi;
use App\Models\Mahasiswa;
use App\Models\Penyewa;
use App\Models\Provinsi;
use App\Models\Kamar;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function index()
    {
        $data = [
            'judul' => 'Penyewa',
        ];

        return view('contents.dashboard.penyewa.main', $data);
    }
    public function datatablepenyewa()
    {
        $asrama = request()->input('asrama');

        $penyewa = Penyewa::when($asrama || $asrama != 0, function ($query) use ($asrama) {
            $query->where('status_asrama', $asrama);
        })->orderby('angkatan', 'DESC')->get();

        $output = [];
        $no = 1;
        foreach ($penyewa as $row) {

            // asrama
            if($row->status_asrama == 1) {
                $btn1 = '<button type="button" class="btn btn-warning fw-bold d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Hentikan Asrama" style="width: 40px;" onclick="onHentikanAsrama(' . $row->id . ')">
                    <i class="fa fa-door-open"></i>
                </button>';
            }
            else {
                $btn1 = '';
            }

            // catering
            if($row->status_catering == 1) {
                $btn2 = '<button type="button" class="btn btn-warning fw-bold d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Hentikan Catering" style="width: 40px;" onclick="onHentikanCatering(' . $row->id . ')">
                    <i class="fa fa-utensils"></i>
                </button>';
            }
            else {
                $btn2 = '';
            }

            $aksi = '
            <div class="d-flex align-items-center justify-content-center gap-1">
                ' . $btn1 . '
                ' . $btn2 . '
            </div>
            ';

            $output[] = [
                'aksi' => $aksi,
                'angkatan' => $row->angkatan,
                'namalengkap' => $row->namalengkap,
                'nim' => $row->nim,
                'kip' => $row->nama_bill_to,
                'noktp' => $row->noktp,
                'nohp' => $row->nohp,
                'email' => $row->email,
                'jenis_kelamin' => $row->jenis_kelamin,
                'alamat' => $row->alamat ? nl2br($row->alamat) : '',
                'status_asrama' => $row->status_asrama == 1 ? 'AKTIF' : '-',
                'status_catering' => $row->status_catering == 1 ? 'AKTIF' : '-',
            ];
        }

        return response()->json([
            'data' => $output
        ]);
    }
    public function singkron()
    {
        if (request()->ajax()) {
            $id = request()->input('api');

            $api = Linkapi::findorfail($id);

            $response = Http::get($api->link);

            if (!$response->successful()) {
                return response()->json([
                    'status' => 500,
                    'message' => 'Gagal ambil data dari API!',
                    'icon' => 'error'
                ]);
            }

            $datapenyewa = $response->json();
            if (isset($datapenyewa['success']) && $datapenyewa['success'] === true) {
                $penyewa = $datapenyewa['data'];

                $inserted = 0;
                $apiUsers = Mahasiswa::getAllMahasiswa()->keyBy('identifier');
                $provinsiList = Provinsi::pluck('name', 'id');
                $kabupatenList = Kabupaten::pluck('name', 'id');
                foreach ($penyewa as $row) {
                    $user = $apiUsers[$row['nim']] ?? null;

                    if (!$user) continue;

                    $street = $user['profile']['street'] ?? '';

                    $provinsi = $provinsiList[$user['profile']['province'] ?? null] ?? '';
                    $kabupaten = $kabupatenList[$user['profile']['city'] ?? null] ?? '';

                    $address = $street . ', ' . $provinsi . ', ' . $kabupaten;

                    Penyewa::updateOrCreate(
                        ['nim' => $user['identifier']],
                        [
                            'angkatan' => $row['angkatan'],
                            'namalengkap' => $user['name'],
                            'noktp' => $user['nik'],
                            'nohp' => $user['phone'],
                            'kip' => $api->kategori,
                            'email' => $user['email'],
                            'jenis_kelamin' => $user['profile']['gender'] ?? null,
                            'alamat' => $address,
                            'nama_bill_to' => $api->nama,
                            'operator_id' => auth()->id(),
                        ]
                    );

                    $inserted++;
                }

                if ($inserted == 0) {
                    return response()->json([
                        'status' => 400,
                        'message' => "Tidak ada data yang disinkronisasikan!",
                        'icon' => 'info'
                    ]);
                }

                return response()->json([
                    'status' => 200,
                    'message' => "Berhasil sinkronisasi $inserted data!",
                    'icon' => 'success'
                ]);
            }

            return response()->json([
                'status' => 500,
                'message' => 'Response API tidak valid!',
                'icon' => 'error'
            ]);
        }
    }
    public function hentikanasrama()
    {
        if (request()->all()) {
            $id = request()->input('id');

            try {
                DB::beginTransaction();

                $pembayaran = Pembayaran::where('penyewa_id', $id)->latest()->first();

                Penyewa::where('id', $id)->update([
                    'status_asrama' => 0
                ]);

                Pembayaran::where('penyewa_id', $id)->update([
                     'status_asrama' => 0
                ]);

                Kamar::where('id', $pembayaran->kamar_id)->decrement('jumlah_penyewa');

                DB::commit();
                return response()->json([
                    'status'  => 200,
                    'message'  => 'Berhasil hentikan asrama!',
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
    public function hentikancatering()
    {
        if (request()->all()) {
            $id = request()->input('id');

            try {
                DB::beginTransaction();

                Penyewa::where('id', $id)->update([
                    'status_catering' => 0
                ]);

                Pembayaran::where('penyewa_id', $id)->update([
                     'status_catering' => 0
                ]);

                DB::commit();
                return response()->json([
                    'status'  => 200,
                    'message'  => 'Berhasil hentikan catering!',
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
