@extends('layouts.main')

@section('contents')
    <div class="container-fluid">
        <h1 class="mt-4">{{ $judul }}</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">{{ $judul }}</li>
        </ol>

        <div class="card mb-4 border-0" style="background-color: rgb(255 227 248)">
            <div class="card-body">
                <div class="row justify-content-center">
                    {{-- dari tanggal --}}
                    <div class="col-xl-3 mb-3">
                        <label for="dari_tanggal" class="form-label fw-bold">Dari Tanggal</label>
                        <input type="date" name="dari_tanggal" class="form-control" id="dari_tanggal">
                    </div>
                    {{-- sampai tanggal --}}
                    <div class="col-xl-3 mb-3">
                        <label for="sampai_tanggal" class="form-label fw-bold">Sampai Tanggal</label>
                        <input type="date" name="sampai_tanggal" class="form-control" id="sampai_tanggal">
                    </div>
                    {{-- penyewa --}}
                    <div class="col-xl-3 mb-3">
                        <label for="penyewa" class="form-label fw-bold">Penyewa</label>
                        <select class="form-select form-select-2" name="penyewa" id="penyewa" style="width: 100%;">
                            <option value="">Filter Penyewa</option>
                            @foreach (\App\Models\Penyewa::all() as $row)
                                <option value="{{ $row->id }}">{{ $row->namalengkap }}</option>
                            @endforeach
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
                        <table class="table m-0" id="datatableomset" style="width: 100%">
                            <thead class="bg-dark text-light">
                                <tr>
                                    <th scope="col" width="50">NO</th>
                                    <th scope="col">NO INVOICE</th>
                                    <th scope="col">NAMA</th>
                                    <th scope="col">NIM</th>
                                    <th scope="col">BILL TO</th>
                                    <th scope="col">ASRAMA</th>
                                    <th scope="col">CATERING</th>
                                    <th scope="col">OMSET</th>
                                    <th scope="col">POTONGAN ASRAMA</th>
                                    <th scope="col">POTONGAN CATERING</th>
                                    <th scope="col">TOTAL POTONGAN HARGA</th>
                                    <th scope="col">NET OMSET</th>
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
            table = $("#datatableomset").DataTable({
                processing: true,
                paging: false,
                ajax: {
                    url: "{{ route('omset.datatableomset') }}",
                    type: "POST",
                    // dataSrc: ""
                    dataType: "json",
                    data: function(d) {
                        d.dari_tanggal = $("#dari_tanggal").val();
                        d.sampai_tanggal = $("#sampai_tanggal").val();
                        d.penyewa = $("#penyewa").val();
                    },
                },
                columns: [{
                        data: "nomor",
                    },
                    {
                        data: "no_invoice",
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
                        data: "asrama",
                    },
                    {
                        data: "catering",
                    },
                    {
                        data: "omset",
                    },
                    {
                        data: "potongan_asrama",
                    },
                    {
                        data: "potongan_catering",
                    },
                    {
                        data: "total_potongan_harga",
                    },
                    {
                        data: "net_omset",
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

            $("#dari_tanggal, #sampai_tanggal, #penyewa").change(function() {
                table.ajax.reload();
            });
        });

        function onRefresh() {
            table.ajax.reload()
        }
    </script>
@endpush
