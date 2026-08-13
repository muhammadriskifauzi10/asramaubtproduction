@extends('layouts.main')

@section('contents')
    <div class="container-fluid">
        <h1 class="mt-4">{{ $judul }}</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dasbor') }}">Kembali</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $judul }}</li>
        </ol>

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
                        <table class="table m-0" id="datatablepermintaankamar" style="width: 100%">
                            <thead class="bg-dark text-light">
                                <tr>
                                    <th scope="col" width="50">NO</th>
                                    <th scope="col">TANGGAL PERMINTAN</th>
                                    <th scope="col">TANGGAL VERIFIKASI</th>
                                    <th scope="col">NAMA</th>
                                    <th scope="col">NIM</th>
                                    <th scope="col">BILL TO</th>
                                    <th scope="col">NO REQUEST</th>
                                    <th scope="col">STATUS PEMBAYARAN</th>
                                    <th scope="col">TANGGAL MASUK</th>
                                    <th scope="col">TANGGAL KELUAR</th>
                                    <th scope="col">DURASI</th>
                                    <th scope="col">KAMAR</th>
                                    <th scope="col">TOTAL TAGIHAN</th>
                                    <th scope="col">TOTAL POTONGAN HARGA</th>
                                    <th scope="col">NET TAGIHAN</th>
                                    <th scope="col">TOTAL BAYAR</th>
                                    <th scope="col">PIUTANG</th>
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
            table = $("#datatablepermintaankamar").DataTable({
                processing: true,
                paging: false,
                ajax: {
                    url: "{{ route('pengguna.verifikasipermintaankamar.datatableverifikasipermintaankamar') }}",
                    type: "POST",
                    // dataSrc: ""
                    dataType: "json",
                    data: function(d) {
                        d.penyewa_id = "{{ $penyewa->id }}"
                    },
                },
                columns: [{
                        data: "nomor",
                    },
                    {
                        data: "tanggal_permintaan",
                    },
                    {
                        data: "tanggal_verifikasi",
                    },
                    {
                        data: "nama",
                    },
                    {
                        data: "nim",
                    },
                    {
                        data: "nama_bill_to",
                    },
                    {
                        data: "no_request",
                    },
                    {
                        data: "status_pembayaran",
                    },
                    {
                        data: "tanggal_masuk",
                    },
                    {
                        data: "tanggal_keluar",
                    },
                    {
                        data: "durasi",
                    },
                    {
                        data: "kamar",
                    },
                    {
                        data: "total_tagihan",
                    },
                    {
                        data: "total_potongan_harga",
                    },
                    {
                        data: "net_tagihan",
                    },
                    {
                        data: "total_bayar",
                    },
                    {
                        data: "piutang",
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
        });

        function onRefresh() {
            table.ajax.reload()
        }
    </script>
@endpush
