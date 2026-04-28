@extends('layouts.main')

@section('mystyles')
    <style>
        .lantai:hover .card {
            background-color: #b7d6ff;
            transition: .3s linear;
        }

        /* Tambahan untuk chart agar rapi di mobile */
        .chart-wrapper {
            overflow-x: auto;
            /* bisa digeser horizontal */
            -webkit-overflow-scrolling: touch;
            padding-bottom: 10px;
        }

        .chart-container {
            min-width: 700px;
            /* cegah chart terlalu kecil di HP */
            height: auto;
        }
    </style>
@endsection

@section('contents')
    <div class="container-fluid mt-3 mb-3">
        <div class="row">
            <div class="col-xl-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">{{ $judul }}</li>
                    </ol>
                </nav>

                <div class="row">
                    <!-- Total Penghuni -->
                    <div class="col-xl-4 mb-4">
                        <div class="card shadow-sm border-0 bg-primary text-white h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-people fs-1 mb-2"></i>
                                <h6 class="card-title">Total Penghuni</h6>
                                <h2 class="fw-bold">{{ \App\Models\Penyewa::where('status_asrama', 1)->get()->count() }}
                                </h2>
                            </div>
                        </div>
                    </div>

                    <!-- Total Laki-laki -->
                    <div class="col-xl-4 mb-4">
                        <div class="card shadow-sm border-0 bg-warning text-dark h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-gender-male fs-1 mb-2"></i>
                                <h6 class="card-title">Total Laki-laki</h6>
                                <h2 class="fw-bold">
                                    {{ \App\Models\Penyewa::where('jenis_kelamin', 'L')->where('status_asrama', 1)->get()->count() }}
                                </h2>
                            </div>
                        </div>
                    </div>

                    <!-- Total Perempuan -->
                    <div class="col-xl-4 mb-4">
                        <div class="card shadow-sm border-0 bg-danger text-white h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-gender-female fs-1 mb-2"></i>
                                <h6 class="card-title">Total Perempuan</h6>
                                <h2 class="fw-bold">
                                    {{ \App\Models\Penyewa::where('jenis_kelamin', 'P')->where('status_asrama', 1)->get()->count() }}
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscripts')
@endpush
