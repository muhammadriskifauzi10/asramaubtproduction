<?php

namespace App\Http\Controllers\Dashboard\Pengguna;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function index()
    {
        $data = [
            'judul' => 'Pengguna',
        ];

        return view('contents.dashboard.pengguna.main', $data);
    }
    public function datatablepengguna()
    {
        $pengguna = User::orderby('id', 'ASC')->get();

        $output = [];
        $no = 1;
        foreach ($pengguna as $row) {

            $aksi = '
            <div class="d-flex align-items-center justify-content-center gap-1">
                <button type="button" class="btn btn-warning fw-bold d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" title="Edit Role" style="width: 40px;" onclick="openModalRole(' . $row->id . ', ' . $row->role_id . ')">
                    <i class="fa-solid fa-user-shield"></i>
                </button>
            </div>
            ';

            if ($row->type == 1) {
                $type = 'Dosen';
            } else if ($row->type == 2) {
                $type = 'Staff';
            } else if ($row->type == 3) {
                $type = 'Mahasiswa/i';
            } else {
                $type = '';
            }

            $status = '
            <div class="d-flex align-items-center justify-content-center">
                <select class="form-select form-select-sm" onchange="onStatus(this, ' . $row->id . ')" style="width: 200px;">
                    <option value="0" ' . ($row->status == 0 ? 'selected' : '') . '>TIDAK AKTIF</option>
                    <option value="1" ' . ($row->status == 1 ? 'selected' : '') . '>AKTIF</option>
                </select>
            </div>
            ';

            $output[] = [
                'aksi' => $aksi,
                'role' => $row->role->nama ?? '',
                'namalengkap' => $row->name,
                'identifier' => $row->identifier,
                'email' => $row->email,
                'type' => $type,
                'status' => $status,
            ];
        }

        return response()->json([
            'data' => $output
        ]);
    }
    public function editrole()
    {
        if (request()->ajax()) {
            $validator = Validator::make(request()->all(), [
                'role_id' => [
                    function ($attribute, $value, $fail) {
                        if (!Role::where('id', (int)$value)->exists()) {
                            $fail('Kolom ini wajib dipilih');
                        }
                    },
                ],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'errors' => $validator->errors()
                ], 422);
            }

            try {
                DB::beginTransaction();

                $user_id = request()->input('user_id');
                $role_id = request()->input('role_id');

                User::where('id', $user_id)->update([
                    'role_id' => $role_id,
                ]);

                DB::commit();
                return response()->json([
                    'status' => 200,
                    'message' => 'Role berhasil diperbarui!',
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
    public function status()
    {
        if (request()->ajax()) {
            try {
                DB::beginTransaction();

                $user_id = request()->input('user_id');
                $status = request()->input('status');

                User::where('id', $user_id)->update([
                    'status' => $status,
                ]);

                DB::commit();
                return response()->json([
                    'status' => 200,
                    'message' => 'Status berhasil diperbarui!',
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
