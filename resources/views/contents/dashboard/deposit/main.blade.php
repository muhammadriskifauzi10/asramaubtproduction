@extends('layouts.main')

@section('mystyles')
    <style>
        input[name="metode_pembayaran"] {
            appearance: none;
            -webkit-appearance: none;

            width: 16px;
            height: 16px;

            border: 2px solid var(--bs-secondary-color);
            border-radius: 50%;

            vertical-align: middle;
            position: relative;
            cursor: pointer;
        }

        /* Belum dipilih + validasi gagal */
        input[name="metode_pembayaran"].is-invalid {
            border-color: var(--bs-danger);
        }

        /* Dipilih */
        input[name="metode_pembayaran"]:checked {
            border-color: var(--bs-primary);
        }

        /* Titik tengah */
        input[name="metode_pembayaran"]:checked::after {
            content: "";
            position: absolute;

            width: 8px;
            height: 8px;

            background-color: var(--bs-primary);
            border-radius: 50%;

            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
@endsection

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
                    {{-- pilih penyewa --}}
                    <div class="col-xl-2 mb-3">
                        <label for="nim" class="form-label fw-bold">Penyewa</label>
                        <select class="form-select form-select-2" name="nim" id="nim" style="width: 100%;">
                            <option value="">Filter Penyewa</option>
                            @foreach (\App\Models\Penyewa::select('namalengkap', 'nim')->distinct()->get() as $row)
                                <option value="{{ $row->nim }}">Nama lengkap:
                                    {{ $row->namalengkap }} | NIM: {{ $row->nim }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- status pembayaran --}}
                    <div class="col-xl-2 mb-3">
                        <label for="metode_pembayaran" class="form-label fw-bold">Metode Pembayaran</label>
                        <select class="form-select form-select-2" name="metode_pembayaran" id="metode_pembayaran"
                            style="width: 100%;">
                            <option value="">Filter Metode Pembayaran</option>
                            @foreach (\App\Models\Deposit::select('metode_pembayaran')->distinct()->get() as $row)
                                <option value="{{ $row->metode_pembayaran }}">{{ $row->metode_pembayaran }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- status --}}
                    <div class="col-xl-2 mb-3">
                        <label for="status" class="form-label fw-bold">Status</label>
                        <select class="form-select form-select-2" name="status" id="status" style="width: 100%;">
                            <option value="">Filter Status</option>
                            <option value="0">Habis</option>
                            <option value="1" selected>Aktif</option>
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
            <a href="{{ route('deposit.tambah') }}" class="btn btn-dark">
                <i class="fa fa-plus me-1"></i>
                {{ $judul }}
            </a>
        </div>
        <div class="card mb-4 border-0" style="background-color: rgb(227 255 230)">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-xl-12">
                        <table class="table m-0" id="datatabledeposit" style="width: 100%">
                            <thead class="bg-dark text-light">
                                <tr>
                                    <th scope="col" width="50"></th>
                                    <th scope="col">NO KUITANSI</th>
                                    <th scope="col">TANGGAL PEMBAYARAN</th>
                                    <th scope="col">NAMA LENGKAP</th>
                                    <th scope="col">NIM</th>
                                    <th scope="col">NAMA BILL TO</th>
                                    <th scope="col">JUMLAH UANG</th>
                                    <th scope="col">SALDO</th>
                                    <th scope="col">METODE PEMBAYARAN</th>
                                    <th scope="col">TANGGAL REFERENSI BAYAR</th>
                                    <th scope="col">STATUS</th>
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
            table = $("#datatabledeposit").DataTable({
                processing: true,
                paging: false,
                ajax: {
                    url: "{{ route('deposit.datatabledeposit') }}",
                    type: "POST",
                    // dataSrc: ""
                    dataType: "json",
                    data: function(d) {
                        d.dari_tanggal = $("#dari_tanggal").val();
                        d.sampai_tanggal = $("#sampai_tanggal").val();
                        d.nim = $("#nim").val();
                        d.metode_pembayaran = $("#metode_pembayaran").val();
                        d.status = $("#status").val();
                    },
                },
                columns: [{
                        data: "aksi",
                    },
                    {
                        data: "no_transaksi",
                    },
                    {
                        data: "tanggal_transaksi",
                    },
                    {
                        data: "namalengkap",
                    },
                    {
                        data: "nim",
                    },
                    {
                        data: "nama_bill_to",
                    },
                    {
                        data: "jumlah_uang",
                    },
                    {
                        data: "saldo",
                    },
                    {
                        data: "metode_pembayaran",
                    },
                    {
                        data: "tanggal_referensi_bayar",
                    },
                    {
                        data: "status",
                    },
                    {
                        data: "operator",
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

            $("#dari_tanggal, #sampai_tanggal, #nim, #metode_pembayaran, #status").change(function() {
                table.ajax.reload();
            });
        });

        function onRefresh() {
            table.ajax.reload()
        }

        async function openModalRefund(id, no_refund) {
            const defaultBankId = 2;
            let banks = [];

            try {
                const res = await fetch('https://sia.ubtsu.ac.id/api/bank');
                banks = await res.json();

                // Hanya tampilkan bank id = 2
                banks = banks.filter(bank => bank.id == 2);

            } catch (error) {
                console.error('Gagal ambil data bank:', error);

                $("#bank-list").html(`
                    <div class="alert alert-danger mb-0">
                        Gagal memuat daftar bank.
                    </div>
                `);

                return;
            }

            let bankOptions = "";
            banks.forEach((bank) => {
                bankOptions += `
                    <div class="form-check mb-3">
                        <input type="radio"
                            name="metode_pembayaran"
                            id="metode_pembayaran_0"
                            value="Cash">
                        <label class="form-check-label" for="metode_pembayaran_0">
                            Cash
                        </label>
                    </div>
                    <div class="form-check">
                        <input type="radio"
                            name="metode_pembayaran"
                            id="metode_pembayaran_${bank.id}"
                            value="${bank.name} - ${bank.account_name}">
                        <label class="form-check-label" for="metode_pembayaran_${bank.id}">
                            ${bank.name} - ${bank.account_number}
                            <br>
                            <small class="text-muted">${bank.account_name}</small>
                        </label>
                    </div>
                `;
            });

            $("#universalModalContent").html(`
            <form class="modal-content" autocomplete="off" onsubmit="requestRefund(event)" id="formrefund">
                <div class="modal-header">
                    <h5 class="modal-title">Refund No Deposit ${no_refund}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" value="${id}">

                    <div class="mb-3">
                        <label for="tanggal_bayar" class="form-label fw-bold">Tanggal Referensi Bayar <sup class="text-danger">*</sup></label>
                        <input type="text" name="tanggal_bayar" id="tanggal_bayar" class="form-control tanggal_flat">
                    </div>
                    <div class="mb-3">
                        <label for="jumlah_uang" class="form-label fw-bold">Jumlah Uang <sup class="text-danger">*</sup></label>
                        <div class="input-group">
                            <span class="input-group-text bg-success text-light">RP</span>

                            <input type="text" name="jumlah_uang" id="jumlah_uang"
                                class="form-control text-end bg-warning fw-bold"
                                value="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            Metode Pembayaran <sup class="text-danger">*</sup>
                        </label>

                        <div id="bank-list">
                            ${bankOptions}
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-end">
                        <button type="submit" class="btn btn-success" id="btn-submit">
                            <i class="fa fa-paper-plane me-1"></i> Simpan
                        </button>
                    </div>
                </div>
            </form>
            `);

            flatpickr(".tanggal_flat", {
                enableTime: true,
                dateFormat: "d/m/Y H:i",
                maxDate: "today"
            });

            new AutoNumeric('#jumlah_uang', {
                digitGroupSeparator: '.',
                decimalCharacter: ',',
                decimalPlaces: 0,
                unformatOnSubmit: true
            });

            $("#universalModal").modal("show");
        }

        function requestRefund(e) {
            e.preventDefault();

            let tanggal_bayar = $("#tanggal_bayar").val();
            let jumlah_uang_raw = $("#jumlah_uang").val();

            // hapus titik (format rupiah)
            let jumlah_uang = parseInt(jumlah_uang_raw.replace(/\./g, '')) || 0;

            let metode_pembayaran = $("input[name='metode_pembayaran']:checked");

            let isValid = true;

            // reset error dulu
            $('#tanggal_bayar').removeClass('is-invalid');
            $('#jumlah_uang').removeClass('is-invalid');
            $("input[name='metode_pembayaran']").removeClass('is-invalid');

            // validasi tanggal bayar
            if (tanggal_bayar == '') {
                $('.tanggal_flat').addClass('is-invalid');
                isValid = false;
            }

            // validasi jumlah uang
            if (jumlah_uang <= 0) {
                $('#jumlah_uang').addClass('is-invalid');
                isValid = false;
            }

            // validasi metode pembayaran
            if (metode_pembayaran.length === 0) {
                $("input[name='metode_pembayaran']").addClass('is-invalid');
                isValid = false;
            }

            if (!isValid) {
                Swal.fire({
                    icon: "warning",
                    text: "Harap isi field sebelum menyimpan data!"
                });
                return;
            }

            var formData = new FormData($("#formrefund")[0]);
            formData.append('_token', '{{ csrf_token() }}')

            $.ajax({
                url: "{{ route('deposit.refund') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $("#btn-submit").html(`
                        <div class="spinner-border spinner-border-sm text-light" role="status"></div>
                    `).prop("disabled", true);
                },
                success: function(response) {
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
