<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dashboard\Harga\MainController as HargaMainController;
use App\Http\Controllers\Dashboard\MainController;
use App\Http\Controllers\Dashboard\Kamar\MainController as KamarMainController;
use App\Http\Controllers\Dashboard\Lantai\MainController as LantaiMainController;
use App\Http\Controllers\Dashboard\Omset\MainController as OmsetMainController;
use App\Http\Controllers\Dashboard\Pengguna\MainController as PenggunaMainController;
use App\Http\Controllers\Dashboard\Penyewa\MainController as PenyewaMainController;
use App\Http\Controllers\Dashboard\Perpanjang\MainController as PerpanjangMainController;
use App\Http\Controllers\Dashboard\Piutang\MainController as PiutangMainController;
use App\Http\Controllers\Dashboard\Transaksi\MainController as TransaksiMainController;
use App\Http\Controllers\Dashboard\Role\MainController as RoleMainController;
use App\Http\Controllers\Dashboard\Tagih\MainController as TagihMainController;
use App\Http\Controllers\Dashboard\Tagihan\MainController as TagihanMainController;
use App\Http\Controllers\Dashboard\Tipeasrama\MainController as TipeasramaMainController;
use App\Http\Controllers\Dashboard\Tipecatering\MainController as TipecateringMainController;
use App\Models\Harga;
use App\Models\Pembayaran;
use App\Models\Penyewa;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

// GUEST
Route::middleware(['check-access'])->group(function () {
    Route::view('/', 'layouts.landing')->name('landing');
});

// AUTH
Route::middleware(['sso'])->group(function () {
    // Route::get('/tes', function () {
    //     $today = Carbon::today();

    //     $penyewaList = Penyewa::all();

    //     foreach ($penyewaList as $p) {

    //         if ($p->kip == 'nonkip') {
    //             $harga_id = 3;

    //             $harga = Harga::find($harga_id);
    //         } else {
    //             $harga_id = 4;

    //             $harga = Harga::find($harga_id);
    //         }

    //         Pembayaran::create([
    //             'harga_id' => $harga_id,
    //             'tanggal_pembayaran' => null,
    //             'tanggal_masuk' => $today,
    //             'tanggal_keluar' => $today->copy()->addMonth(),
    //             'penyewa_id' => $p->id,
    //             'kamar_id' => null,
    //             'jenissewa' => 'Catering',
    //             'jumlah_pembayaran' => intval($harga->harga),
    //             'potongan_harga' => 0,
    //             'total_bayar' => 0,
    //             'status_pembayaran' => 'pending',
    //             'status' => 1,
    //             'operator_id' => auth()->user()->id,
    //         ]);
    //     }
    // });


    Route::get('/login-check', [LoginController::class, 'index'])->name('login-check');

    Route::get('/dasbor', [MainController::class, 'index'])->name('dasbor');
    Route::post('/user/logout', [MainController::class, 'logout'])->name('logout');

    // tagihan
    Route::get('/tagihan', [TagihanMainController::class, 'index'])->name('tagihan');
    Route::post('/tagihan/datatabletagihan', [TagihanMainController::class, 'datatabletagihan'])->name('tagihan.datatabletagihan');
    Route::get('/tagihan/detail/{no_invoice}', [TagihanMainController::class, 'detail'])->name('tagihan.detail');
    Route::post('/tagihan/bayar', [TagihanMainController::class, 'bayar'])->name('tagihan.bayar');
    Route::post('/tagihan/cancelitem', [TagihanMainController::class, 'cancelitem'])->name('tagihan.cancelitem');
    Route::post('/tagihan/potongan_harga', [TagihanMainController::class, 'potongan_harga'])->name('tagihan.potongan_harga');
    Route::get('/tagihan/tambah', [TagihanMainController::class, 'tambah'])->name('tagihan.tambah');
    Route::post('/tagihan/tambah', [TagihanMainController::class, 'create'])->name('tagihan.posttagihan');
    Route::get('/tagihan/tambah/catering', [TagihanMainController::class, 'tambahcatering'])->name('tagihan.tambah.catering');
    Route::post('/tagihan/tambah/catering', [TagihanMainController::class, 'postcatering'])->name('tagihan.tambah.postcatering');
    Route::get('/tagihan/invoice/{no_invoice}', [TagihanMainController::class, 'invoice'])->name('tagihan.invoice');

    // perpanjang
    Route::get('/perpanjang', [PerpanjangMainController::class, 'index'])->name('perpanjang');
    Route::post('/perpanjang/datatableperpanjang', [PerpanjangMainController::class, 'datatableperpanjang'])->name('perpanjang.datatableperpanjang');
    Route::post('/perpanjang/perpanjangmassal', [PerpanjangMainController::class, 'perpanjangmassal'])->name('perpanjang.perpanjangmassal');

    // transaksi
    Route::get('/transaksi', [TransaksiMainController::class, 'index'])->name('transaksi');
    Route::post('/transaksi/datatabletransaksi', [TransaksiMainController::class, 'datatabletransaksi'])->name('transaksi.datatabletransaksi');
    Route::get('/transaksi/kwitansi/{no_invoice}', [TransaksiMainController::class, 'kwitansi'])->name('transaksi.kwitansi');

    // omset
    Route::get('/omset', [OmsetMainController::class, 'index'])->name('omset');
    Route::post('/omset/datatableomset', [OmsetMainController::class, 'datatableomset'])->name('omset.datatableomset');

    // piutang
    Route::get('/piutang', [PiutangMainController::class, 'index'])->name('piutang');
    Route::post('/piutang/datatablepiutang', [PiutangMainController::class, 'datatablepiutang'])->name('piutang.datatablepiutang');

    // lantai
    Route::get('/lantai', [LantaiMainController::class, 'index'])->name('lantai');
    Route::post('/lantai/datatablelantai', [LantaiMainController::class, 'datatablelantai'])->name('lantai.datatablelantai');
    Route::get('/lantai/tambah', [LantaiMainController::class, 'tambah'])->name('lantai.tambah');
    Route::post('/lantai/tambah', [LantaiMainController::class, 'create'])->name('lantai.post');
    Route::get('/lantai/edit/{id}', [LantaiMainController::class, 'edit'])->name('lantai.edit');
    Route::put('/lantai/edit/{id}', [LantaiMainController::class, 'update'])->name('lantai.update');

    // tipeasrama
    Route::get('/tipeasrama', [TipeasramaMainController::class, 'index'])->name('tipeasrama');
    Route::post('/tipeasrama/datatabletipeasrama', [TipeasramaMainController::class, 'datatabletipeasrama'])->name('tipeasrama.datatabletipeasrama');
    Route::get('/tipeasrama/tambah', [TipeasramaMainController::class, 'tambah'])->name('tipeasrama.tambah');
    Route::post('/tipeasrama/tambah', [TipeasramaMainController::class, 'create'])->name('tipeasrama.post');
    Route::get('/tipeasrama/edit/{id}', [TipeasramaMainController::class, 'edit'])->name('tipeasrama.edit');
    Route::put('/tipeasrama/edit/{id}', [TipeasramaMainController::class, 'update'])->name('tipeasrama.update');

    // kamar
    Route::get('/kamar', [KamarMainController::class, 'index'])->name('kamar');
    Route::post('/kamar/datatablekamar', [KamarMainController::class, 'datatablekamar'])->name('kamar.datatablekamar');
    Route::get('/kamar/tambah', [KamarMainController::class, 'tambah'])->name('kamar.tambah');
    Route::post('/kamar/tambah', [KamarMainController::class, 'create'])->name('kamar.post');
    Route::get('/kamar/edit/{id}', [KamarMainController::class, 'edit'])->name('kamar.edit');
    Route::put('/kamar/edit/{id}', [KamarMainController::class, 'update'])->name('kamar.update');

    // harga
    Route::get('/harga', [HargaMainController::class, 'index'])->name('harga');
    Route::post('/harga/datatableharga', [HargaMainController::class, 'datatableharga'])->name('harga.datatableharga');
    Route::get('/harga/tambah', [HargaMainController::class, 'tambah'])->name('harga.tambah');
    Route::post('/harga/tambah', [HargaMainController::class, 'create'])->name('harga.post');
    Route::get('/harga/edit/{id}', [HargaMainController::class, 'edit'])->name('harga.edit');
    Route::put('/harga/edit/{id}', [HargaMainController::class, 'update'])->name('harga.update');

    // penyewa
    Route::get('/penyewa', [PenyewaMainController::class, 'index'])->name('penyewa');
    Route::post('/penyewa/datatablepenyewa', [PenyewaMainController::class, 'datatablepenyewa'])->name('penyewa.datatablepenyewa');
    Route::post('/penyewa/singkron', [PenyewaMainController::class, 'singkron'])->name('penyewa.singkron');
    Route::post('/penyewa/hentikanasrama', [PenyewaMainController::class, 'hentikanasrama'])->name('penyewa.hentikanasrama');
    Route::post('/penyewa/hentikancatering', [PenyewaMainController::class, 'hentikancatering'])->name('penyewa.hentikancatering');

    // tipe catering
    Route::get('/tipecatering', [TipecateringMainController::class, 'index'])->name('tipecatering');
    Route::post('/tipecatering/datatabletipecatering', [TipecateringMainController::class, 'datatabletipecatering'])->name('tipecatering.datatabletipecatering');
    Route::get('/tipecatering/tambah', [TipecateringMainController::class, 'tambah'])->name('tipecatering.tambah');
    Route::post('/tipecatering/tambah', [TipecateringMainController::class, 'create'])->name('tipecatering.post');
    Route::get('/tipecatering/edit/{id}', [TipecateringMainController::class, 'edit'])->name('tipecatering.edit');
    Route::put('/tipecatering/edit/{id}', [TipecateringMainController::class, 'update'])->name('tipecatering.update');

    // tagih
    Route::get('/tagih', [TagihMainController::class, 'index'])->name('tagih');
    Route::post('/tagih/datatabletagih', [TagihMainController::class, 'datatabletagih'])->name('tagih.datatabletagih');
    Route::get('/tagih/tambah', [TagihMainController::class, 'tambah'])->name('tagih.tambah');
    Route::post('/tagih/tambah', [TagihMainController::class, 'create'])->name('tagih.post');
    Route::get('/tagih/edit/{id}', [TagihMainController::class, 'edit'])->name('tagih.edit');
    Route::put('/tagih/edit/{id}', [TagihMainController::class, 'update'])->name('tagih.update');

    // role
    Route::get('/role', [RoleMainController::class, 'index'])->name('role');
    Route::post('/role/datatablerole', [roleMainController::class, 'datatablerole'])->name('role.datatablerole');

    // pengguna
    Route::get('/pengguna', [PenggunaMainController::class, 'index'])->name('pengguna');
    Route::post('/pengguna/datatablepengguna', [PenggunaMainController::class, 'datatablepengguna'])->name('pengguna.datatablepengguna');
    Route::post('/pengguna/editrole', [PenggunaMainController::class, 'editrole'])->name('pengguna.editrole');
    Route::post('/pengguna/status', [PenggunaMainController::class, 'status'])->name('pengguna.status');
});
