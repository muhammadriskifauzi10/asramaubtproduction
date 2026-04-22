<nav class="navbar navbar-expand-lg navbar-light bg-green">
    <div class="container-fluid navbar-child">
        <div class="navbar-child-1">
            <a class="navbar-brand yellow fw-bold logo" href="{{ route('dasbor') }}">{{ env('APP_NAME') }}</a>
            <div class="d-flex gap-2">
                <button class="btn btn-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu"
                    aria-controls="offcanvasMenu" id="btnOpenSidebarMenu">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
        </div>

        <div class="navbar-child-2">
            @if (auth()->user()->role_id == 8 || auth()->user()->role_id == 0)
            @else
                <a href="{{ route('tagihan.tambah') }}" class="btn btn-sm btn-dark fw-bold text-light me-2"
                    data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Buat Tagihan Baru">
                    <i class="fa-solid fa-credit-card me-1"></i>
                    Buat Tagihan
                </a>
            @endif
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
                            <button type="submit" class="w-100 btn btn-link text-danger text-decoration-none fw-bold">
                                Keluar
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
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
                    class="list-group-item sidebar-link {{ request()->is('tagihan') ? 'active' : '' }}">
                    <i class="fa-solid fa-list"></i>
                    <span>Tagihan</span>
                </a>

                <a href="{{ route('perpanjang') }}"
                    class="list-group-item sidebar-link {{ request()->is('perpanjang') ? 'active' : '' }}">
                    <i class="fa-solid fa-calendar-plus"></i>
                    <span>Perpanjang</span>
                </a>

                <div class="sidebar-title">Laporan</div>

                <a href="{{ route('transaksi') }}"
                    class="list-group-item sidebar-link {{ request()->is('transaksi') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-lines"></i>
                    <span>Transaksi</span>
                </a>

                <a href="#" class="list-group-item sidebar-link">
                    <i class="fa-solid fa-users"></i>
                    <span>Rekap Peserta Catering</span>
                </a>

                <a href="{{ route('omset') }}"
                    class="list-group-item sidebar-link {{ request()->is('omset*') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line"></i>
                    <span>Omset</span>
                </a>

                <a href="{{ route('piutang') }}"
                    class="list-group-item sidebar-link {{ request()->is('piutang*') ? 'active' : '' }}">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                    <span>Piutang</span>
                </a>

                <div class="sidebar-title">Master Data</div>

                <a href="{{ route('lantai') }}"
                    class="list-group-item sidebar-link {{ request()->is('lantai*') ? 'active' : '' }}">
                    <i class="fa-solid fa-layer-group"></i>
                    <span>Lantai</span>
                </a>

                <a href="{{ route('tipeasrama') }}"
                    class="list-group-item sidebar-link {{ request()->is('tipeasrama*') ? 'active' : '' }}">
                    <i class="fa-solid fa-building"></i>
                    <span>Tipe Asrama</span>
                </a>

                <a href="{{ route('kamar') }}"
                    class="list-group-item sidebar-link {{ request()->is('kamar*') ? 'active' : '' }}">
                    <i class="fa-solid fa-bed"></i>
                    <span>Kamar</span>
                </a>

                <a href="{{ route('harga') }}"
                    class="list-group-item sidebar-link {{ request()->is('harga*') ? 'active' : '' }}">
                    <i class="fa-solid fa-tags"></i>
                    <span>Harga</span>
                </a>

                <a href="{{ route('penyewa') }}"
                    class="list-group-item sidebar-link {{ request()->is('penyewa*') ? 'active' : '' }}">
                    <i class="fa-solid fa-user"></i>
                    <span>Penyewa</span>
                </a>

                <a href="{{ route('tipecatering') }}"
                    class="list-group-item sidebar-link {{ request()->is('tipecatering*') ? 'active' : '' }}">
                    <i class="fa-solid fa-utensils"></i>
                    <span>Tipe Catering</span>
                </a>

                <a href="{{ route('tagih') }}"
                    class="list-group-item sidebar-link {{ request()->is('tagih') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-invoice"></i>
                    <span>Tagih</span>
                </a>

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
