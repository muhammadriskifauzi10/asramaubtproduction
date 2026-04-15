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
                        <label for="dari_tanggal" class="form-label fw-bold">Dari Tanggal Masuk</label>
                        <input type="date" name="dari_tanggal" class="form-control" id="dari_tanggal">
                    </div>
                    {{-- sampai tanggal --}}
                    <div class="col-xl-3 mb-3">
                        <label for="sampai_tanggal" class="form-label fw-bold">Sampai Tanggal Masuk</label>
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
            <button type="button" class="btn btn-primary" onclick="onPerpanjang()">
                <i class="fa-solid fa-calendar-plus me-1"></i>
                Perpanjang Tagihan
            </button>
            <a href="javascript:void(0)" class="btn btn-info" onclick="onRefresh()">
                <i class="fa-solid fa-arrows-rotate me-1"></i>
                Refresh
            </a>
        </div>

        <div class="card mb-4 border-0" style="background-color: rgb(227 255 230)">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-xl-12">
                        <table class="table m-0" id="datatableperpanjang" style="width: 100%">
                            <thead class="bg-dark text-light">
                                <tr>
                                    <th scope="col" width="50">
                                        <input type="checkbox" id="checkAll">
                                    </th>
                                    <th scope="col">NAMA</th>
                                    <th scope="col">NIM</th>
                                    <th scope="col">NO INVOICE</th>
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
            table = $("#datatableperpanjang").DataTable({
                processing: true,
                paging: false,
                ajax: {
                    url: "{{ route('perpanjang.datatableperpanjang') }}",
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
                        data: "aksi",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "nama",
                    },
                    {
                        data: "nim",
                    },
                    {
                        data: "no_invoice",
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
                        data: "hutang",
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

            $("#penyewa").change(function() {
                table.ajax.reload();
            });

            // check all
            $(document).on('change', '#checkAll', function() {
                $('.row-check').prop('checked', this.checked);
            });

            // jika salah satu unchecked → checkAll ikut mati
            $(document).on('change', '.row-check', function() {
                if ($('.row-check:checked').length !== $('.row-check').length) {
                    $('#checkAll').prop('checked', false);
                } else {
                    $('#checkAll').prop('checked', true);
                }
            });
        });

        function onRefresh() {
            table.ajax.reload()
        }

        function onPerpanjang() {
            let checkedIds = [];

            $('.row-check:checked').each(function() {
                checkedIds.push($(this).val());
            });

            if (checkedIds.length === 0) {
                Swal.fire({
                    text: 'Pilih data terlebih dahulu!',
                    icon: 'info'
                });
                return;
            }

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: `Apakah Anda benar-benar ingin perpanjang secara massal?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, perpanjang!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                width: '700px'
            }).then((result) => {
                if (result.isConfirmed) {
                    // tampilkan loading saat proses pengiriman berlangsung
                    Swal.fire({
                        title: 'Sedang memproses...',
                        text: 'Mohon tunggu, sistem sedang memproses',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        width: '700px',
                        didOpen: () => {
                            Swal.showLoading(); // animasi spinner loading
                        }
                    });

                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('ids', checkedIds);

                    $.ajax({
                        url: '{{ route('perpanjang.perpanjangmassal') }}',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            Swal.close(); // tutup loading
                            if (response.status == 200) {
                                Swal.fire({
                                    title: "Berhasil 🚀",
                                    html: response.message,
                                    icon: response.icon,
                                    timer: 5000,
                                    showConfirmButton: false
                                });

                                table.ajax.reload()

                                $('#checkAll').prop('checked', false);
                            } else {
                                Swal.fire({
                                    title: "Gagal",
                                    text: response.message,
                                    icon: response.icon
                                });
                            }
                        },
                        error: function() {
                            Swal.close(); // tutup loading jika error
                            Swal.fire({
                                title: "Error",
                                text: "Terjadi kesalahan saat mengirim pesan. Coba lagi nanti.",
                                icon: "error"
                            });
                        }
                    });
                }
            });
        }
    </script>
@endpush
