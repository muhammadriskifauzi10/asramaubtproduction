@extends('layouts.main')

@section('contents')
    <div class="container-fluid">
        <h1 class="mt-4">{{ $judul }}</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">{{ $judul }}</li>
        </ol>
        <div class="mb-2 d-flex align-content-center justify-content-end gap-2">
            <a href="javascript:void(0)" class="btn btn-info" onclick="onRefresh()">
                <i class="fa-solid fa-arrows-rotate me-1"></i>
                Refresh
            </a>
            <a href="{{ route('tipeasrama.tambah') }}" class="btn btn-dark">
                <i class="fa fa-plus me-1"></i>
                {{ $judul }}
            </a>
        </div>
        <div class="card mb-4 border-0" style="background-color: rgb(227 255 230)">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-xl-12">
                        <table class="table m-0" id="datatabletipeasrama" style="width: 100%">
                            <thead class="bg-dark text-light">
                                <tr>
                                    <th scope="col" width="50"></th>
                                    <th scope="col">NAMA</th>
                                    <th scope="col">JUMLAH LANTAI</th>
                                    <th scope="col">JUMLAH KAMAR</th>
                                    <th scope="col">JUMLAH KAPASITAS</th>
                                    <th scope="col">JUMLAH PENYEWA</th>
                                    <th scope="col">TERSEDIA</th>
                                </tr>
                            </thead>
                            <tfoot class="bg-dark text-light">
                                <tr>
                                    <th></th>
                                    <th>TOTAL</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
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
            table = $("#datatabletipeasrama").DataTable({
                processing: true,
                paging: false,
                ajax: {
                    url: "{{ route('tipeasrama.datatabletipeasrama') }}",
                    type: "POST",
                    // dataSrc: ""
                    dataType: "json"
                },
                columns: [{
                        data: "aksi",
                    },
                    {
                        data: "nama",
                    },
                    {
                        data: "jumlah_lantai",
                    },
                    {
                        data: "jumlah_kamar",
                    },
                    {
                        data: "jumlah_kapasitas",
                    },
                    {
                        data: "jumlah_penyewa",
                    },
                    {
                        data: "tersedia",
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
                    var tooltipTriggerList = [].slice.call(
                        document.querySelectorAll('[data-bs-toggle="tooltip"]')
                    );

                    tooltipTriggerList.map(function(tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });
                },
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();

                    // Fungsi untuk mengubah data menjadi angka
                    var intVal = function(i) {
                        if (typeof i === 'string') {
                            return parseInt(i.replace(/[\$,]/g, '')) || 0;
                        }

                        if (typeof i === 'number') {
                            return i;
                        }

                        return 0;
                    };

                    // JUMLAH LANTAI - kolom index 2
                    var totalLantai = api
                        .column(2, {
                            page: 'current'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    // JUMLAH KAMAR - kolom index 3
                    var totalKamar = api
                        .column(3, {
                            page: 'current'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    // JUMLAH KAPASITAS - kolom index 4
                    var totalKapasitas = api
                        .column(4, {
                            page: 'current'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    // JUMLAH PENYEWA - kolom index 5
                    var totalPenyewa = api
                        .column(5, {
                            page: 'current'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    // TERSEDIA - kolom index 6
                    var totalTersedia = api
                        .column(6, {
                            page: 'current'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    // Tampilkan ke footer
                    $(api.column(2).footer()).html(totalLantai);
                    $(api.column(3).footer()).html(totalKamar);
                    $(api.column(4).footer()).html(totalKapasitas);
                    $(api.column(5).footer()).html(totalPenyewa);
                    $(api.column(6).footer()).html(totalTersedia);
                },
            });
        });

        function onRefresh() {
            table.ajax.reload()
        }
    </script>
@endpush
