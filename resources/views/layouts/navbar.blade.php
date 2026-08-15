<nav class="navbar navbar-expand-lg navbar-light bg-green">
    <div class="container-fluid navbar-child">
        <div class="navbar-child-1">
            <a class="navbar-brand yellow fw-bold logo" href="{{ route('dasbor') }}">{{ env('APP_NAME') }}</a>
            @if (auth()->user()->role_id == 8 || auth()->user()->role_id == 0)
            @else
                <div class="d-flex gap-2">
                    <button class="btn btn-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu"
                        aria-controls="offcanvasMenu" id="btnOpenSidebarMenu">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
            @endif
        </div>

        <div class="navbar-child-2">
            @if (auth()->user()->role_id == 8 || auth()->user()->role_id == 0)
                <a href="{{ route('pengguna.tagihan.tambah') }}" class="btn btn-sm btn-dark fw-bold text-light me-2"
                    data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Buat Permintaan Kamar">
                    <i class="fa-solid fa-plus me-1"></i>
                    Permintaan Kamar
                </a>
                <div class="btn-group me-2">
                    <button type="button" class="dropdown-toggle fw-bold text-dark" data-bs-toggle="dropdown"
                        aria-expanded="false" style="background-color: transparent;">
                        Hai, {{ auth()->user()->name }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item text-center" href="#" data-bs-toggle="modal"
                                data-bs-target="#modalProfil">
                                <i class="fas fa-user me-2"></i>Lihat Profil
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-100 btn btn-link text-danger text-decoration-none fw-bold">
                                    Keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>

                <div class="modal fade" id="modalProfil" tabindex="-1" aria-labelledby="modalProfilLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title" id="modalProfilLabel">
                                    <i class="fas fa-user-circle me-2"></i>Profil Mahasiswa
                                </h5>
                                <button type="button" class="btn-close btn-close-white"
                                    data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <div class="text-center mb-4">
                                    <i class="fas fa-user-circle text-success" style="font-size:80px;"></i>
                                    <h5 class="mt-3 mb-0">{{ auth()->user()->name }}</h5>
                                    <small class="text-muted">Mahasiswa</small>
                                </div>
                                <table class="t-top">
                                    <tbody>
                                        <tr>
                                            <th width="130">Nama</th>
                                            <td>: {{ auth()->user()->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>NIM</th>
                                            <td>: {{ auth()->user()->identifier }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td>: {{ auth()->user()->email }}</td>
                                        </tr>
                                        <tr>
                                            <th>Barcode</th>
                                            <td>
                                                : <img
                                                    src="https://bwipjs-api.metafloor.com/?bcid=code128&text={{ auth()->user()->identifier }}&scale=3&height=10&includetext"
                                                    alt="Barcode Mahasiswa" class="rounded shadow-sm">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <a href="{{ route('tagihan.tambah') }}" class="btn btn-sm btn-dark fw-bold text-light me-2"
                    data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Buat Tagihan Baru">
                    <i class="fa-solid fa-credit-card me-1"></i>
                    Buat Tagihan
                </a>
                <div class="btn-group me-2">
                    <button type="button" class="dropdown-toggle fw-bold text-dark" data-bs-toggle="dropdown"
                        aria-expanded="false" style="background-color: transparent;">
                        Hai, {{ auth()->user()->name }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item text-center" href="#">Lihat Profil</a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-100 btn btn-link text-danger text-decoration-none fw-bold">
                                    Keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endif
        </div>
    </div>
</nav>

<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasMenu" aria-labelledby="offcanvasMenuLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasMenuLabel">{{ env('APP_NAME') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"
            id="btnCloseSidebarMenu"></button>
    </div>
    <div class="offcanvas-body">
        <div class="list-group border-0 sidebar-menu">
            @if (auth()->user()->role_id == 8 || auth()->user()->role_id == 0)
            @else
                <a href="{{ route('dasbor') }}"
                    class="list-group-item sidebar-link {{ request()->is('dasbor*') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge"></i>
                    <span>Dasbor</span>
                </a>

                <div class="sidebar-title">Transaksi</div>

                <a href="{{ route('tagihan') }}"
                    class="list-group-item sidebar-link {{ request()->is('tagihan*') ? 'active' : '' }}">
                    <i class="fa-solid fa-list"></i>
                    <span>Tagihan</span>
                </a>

                <a href="{{ route('perpanjang') }}"
                    class="list-group-item sidebar-link {{ request()->is('perpanjang') ? 'active' : '' }}">
                    <i class="fa-solid fa-calendar-plus"></i>
                    <span>Perpanjang</span>
                </a>

                <a href="{{ route('deposit') }}"
                    class="list-group-item sidebar-link {{ request()->is('deposit') ? 'active' : '' }}">
                    <i class="fa-solid fa-wallet"></i>
                    <span>Deposit</span>
                </a>

                {{-- <div class="sidebar-title">Request Kamar</div>

                <a href="{{ route('request.permintaankamar') }}"
                    class="list-group-item sidebar-link {{ request()->is('permintaankamar') ? 'active' : '' }}">
                    <i class="fa-solid fa-bed"></i>
                    <span>Permintaan</span>
                </a>

                <a href="{{ route('request.verifikasipermintaankamar') }}"
                    class="list-group-item sidebar-link {{ request()->is('verifikasipermintaankamar') ? 'active' : '' }}">
                    <i class="fa-solid fa-check-circle"></i>
                    <span>Verifikasi</span>
                </a> --}}

                <div class="sidebar-title">Laporan</div>

                <a href="{{ route('omset') }}"
                    class="list-group-item sidebar-link {{ request()->is('omset*') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line"></i>
                    <span>Omset</span>
                </a>

                <a href="{{ route('transaksi') }}"
                    class="list-group-item sidebar-link {{ request()->is('transaksi') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-lines"></i>
                    <span>Pembayaran</span>
                </a>

                <a href="{{ route('piutang') }}"
                    class="list-group-item sidebar-link {{ request()->is('piutang*') ? 'active' : '' }}">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                    <span>Piutang</span>
                </a>
                {{--
                <a href="#" class="list-group-item sidebar-link">
                    <i class="fa-solid fa-users"></i>
                    <span>Rekap Peserta Catering</span>
                </a> --}}

                <div class="sidebar-title">Master Data</div>

                {{-- <a href="{{ route('lantai') }}"
                    class="list-group-item sidebar-link {{ request()->is('lantai*') ? 'active' : '' }}">
                    <i class="fa-solid fa-layer-group"></i>
                    <span>Lantai</span>
                </a> --}}

                <a href="{{ route('tipeasrama') }}"
                    class="list-group-item sidebar-link {{ request()->is('tipeasrama*') ? 'active' : '' }}">
                    <i class="fa-solid fa-building"></i>
                    <span>Lokasi Asrama</span>
                </a>

                {{-- <a href="{{ route('kamar') }}"
                    class="list-group-item sidebar-link {{ request()->is('kamar*') ? 'active' : '' }}">
                    <i class="fa-solid fa-bed"></i>
                    <span>Kamar</span>
                </a> --}}
                <a href="{{ route('tagih') }}"
                    class="list-group-item sidebar-link {{ request()->is('tagih') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-invoice"></i>
                    <span>Kategori Tagihan</span>
                </a>

                <a href="{{ route('harga') }}"
                    class="list-group-item sidebar-link {{ request()->is('harga*') ? 'active' : '' }}">
                    <i class="fa-solid fa-tags"></i>
                    <span>Item Tagihan</span>
                </a>

                <a href="{{ route('penyewa') }}"
                    class="list-group-item sidebar-link {{ request()->is('penyewa*') ? 'active' : '' }}">
                    <i class="fa-solid fa-user"></i>
                    <span>Penyewa</span>
                </a>
                {{--
                <a href="{{ route('tipecatering') }}"
                    class="list-group-item sidebar-link {{ request()->is('tipecatering*') ? 'active' : '' }}">
                    <i class="fa-solid fa-utensils"></i>
                    <span>Tipe Catering</span>
                </a> --}}
                <div class="sidebar-title">Manajemen Pengguna</div>

                <a href="{{ route('role') }}"
                    class="list-group-item sidebar-link {{ request()->is('role*') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-shield"></i>
                    <span>Role</span>
                </a>

                <a href="{{ route('pengguna') }}"
                    class="list-group-item sidebar-link {{ request()->is('pengguna*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i>
                    <span>Pengguna</span>
                </a>
            @endif
        </div>
    </div>
</div>
