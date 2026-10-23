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
            <a href="{{ route('tarifpendaftaran.tambah') }}" class="btn btn-dark">
                <i class="fa fa-plus me-1"></i>
                {{ $judul }}
            </a>
        </div>
        <div class="card mb-4 border-0" style="background-color: rgb(227 255 230)">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-xl-12">
                        <table class="table m-0" id="datatabletarifpendaftaran" style="width: 100%">
                            <thead class="bg-dark text-light">
                                <tr>
                                    <th scope="col" width="50"></th>
                                    <th scope="col">NAMA</th>
                                    <th scope="col">JENIS</th>
                                    <th scope="col">KATEGORI</th>
                                    <th scope="col">NOMINAL</th>
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
            table = $("#datatabletarifpendaftaran").DataTable({
                processing: true,
                paging: false,
                ajax: {
                    url: "{{ route('tarifpendaftaran.datatabletarifpendaftaran') }}",
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
                        data: "jenis",
                    },
                    {
                        data: "kategori",
                    },
                    {
                        data: "nominal",
                    },
                    {
                        data: "status_tarif",
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
            });
        });

        function onRefresh() {
            table.ajax.reload()
        }

        function requestToggle(id, deskripsi, status) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: deskripsi,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya!',
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
                    formData.append('id', id);
                    formData.append('status', status);

                    $.ajax({
                        url: '{{ route('tarifpendaftaran.toggle') }}',
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
