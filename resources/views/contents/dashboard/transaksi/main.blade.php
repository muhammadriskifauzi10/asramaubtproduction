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
                    <div class="col-xl-2 mb-3">
                        <label for="dari_tanggal" class="form-label fw-bold">Dari Tanggal</label>
                        <input type="date" name="dari_tanggal" class="form-control" id="dari_tanggal">
                    </div>
                    {{-- sampai tanggal --}}
                    <div class="col-xl-2 mb-3">
                        <label for="sampai_tanggal" class="form-label fw-bold">Sampai Tanggal</label>
                        <input type="date" name="sampai_tanggal" class="form-control" id="sampai_tanggal">
                    </div>
                    {{-- no invoice --}}
                    <div class="col-xl-2 mb-3">
                        <label for="no_invoice" class="form-label fw-bold">No Invoice</label>
                        <select class="form-select form-select-2" name="no_invoice" id="no_invoice" style="width: 100%;">
                            <option value="">Filter No Invoice</option>
                            @foreach (\App\Models\Transaksi::whereNotNull('no_invoice')->select('no_invoice')->distinct()->get() as $row)
                                <option value="{{ $row->no_invoice }}">{{ $row->no_invoice }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- penyewa --}}
                    <div class="col-xl-2 mb-3">
                        <label for="penyewa" class="form-label fw-bold">Penyewa</label>
                        <select class="form-select form-select-2" name="penyewa" id="penyewa" style="width: 100%;">
                            <option value="">Filter Penyewa</option>
                            @foreach (\App\Models\Penyewa::all() as $row)
                                <option value="{{ $row->nim }}">{{ $row->namalengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- metode pembayaran --}}
                    <div class="col-xl-2 mb-3">
                        <label for="metode_pembayaran" class="form-label fw-bold">Metode Pembayaran</label>
                        <select class="form-select form-select-2" name="metode_pembayaran" id="metode_pembayaran"
                            style="width: 100%;">
                            <option value="">Filter Metode Pembayaran</option>
                            @foreach (\App\Models\Transaksi::select('metode_pembayaran')->distinct()->get() as $row)
                                <option value="{{ $row->metode_pembayaran }}">{{ $row->metode_pembayaran }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- jenis pembayaran --}}
                    <div class="col-xl-2 mb-3">
                        <label for="jenis_pembayaran" class="form-label fw-bold">Jenis Pembayaran</label>
                        <select class="form-select form-select-2" name="jenis_pembayaran" id="jenis_pembayaran"
                            style="width: 100%;">
                            <option value="">Filter Jenis Pembayaran</option>
                            @foreach (\App\Models\Transaksi::select('jenis_transaksi')->distinct()->get() as $row)
                                <option value="{{ $row->jenis_transaksi }}">{{ $row->jenis_transaksi }}</option>
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
                        <table class="table m-0" id="datatabletransaksi" style="width: 100%">
                            <thead class="bg-dark text-light">
                                <tr>
                                    <th scope="col" width="50">NO</th>
                                    <th scope="col">JENIS PEMBAYARAN</th>
                                    <th scope="col">NO INVOICE</th>
                                    <th scope="col">NO KUITANSI</th>
                                    <th scope="col">NAMA</th>
                                    <th scope="col">TANGGAL PEMBAYARAN</th>
                                    <th scope="col">JUMLAH UANG</th>
                                    <th scope="col">METODE PEMBAYARAN</th>
                                    <th scope="col">TANGGAL REFERENSI BAYAR</th>
                                    <th scope="col">FILE BUKTI</th>
                                    <th scope="col">OPERATOR</th>
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
            table = $("#datatabletransaksi").DataTable({
                processing: true,
                paging: false,
                ajax: {
                    url: "{{ route('transaksi.datatabletransaksi') }}",
                    type: "POST",
                    // dataSrc: ""
                    dataType: "json",
                    data: function(d) {
                        d.dari_tanggal = $("#dari_tanggal").val();
                        d.sampai_tanggal = $("#sampai_tanggal").val();
                        d.no_invoice = $("#no_invoice").val();
                        d.nim = $("#penyewa").val();
                        d.metode_pembayaran = $("#metode_pembayaran").val();
                        d.jenis_pembayaran = $("#jenis_pembayaran").val();
                    },
                },
                columns: [{
                        data: "no",
                    },
                    {
                        data: "jenis_pembayaran",
                    },
                    {
                        data: "no_invoice",
                    },
                    {
                        data: "no_transaksi",
                    },
                    {
                        data: "nama",
                    },
                    {
                        data: "tanggal_transaksi",
                    },
                    {
                        data: "jumlah_uang",
                    },
                    {
                        data: "metode_pembayaran",
                    },
                    {
                        data: "tanggal_referensi_bayar",
                    },
                    {
                        data: "file_bukti",
                    },
                    {
                        data: "operator",
                    },
                ],
                "order": [
                    [5, 'desc']
                ],
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
                    filename: "pembayaran",
                    className: 'btn btn-success',
                    exportOptions: {
                        columns: ':not(:first-child)',
                        modifier: {
                            search: "none",
                        },
                    },
                    title: `Pembayaran`
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

            $("#dari_tanggal, #sampai_tanggal, #no_invoice, #penyewa, #metode_pembayaran, #jenis_pembayaran")
                .change(
                    function() {
                        table.ajax.reload();
                    });
        });

        function onRefresh() {
            table.ajax.reload()
        }

        $(document).on('click', '.lihat-detail', function() {
            var no_transaksi = $(this).data('no-transaksi');

            $.ajax({
                url: "{{ route('transaksi.detail') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    no_transaksi: no_transaksi
                },
                beforeSend: function() {
                    $('#universalModal').modal('show');
                    $("#universalModalContent").empty();
                    $("#universalModalContent").addClass("modal-xl modal-dialog-centered");
                },
                success: function(response) {
                    if (response.status == 200) {
                        $("#universalModalContent").append(response.dataHTML)
                    }
                }
            });
        });
    </script>
@endpush
