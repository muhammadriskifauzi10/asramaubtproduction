@extends('layouts.main')

@section('contents')
    <div class="container-fluid">
        <h1 class="mt-4">{{ $judul }} {{ $tipeasrama->nama }}</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('tipeasrama') }}">Kembali</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $judul }}</li>
        </ol>

        <div class="card mb-4 border-0" style="background-color: rgb(255 227 248)">
            <div class="card-body">
                <div class="row justify-content-center">
                    @php
                        $rekap = \App\Models\Kamar::selectRaw(
                            "
                                lantai,
                                SUM(kapasitas) as total_kapasitas,
                                SUM(jumlah_penyewa) as total_penyewa,
                                SUM(kapasitas - jumlah_penyewa) as total_tersedia
                            ",
                        )
                            ->where('tipe_asrama_id', $tipeasrama->id)
                            ->groupBy('lantai')
                            ->orderBy('lantai')
                            ->get();

                        $grandKapasitas = $rekap->sum('total_kapasitas');
                        $grandPenyewa = $rekap->sum('total_penyewa');
                        $grandTersedia = $rekap->sum('total_tersedia');
                    @endphp

                    <div class="col-xl-12 my-3">
                        <table class="table table-bordered m-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>LANTAI</th>
                                    <th class="text-center">JUMLAH KAPASITAS</th>
                                    <th class="text-center">JUMLAH PENYEWA</th>
                                    <th class="text-center">JUMLAH TERSEDIA</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($rekap as $item)
                                    <tr>
                                        <td>Lantai {{ $item->lantai }}</td>
                                        <td class="text-center">{{ $item->total_kapasitas }}</td>
                                        <td class="text-center">{{ $item->total_penyewa }}</td>
                                        <td class="text-center">{{ $item->total_tersedia }}</td>
                                    </tr>
                                @endforeach
                            </tbody>

                            <tfoot class="fw-bold">
                                <tr>
                                    <td>Total</td>
                                    <td class="text-center">{{ $grandKapasitas }}</td>
                                    <td class="text-center">{{ $grandPenyewa }}</td>
                                    <td class="text-center">{{ $grandTersedia }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-2 d-flex align-content-center justify-content-end gap-2">
            <a href="javascript:void(0)" class="btn btn-info" onclick="onRefresh()">
                <i class="fa-solid fa-arrows-rotate me-1"></i>
                Refresh
            </a>
            <a href="{{ route('kamar.tambah', $tipeasrama->id) }}" class="btn btn-dark">
                <i class="fa fa-plus me-1"></i>
                {{ $judul }}
            </a>
        </div>
        <div class="card mb-4 border-0" style="background-color: rgb(227 255 230)">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-xl-12">
                        <table class="table m-0" id="datatablekamar" style="width: 100%">
                            <thead class="bg-dark text-light">
                                <tr>
                                    <th scope="col" width="50"></th>
                                    <th scope="col">TOKEN LISTRIK</th>
                                    <th scope="col">TIPE ASRAMA</th>
                                    <th scope="col">LANTAI</th>
                                    <th scope="col">NOMOR KAMAR</th>
                                    <th scope="col">KAPASITAS</th>
                                    <th scope="col">JUMLAH PENYEWA</th>
                                    <th scope="col">TERSEDIA</th>
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
            table = $("#datatablekamar").DataTable({
                processing: true,
                paging: false,
                ajax: {
                    url: "{{ route('kamar.datatablekamar') }}",
                    type: "POST",
                    // dataSrc: ""
                    dataType: "json",
                    data: function(d) {
                        d.tipeasrama = "{{ $tipeasrama->id }}";
                    },
                },
                columns: [{
                        data: "aksi",
                    },
                    {
                        data: "token_listrik",
                    },
                    {
                        data: "type",
                    },
                    {
                        data: "lantai",
                    },
                    {
                        data: "nomor_kamar",
                    },
                    {
                        data: "kapasitas",
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
                dom: "lBfrtip",
                buttons: [{
                    extend: "excel",
                    text: "Export Excel",
                    filename: "daftar_kamar",
                    className: 'btn btn-success',
                    exportOptions: {
                        columns: ':not(:first-child)',
                        modifier: {
                            search: "none",
                        },
                    },
                    title: `Daftar Kamar`
                }, ],
                drawCallback: function() {
                    var tooltipTriggerList = [].slice.call(
                        document.querySelectorAll('[data-bs-toggle="tooltip"]')
                    );

                    tooltipTriggerList.map(function(tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });
                },
            });
        });

        function onRefresh() {
            table.ajax.reload()
        }

        $(document).on('click', '.lihat-penyewa', function() {
            let kamar_id = $(this).data('id');

            $.ajax({
                url: "{{ route('kamar.getpenyewa') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    kamar_id: kamar_id
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
