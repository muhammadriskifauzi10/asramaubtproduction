@extends('layouts.main')

@section('contents')
    <div class="container-fluid">
        <h1 class="mt-4">{{ $judul }}</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dasbor') }}">Kembali</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $judul }}</li>
        </ol>

        <div class="card mb-4 border-0" style="background-color: rgb(255 227 248)">
            <div class="card-body">
                <div class="row justify-content-center">
                    {{-- dari tanggal --}}
                    <div class="col-xl-2 mb-3">
                        <label for="dari_tanggal" class="form-label fw-bold">Dari Tanggal Masuk</label>
                        <input type="date" name="dari_tanggal" class="form-control" id="dari_tanggal"
                            value="{{ date('Y-m-01') }}">
                    </div>
                    {{-- sampai tanggal --}}
                    <div class="col-xl-2 mb-3">
                        <label for="sampai_tanggal" class="form-label fw-bold">Sampai Tanggal Masuk</label>
                        <input type="date" name="sampai_tanggal" class="form-control" id="sampai_tanggal"
                            {{ date('Y-m-d') }}>
                    </div>
                    {{-- pendaftaran dari --}}
                    <div class="col-xl-2 mb-3">
                        <label for="pendaftaran_dari" class="form-label fw-bold">Pendaftaran Dari</label>
                        <select class="form-select form-select-2" name="pendaftaran_dari" id="pendaftaran_dari"
                            style="width: 100%;">
                            <option value="">Filter Pendaftaran Dari</option>
                            <option value="Akun SSO">Akun SSO</option>
                            <option value="Akun Simaba">Akun Simaba</option>
                        </select>
                    </div>
                    {{-- status pengajuan --}}
                    <div class="col-xl-2 mb-3">
                        <label for="status_pengajuan" class="form-label fw-bold">Status Pengajuan</label>
                        <select class="form-select form-select-2" name="status_pengajuan" id="status_pengajuan"
                            style="width: 100%;">
                            <option value="">Filter Status Pengajuan</option>
                            <option value="Menunggu Pembayaran">Menunggu Pembayaran</option>
                            <option value="Menunggu Verifikasi Pembayaran">Menunggu Verifikasi Pembayaran</option>
                            <option value="Permintaan Kamar Ditolak">Permintaan Kamar Ditolak</option>
                            <option value="Permintaan Kamar Disetujui">Permintaan Kamar Disetujui</option>
                            <option value="Kontrak Asrama Disetujui">Kontrak Asrama Disetujui</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-2 d-flex align-content-center justify-content-end gap-2">
            <a href="javascript:void(0)" class="btn btn-info" onclick="onRefresh()">
                <i class="fa-solid fa-arrows-rotate me-1"></i>
                Refresh
            </a>
        </div>

        <div class="card mb-4 border-0" style="background-color: rgb(227 255 230)">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-xl-12">
                        <table class="table m-0" id="datatablepengajuankamar" style="width: 100%">
                            <thead class="bg-dark text-light">
                                <tr>
                                    <th scope="col" width="50"></th>
                                    <th scope="col">TANGGAL PENGAJUAN</th>
                                    <th scope="col">NO PENGAJUAN</th>
                                    <th scope="col">PENDAFTARAN DARI</th>
                                    <th scope="col">NIM</th>
                                    <th scope="col">NO SIMABA</th>
                                    <th scope="col">NAMA</th>
                                    <th scope="col">NO KTP</th>
                                    <th scope="col">JENIS KELAMIN</th>
                                    <th scope="col">PRODI</th>
                                    <th scope="col">PERMINTAAN KAMAR</th>
                                    <th scope="col">STATUS</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscripts')
    <script>
        var table
        $(document).ready(function() {
            table = $("#datatablepengajuankamar").DataTable({
                processing: true,
                paging: false,
                ajax: {
                    url: "{{ route('pengajuankamar.datatablepengajuankamar') }}",
                    type: "POST",
                    // dataSrc: ""
                    dataType: "json",
                    data: function(d) {
                        d.dari_tanggal = $("#dari_tanggal").val();
                        d.sampai_tanggal = $("#sampai_tanggal").val();
                        d.pendaftaran_dari = $("#pendaftaran_dari").val();
                        d.status_pengajuan = $("#status_pengajuan").val();
                    },
                },
                columns: [{
                        data: "aksi",
                    },
                    {
                        data: "tanggal_pengajuan",
                    },
                    {
                        data: "no_pengajuan",
                    },
                    {
                        data: "pendaftaran_dari",
                    },
                    {
                        data: "nim",
                    },
                    {
                        data: "no_simaba",
                    },
                    {
                        data: "nama",
                    },
                    {
                        data: "no_ktp",
                    },
                    {
                        data: "jenis_kelamin",
                    },
                    {
                        data: "prodi",
                    },
                    {
                        data: "permintaan_kamar",
                    },
                    {
                        data: "status",
                    },
                ],
                // "order": [
                //     [1, 'asc']
                // ],
                // scrollY: "700px",
                scrollX: true,
                // scrollCollapse: true,
                // paging:         false,
                // fixedColumns: {
                //     left: 3,
                // }
                dom: "lBfrtip",
                buttons: [{
                    extend: "excel",
                    text: "Export Excel",
                    filename: "tagihan",
                    className: 'btn btn-success',
                    exportOptions: {
                        columns: ':not(:first-child)',
                        modifier: {
                            search: "none",
                        },
                    },
                    title: `Pengajuan Kamar`
                }, ],
                drawCallback: function() {
                    // var api = this.api();

                    // tooltip
                    var tooltipTriggerList = [].slice.call(
                        document.querySelectorAll('[data-bs-toggle="tooltip"]')
                    );
                    tooltipTriggerList.map(function(tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });
                },
                // rowCallback: function(row, data) {
                //     if (data.status_row == 'completed') {
                //         $('td', row).addClass('bg-success text-light fw-bold');
                //     } else if (data.status_row == 'pending') {
                //         $('td', row).addClass('bg-warning text-dark fw-bold');
                //     } else {
                //         $('td', row).addClass('bg-danger text-light fw-bold');
                //     }
                // },
            });

            $("#dari_tanggal, #sampai_tanggal, #pendaftaran_dari, #status_pengajuan").change(function() {
                table.ajax.reload();
            });
        });
    </script>
@endpush
