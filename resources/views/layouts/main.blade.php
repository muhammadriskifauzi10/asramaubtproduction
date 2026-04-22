<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="cache-control" content="no-cache, must-revalidate, post-check=0, pre-check=0">
    <meta http-equiv="expires" content="0">
    <meta http-equiv="pragma" content="no-cache">
    <title>{{ isset($judul) ? 'Asrama UBT | ' . $judul : config('app.name') }}</title>

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

    {{-- Sweetalert --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('css/sweetalert2.min.css') }}">

    {{-- My style --}}
    <link rel="stylesheet" href="{{ asset('css/universal.css') }}" />

    {{-- toastify css --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    {{-- icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    @yield('mystyles')
</head>

<body style="background-color: #eef0f8; position: relative;">

    {{-- @if (!request()->is('scan*')) --}}
    @include('layouts.navbar')

    @if (auth()->user()->role_id == 8 || auth()->user()->role_id == 0)
    @else
        {{-- @endif --}}
        <main>
            @yield('contents')
        </main>
    @endif

    <!-- Modal Universal -->
    <div class="modal fade" id="universalModal" tabindex="-1" aria-labelledby="universalModalLabel" aria-hidden="true">
        <div class="modal-dialog" id="universalModalContent">
        </div>
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

    {{-- Money JS --}}
    <script src="{{ asset('js/money.js') }}"></script>

    {{-- Money JS --}}
    <script src="{{ asset('js/money2.js') }}"></script>

    {{-- Universal JS --}}
    <script src="{{ asset('js/universal.js') }}"></script>

    {{-- Sweetalert JS --}}
    <script src="{{ asset('js/sweetalert2.min.js') }}"></script>

    {{-- toastify js --}}
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();

            flatpickr(".tanggal_flat", {
                dateFormat: "d/m/Y",
            });
        });

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(".form-select-2").select2({
                theme: "bootstrap-5",
                // selectionCssClass: "select2--small",
                // dropdownCssClass: "select2--small",
            });

            // Money
            $('.formatrupiah').maskMoney({
                allowNegative: false,
                precision: 0,
                thousands: '.'
            });

            // rupiah
            $('.rupiah').each(function() {
                new AutoNumeric(this, {
                    digitGroupSeparator: '.', // Pemisah ribuan
                    decimalCharacter: ',', // Pemisah desimal
                    currencySymbol: '', // Tidak ada simbol mata uang
                    decimalPlaces: 0, // Tidak ada desimal
                });
            });
        })

        // Session Success
        let existsMessageSuccess = "{{ Session::has('messageSuccess') }}";
        let getMessageSuccess = "{{ Session::get('messageSuccess') }}";
        if (existsMessageSuccess) {
            Swal.fire({
                title: "Success",
                text: getMessageSuccess,
                icon: "success"
            })
        }

        // Session Info
        let existsMessageInfo = "{{ Session::has('messageInfo') }}";
        let getMessageInfo = "{{ Session::get('messageInfo') }}";
        if (existsMessageInfo) {
            Swal.fire({
                title: "Info",
                text: getMessageInfo,
                icon: "info"
            })
        }

        // Session Failed
        let existsMessageFailed = "{{ Session::has('messageFailed') }}";
        let getMessageFailed = "{{ Session::get('messageFailed') }}";
        if (existsMessageFailed) {
            Swal.fire({
                title: "Failed",
                text: getMessageFailed,
                icon: "error"
            })
        }
    </script>
    @stack('myscripts')
</body>

</html>
