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
                    {{-- status pembayaran --}}
                    <div class="col-xl-3 mb-3">
                        <label for="status_pembayaran" class="form-label fw-bold">Status Pembayaran</label>
                        <select class="form-select form-select-2" name="status_pembayaran" id="status_pembayaran"
                            style="width: 100%;">
                            <option value="">Filter Status Pembayaran</option>
                            <option value="failed">Failed</option>
                            <option value="pending" selected>Pending</option>
                            <option value="completed">Completed</option>
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
                        <table class="table m-0" id="datatabletagihan" style="width: 100%">
                            <thead class="bg-dark text-light">
                                <tr>
                                    <th scope="col" width="50"></th>
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
            table = $("#datatabletagihan").DataTable({
                processing: true,
                paging: false,
                ajax: {
                    url: "{{ route('tagihan.datatabletagihan') }}",
                    type: "POST",
                    // dataSrc: ""
                    dataType: "json",
                    data: function(d) {
                        d.dari_tanggal = $("#dari_tanggal").val();
                        d.sampai_tanggal = $("#sampai_tanggal").val();
                        d.penyewa = $("#penyewa").val();
                        d.status_pembayaran = $("#status_pembayaran").val();
                    },
                },
                columns: [{
                        data: "aksi",
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

            $("#dari_tanggal, #sampai_tanggal, #penyewa, #status_pembayaran").change(function() {
                table.ajax.reload();
            });
        });

        function onRefresh() {
            table.ajax.reload()
        }

        async function openModalPay(no_invoice) {
            // ambil data bank dari API
            let banks = [];
            try {
                let res = await fetch('https://sia.ubtsu.ac.id/api/bank');
                banks = await res.json();
            } catch (error) {
                console.error('Gagal ambil data bank:', error);
            }

            const priorityId = 3;
            banks.sort((a, b) => {
                if (a.id === priorityId) return -1;
                if (b.id === priorityId) return 1;
                return 0;
            });

            let bankOptions = '';

            const defaultBankId = 3;
            banks.forEach((bank) => {
                bankOptions += `
                    <div class="form-check">
                        <input type="radio"
                            name="metode_pembayaran"
                            id="metode_pembayaran_${bank.id}"
                            value="${bank.name} - ${bank.account_name}"
                            ${bank.id === defaultBankId ? 'checked' : ''}>
                        <label class="form-check-label" for="metode_pembayaran_${bank.id}">
                            ${bank.name} - ${bank.account_number}
                            <br>
                            <small class="text-muted">${bank.account_name}</small>
                        </label>
                    </div>
                `;
            });

            $("#universalModalContent").html(`
                <form class="modal-content" autocomplete="off" onsubmit="requestBayar(event)" id="formbayar" enctype="multipart/formdata">
                    <div class="modal-header">
                        <h5 class="modal-title">Bayar No Invoice: ${no_invoice}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="no_invoice" value="${no_invoice}">
                        <div class="mb-3">
                            <label for="jumlah_uang" class="form-label fw-bold">Jumlah Uang <sup
                                    class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text bg-success text-light">RP</span>

                                <input type="text" name="jumlah_uang" id="jumlah_uang"
                                    class="form-control text-end bg-warning fw-bold"
                                    value="0">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="file_bukti" class="form-label fw-bold">File Bukti <sup class="text-danger">* (PDF, JPG, JPEG, PNG)</sup></label>
                            <input type="file" name="file_bukti" id="file_bukti" class="form-control">
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

            new AutoNumeric('#jumlah_uang', {
                digitGroupSeparator: '.',
                decimalCharacter: ',',
                decimalPlaces: 0,
                unformatOnSubmit: true
            });

            $("#universalModal").modal("show");
        }

        function requestBayar(e) {
            e.preventDefault();

            let jumlah_uang_raw = $("#jumlah_uang").val();

            // hapus titik (format rupiah)
            let jumlah_uang = parseInt(jumlah_uang_raw.replace(/\./g, '')) || 0;

            let fileInput = $('#file_bukti')[0];
            let file_bukti = fileInput.files[0];

            let isValid = true;

            // reset error dulu
            $('#jumlah_uang').removeClass('is-invalid');
            $('#file_bukti').removeClass('is-invalid');

            // validasi jumlah uang
            if (jumlah_uang <= 0) {
                $('#jumlah_uang').addClass('is-invalid');
                isValid = false;
            }

            // validasi file
            if (!file_bukti) {
                $('#file_bukti').addClass('is-invalid');
                isValid = false;
            } else {
                let allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
                let fileName = file_bukti.name.toLowerCase();
                let fileExtension = fileName.split('.').pop();

                if (!allowedExtensions.includes(fileExtension)) {
                    Swal.fire({
                        icon: "warning",
                        text: "Format file tidak valid! Hanya boleh (PDF, JPG, JPEG, PNG)!"
                    });

                    fileInput.value = '';
                    $('#file_bukti').addClass('is-invalid');
                    return;
                }
            }

            if (!isValid) {
                Swal.fire({
                    icon: "warning",
                    text: "Harap isi semua field sebelum menyimpan data!"
                });
                return;
            }

            var formData = new FormData($("#formbayar")[0]);
            formData.append('_token', '{{ csrf_token() }}')

            $.ajax({
                url: "{{ route('tagihan.bayar') }}",
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
    </script>
@endpush
