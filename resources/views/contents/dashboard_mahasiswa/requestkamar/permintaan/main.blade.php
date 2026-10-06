@extends('layouts.main')

@section('contents')
    <div class="container-fluid">
        <h1 class="mt-4">{{ $judul }}</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dasbor') }}">Kembali</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $judul }}</li>
        </ol>

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
                        <table class="table m-0" id="datatablepermintaankamar" style="width: 100%">
                            <thead class="bg-dark text-light">
                                <tr>
                                    <th scope="col" width="50"></th>
                                    <th scope="col">TANGGAL PERMINTAN</th>
                                    <th scope="col">NAMA</th>
                                    <th scope="col">NIM</th>
                                    <th scope="col">BILL TO</th>
                                    <th scope="col">NO REQUEST</th>
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
            table = $("#datatablepermintaankamar").DataTable({
                processing: true,
                paging: false,
                ajax: {
                    url: "{{ route('pengguna.permintaankamar.datatablepermintaankamar') }}",
                    type: "POST",
                    // dataSrc: ""
                    dataType: "json",
                    data: function(d) {
                        d.penyewa_id = "{{ $penyewa->id }}"
                    },
                },
                columns: [{
                        data: "aksi",
                    },
                    {
                        data: "tanggal_permintaan",
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
                        data: "no_request",
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
                        data: "piutang",
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
        });

        function onRefresh() {
            table.ajax.reload()
        }

        async function openModalPay(penyewa_id, no_invoice, total_tagihan) {
            $("#universalModalContent").addClass("modal-lg")

            // metode pembayaran
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

            // deposit
            let depositOptions = `<option value="">Pilih Deposit</option>`;
            try {
                const response = await $.ajax({
                    url: `/deposit/get/${penyewa_id}`,
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                response.forEach(item => {
                    depositOptions += `
                        <option value="${item.id}" data-saldo="${item.saldo}">
                            No Kuitansi ${item.no_transaksi} | Saldo: Rp ${Number(item.saldo).toLocaleString('id-ID')}
                        </option>
                    `;
                });
            } catch (xhr) {
                console.error(xhr.status);
                console.error(xhr.responseText);
            }

            $("#universalModalContent").html(`
            <form class="modal-content" autocomplete="off" onsubmit="requestBayar(event)" id="formbayar" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="mb-3">
                        <tbody>
                            <tr>
                                <td>NO REQUEST</td>
                                <td width="20" class="text-right">:</td>
                                <td>${no_invoice}</td>
                            </tr>
                            <tr>
                                <td>TOTAL PIUTANG</td>
                                <td width="20" class="text-right">:</td>
                                <td>
                                    RP. ${Number(total_tagihan).toLocaleString('id-ID')}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <input type="hidden" name="no_invoice" value="${no_invoice}">
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
                        <label for="file_bukti" class="form-label fw-bold">File Bukti <sup class="text-danger">(PDF, JPG, JPEG, PNG)</sup></label>
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

        function requestBayar(e) {
            e.preventDefault();

            let tanggal_bayar = $("#tanggal_bayar").val();
            let jumlah_uang_raw = $("#jumlah_uang").val();

            // hapus titik (format rupiah)
            let jumlah_uang = parseInt(jumlah_uang_raw.replace(/\./g, '')) || 0;

            let fileInput = $('#file_bukti')[0];
            let file_bukti = fileInput.files[0];

            let metode_pembayaran = $("input[name='metode_pembayaran']:checked");

            let isValid = true;

            // reset error dulu
            $('#tanggal_bayar').removeClass('is-invalid');
            $('#jumlah_uang').removeClass('is-invalid');
            $('#file_bukti').removeClass('is-invalid');
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

            if (file_bukti) {
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

            var formData = new FormData($("#formbayar")[0]);
            formData.append('_token', '{{ csrf_token() }}')

            $.ajax({
                url: "{{ route('pengguna.permintaankamar.bayar') }}",
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

                        // var pdfUrl = `{{ url('transaksi/kwitansi/') }}/${response.no_transaksi}`;

                        // // set iframe src ke PDF kwitansi
                        // $("#iframeCetakKwitansi").attr("src", pdfUrl);

                        // // tampilkan modal popup
                        // $("#modalCetakKwitansi").modal("show");

                        // // ✅ reload setelah modal ditutup
                        // $("#modalCetakKwitansi").on("hidden.bs.modal", function() {
                        //     table.ajax.reload()
                        // });
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
