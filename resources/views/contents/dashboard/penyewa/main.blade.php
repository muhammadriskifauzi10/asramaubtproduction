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
                    {{-- asrama --}}
                    <div class="col-xl-3 mb-3">
                        <label for="asrama" class="form-label fw-bold">Asrama</label>
                        <select class="form-select form-select-2" name="asrama" id="asrama" style="width: 100%;">
                            <option value="">Filter Asrama Aktif</option>
                            <option value="1" selected>Aktif</option>
                            <option value="0">Tidak Aktif</option>
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
            <button type="button" class="btn btn-danger" onclick="onSinkron()">
                <i class="fa-solid fa-arrows-rotate me-1"></i>
                Sinkronisasikan Data
            </button>
        </div>

        <div class="card mb-4 border-0" style="background-color: rgb(227 255 230)">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-xl-12">
                        <table class="table m-0" id="datatablepenyewa" style="width: 100%">
                            <thead class="bg-dark text-light">
                                <tr>
                                    <th scope="col" width="50"></th>
                                    <th scope="col">ANGKATAN</th>
                                    <th scope="col">NAMA LENGKAP</th>
                                    <th scope="col">NIM</th>
                                    <th scope="col">BILL TO</th>
                                    <th scope="col">NO KTP</th>
                                    <th scope="col">NO HP</th>
                                    <th scope="col">EMAIL</th>
                                    <th scope="col">JENIS KELAMIN</th>
                                    <th scope="col">ALAMAT</th>
                                    <th scope="col">STATUS ASRAMA</th>
                                    <th scope="col">STATUS CATERING</th>
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
            table = $("#datatablepenyewa").DataTable({
                processing: true,
                paging: false,
                ajax: {
                    url: "{{ route('penyewa.datatablepenyewa') }}",
                    type: "POST",
                    // dataSrc: ""
                    dataType: "json",
                    data: function(d) {
                        d.asrama = $("#asrama").val();
                    },
                },
                columns: [{
                        data: "aksi",
                    },
                    {
                        data: "angkatan",
                    },
                    {
                        data: "namalengkap",
                    },
                    {
                        data: "nim",
                    },
                    {
                        data: "kip",
                    },
                    {
                        data: "noktp",
                    },
                    {
                        data: "nohp",
                    },
                    {
                        data: "email",
                    },
                    {
                        data: "jenis_kelamin",
                    },
                    {
                        data: "alamat",
                    },
                    {
                        data: "status_asrama",
                    },
                    {
                        data: "status_catering",
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

            $("#asrama").change(function() {
                table.ajax.reload();
            });
        });

        function onRefresh() {
            table.ajax.reload()
        }

        function onSinkron() {
            $("#universalModalContent").html(`
                <form class="modal-content" autocomplete="off" onsubmit="requestSinkron(event)" id="formsinkron">
                    <div class="modal-header">
                        <h5 class="modal-title">Sinkronisasikan Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <div class="mb-3">
                            <label for="api" class="fw-bold">Jenis API</label>
                            <select class="form-select" name="api" id="api" style="width: 100%">
                                <option value="">Pilih Jenis API</option>
                                @foreach (\App\Models\Linkapi::where('jenis', 'penyewa')->get() as $row)
                                    <option value="{{ $row->id }}">
                                        Nama API: {{ $row->nama }} | kategori: {{ $row->kategori }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <button type="submit" class="btn btn-success w-100" id="btn-submit">
                                <i class="fa fa-paper-plane me-1"></i> Simpan
                            </button>
                        </div>
                    </div>
                </form>
            `);


            $(".form-select").select2({
                dropdownParent: $("#universalModal"),
                theme: "bootstrap-5",
            });

            $("#universalModal").modal("show");
        }

        function requestSinkron(e) {
            e.preventDefault();

            if ($('#api').val() == '') {
                Swal.fire({
                    icon: 'info',
                    text: 'Jenis Api wajib dipilih!'
                });

                return
            }


            var data = new FormData($("#formsinkron")[0])

            $.ajax({
                url: "{{ route('penyewa.singkron') }}",
                type: "POST",
                data: data,
                processData: false,
                contentType: false,
                beforeSend: function() {

                    // 🔥 Loading SweetAlert
                    Swal.fire({
                        title: 'Loading...',
                        text: 'Sedang proses sinkronisasi',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $("#btn-submit").prop("disabled", true)
                    $("#btn-submit").html(`
                        <div class="spinner-border spinner-border-sm text-light" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    `)
                },
                success: function(response) {
                    Swal.close(); // 🔥 tutup loading dulu

                    if (response.status == 200) {
                        Swal.fire({
                            title: "Success",
                            icon: response.icon,
                            text: response.message,
                            timer: 5000,
                            showConfirmButton: false
                        });

                        $("#universalModal").modal("hide");

                        table.ajax.reload()
                    } else {
                        Swal.fire({
                            icon: response.icon,
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    Swal.close(); // 🔥 tutup loading dulu

                    Swal.fire({
                        icon: "error",
                        text: "Terjadi kesalahan!"
                    });
                },
                complete: function() {
                    $("#btn-submit").html(`
                        <i class="fa fa-paper-plane me-1"></i> Simpan
                    `).prop("disabled", false);
                }
            });
        }

        function onHentikanAsrama(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: `Apakah Anda benar-benar ingin hentikan asrama?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hentikan!',
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

                    $.ajax({
                        url: '{{ route('penyewa.hentikanasrama') }}',
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

        function onHentikanCatering(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: `Apakah Anda benar-benar ingin hentikan catering?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hentikan!',
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

                    $.ajax({
                        url: '{{ route('penyewa.hentikancatering') }}',
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
