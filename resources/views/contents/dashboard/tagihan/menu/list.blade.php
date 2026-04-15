{{-- list --}}
<div class="col-xl-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-2">
            <div class="list-group list-group-flush sidebar-menu">
                <a href="{{ route('tagihan.tambah') }}"
                    class="list-group-item sidebar-link rounded mb-2 {{ request()->is('tagihan/tambah') ? 'active' : '' }}">
                    <i class="fa-solid fa-building me-3"></i>
                    <span class="fw-semibold">Tagihan Asrama & Catering</span>
                </a>
                <a href="{{ route('tagihan.tambah.catering') }}"
                    class="list-group-item sidebar-link rounded {{ request()->is('tagihan/tambah/catering') ? 'active' : '' }}">
                    <i class="fa-solid fa-utensils me-3"></i>
                    <span class="fw-semibold">Tagihan Catering</span>
                </a>
            </div>
        </div>
    </div>
</div>
