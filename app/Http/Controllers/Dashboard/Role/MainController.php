<?php

namespace App\Http\Controllers\Dashboard\Role;

use App\Http\Controllers\Controller;
use App\Models\Role;

class MainController extends Controller
{
    public function index()
    {
        $data = [
            'judul' => 'Role',
        ];

        return view('contents.dashboard.role.main', $data);
    }
    public function datatablerole()
    {
        $role = Role::orderby('id', 'ASC')->get();

        $output = [];
        $no = 1;
        foreach ($role as $row) {
            $output[] = [
                'nomor' => '<strong>' . $no++ . '</strong>',
                'nama' => $row->nama,
            ];
        }

        return response()->json([
            'data' => $output
        ]);
    }
}
