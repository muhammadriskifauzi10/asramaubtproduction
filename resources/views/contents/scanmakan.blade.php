<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Makan Mahasiswa</title>
    {{-- Bootstrap 5 CSS --}}
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />

    {{-- Bootstrap CSS Select 2 --}}
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />

    {{-- Datatable CSS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

    {{-- Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Sweetalert --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('css/sweetalert2.min.css') }}">

    {{-- My style --}}
    <link rel="stylesheet" href="{{ asset('css/universal.css') }}" />

    <style>
        body {
            background: linear-gradient(to right, #2e7d32, #43a047, #66bb6a, #26a69a);
        }
    </style>
</head>

<body>
    <div class="container-fluid mt-3 mb-5">
        <div class="row">
            <div class="col-xl-7 mb-3 order-2 order-xl-1">
                <div class="card shadow border-0">
                    <div class="card-header text-dark fw-bold text-center">
                        Daftar Absensi Makan
                    </div>
                    <div class="card-body">
                        <h6 class="mt-3">Link scan makan</h6>
                        <a href="https://as.ubtsu.ac.id/scanmakan" target="_blank">Scan Makan Keseluruhan</a> |
                        <a href="https://as.ubtsu.ac.id/scanmakan?waktu_makan=pagi" target="_blank">Scan Makan
                            Keseluruhan Pagi</a> |
                        <a href="https://as.ubtsu.ac.id/scanmakan?waktu_makan=siang" target="_blank">Scan Makan
                            Keseluruhan Siang</a> |
                        <a href="https://as.ubtsu.ac.id/scanmakan?waktu_makan=malam" target="_blank">Scan Makan
                            Keseluruhan Malam</a>

                        <h6 class="mt-3">Link scan makan Laki-laki</h6>
                        <a href="https://as.ubtsu.ac.id/scanmakan?jenis_kelamin=l" target="_blank">Laki-laki</a> |
                        <a href="https://as.ubtsu.ac.id/scanmakan?jenis_kelamin=l&waktu_makan=pagi"
                            target="_blank">Laki-laki Pagi</a> |
                        <a href="https://as.ubtsu.ac.id/scanmakan?jenis_kelamin=l&waktu_makan=siang"
                            target="_blank">Laki-laki Siang</a> |
                        <a href="https://as.ubtsu.ac.id/scanmakan?jenis_kelamin=l&waktu_makan=malam"
                            target="_blank">Laki-laki Malam</a>

                        <h6 class="mt-3">Link Scan Makan Perempuan</h6>
                        <a href="https://as.ubtsu.ac.id/scanmakan?jenis_kelamin=p" target="_blank">Perempuan</a> |
                        <a href="https://as.ubtsu.ac.id/scanmakan?jenis_kelamin=p&waktu_makan=pagi"
                            target="_blank">Perempuan Pagi</a> |
                        <a href="https://as.ubtsu.ac.id/scanmakan?jenis_kelamin=p&waktu_makan=siang"
                            target="_blank">Perempuan Siang</a> |
                        <a href="https://as.ubtsu.ac.id/scanmakan?jenis_kelamin=p&waktu_makan=malam"
                            target="_blank">Perempuan Malam</a>

                        <table class="table table-bordered table-hover border-0 m-0 not-va"
                            style="width: 100%; white-space: nowrap;" id="datatablescanmakan">
                            <thead>
                                <tr>
                                    <th scope="col" width="20">No</th>
                                    <th scope="col">Waktu Absensi</th>
                                    <th scope="col">NIM</th>
                                    <th scope="col">Nama</th>
                                    <th scope="col">Waktu Makan</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Bagian Kanan: Input Scan NIM -->
            <div class="col-xl-5 mb-3 order-1 order-xl-2">
                <div class="card shadow border-0">
                    <div class="card-body text-center">
                        <form onsubmit="return false;" autocomplete="off">
                            <div class="mb-3">
                                <label for="nim" class="form-label fw-semibold">Masukkan / Scan NIM</label>
                                <input type="text" id="nim" name="nim"
                                    class="form-control form-control-lg text-center p-3" placeholder="Scan NIM di sini"
                                    autofocus onchange="onScan()">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="position-fixed bottom-0 start-0 w-100 bg-dark text-white py-2 px-3" style="z-index: 3;">
        <marquee behavior="scroll" direction="left">
            Silakan scan QR Code NIM Anda untuk mengambil antrian makan. Mohon pastikan QR Code terlihat jelas agar
            proses berjalan lancar. Terima kasih.
        </marquee>
    </div>

    {{-- Jquery JS --}}
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

    {{-- Bootstrap JS --}}
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

    {{-- Bootstrap JS Select 2 --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>

    {{-- Datatable JS --}}
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>

    {{-- Sweetalert JS --}}
    <script src="{{ asset('js/sweetalert2.min.js') }}"></script>

    <script src="https://code.responsivevoice.org/responsivevoice.js?key=7menMdg3"></script>
    <script>
        var tablescanmakan
        $(document).ready(function() {
            tablescanmakan = $("#datatablescanmakan").DataTable({
                paging: false,
                processing: true,
                ajax: {
                    url: "{{ route('scanmakan.datatablescanmakan') }}",
                    type: "POST",
                    // dataSrc: ""
                    dataType: "json",
                    data: {
                        _token: "{{ csrf_token() }}",
                        jenis_kelamin: '{{ $jenis_kelamin }}',
                        waktu_makan: '{{ $waktu_makan }}',
                    },
                },
                columns: [{
                        data: "nomor",
                    },
                    {
                        data: "waktu_absensi",
                    },
                    {
                        data: "nim",
                    },
                    {
                        data: "nama_lengkap",
                    },
                    {
                        data: "waktu_makan",
                    },
                ],

                // "order": [
                //     [1, 'asc']
                // ],
                // scrollY: "700px",
                scrollX: true,
                dom: '<"top"i>rt<"bottom"p><"clear">',
                // scrollCollapse: true,
                // paging:         false,
                // fixedColumns: {
                //     left: 3,
                // }
            });
        });

        let isProcessing = false; // flag global

        function onScan() {
            if (isProcessing) {
                return; // kalau masih proses, hentikan
            }

            // if ($("#nim").val().length > 3) {
            isProcessing = true; // set sedang proses

            $.ajax({
                url: "{{ route('scanmakan.scan') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    nim: $("#nim").val(),
                },
                success: function(response) {
                    if (response.status === 200) {
                        Swal.fire({
                            title: "Success",
                            html: response.message,
                            icon: response.icon,
                            timer: 5000,
                            showConfirmButton: false
                        });

                        responsiveVoice.speak(response.message, "Indonesian Male");

                        $("#nim").val("");
                        tablescanmakan.ajax.reload();
                    } else {
                        Swal.fire({
                            text: response.message,
                            icon: response.icon
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        text: "Terjadi kesalahan server",
                        icon: "error"
                    });
                },
                complete: function() {
                    // apapun hasilnya, reset flag
                    isProcessing = false;
                }
            });
            // }
        }
    </script>
</body>

</html>
