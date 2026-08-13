@extends('layouts.main')

@section('contents')
    <div class="container-fluid">
        <h1 class="mt-4">{{ $judul }}</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="javascript:history.back()">Kembali</a></li>
            <li class="breadcrumb-item active">{{ $judul }}</li>
        </ol>

        @if ($tagihan->status_pembayaran == 'pending')
            @php
                $piutang = $tagihan->total_tagihan - $tagihan->total_potongan_harga - $tagihan->total_bayar;
            @endphp
            <div class="my-2 d-flex align-content-center justify-content-end gap-2">
                <a href="javascript:void(0)" class="btn btn-success fw-bold d-flex align-items-center justify-content-center"
                    data-bs-toggle="tooltip" title="Bayar" style="width: 100px;"
                    onclick="openModalPay({{ $tagihan->penyewa->nim }}, '{{ $tagihan->no_invoice }}', {{ $piutang }})">
                    <i class="fa fa-credit-card me-1"></i> Bayar
                </a>
            </div>
        @endif

        <div class="card mb-4 border-0" style="background-color: rgb(255 227 248)">
            <div class="card-body">
                <div class="row justify-content-start">
                    <div class="col-xl-4 mb-3">
                        <table class="50">
                            <tbody>
                                <tr>
                                    <td>NO INVOICE</td>
                                    <td width="20" class="text-right">:</td>
                                    <td>{{ $tagihan->no_invoice }}</td>
                                </tr>
                                <tr>
                                    <td>NAMA</td>
                                    <td width="20" class="text-right">:</td>
                                    <td>{{ $tagihan->penyewa->namalengkap }}</td>
                                </tr>
                                <tr>
                                    <td>NIM</td>
                                    <td width="20" class="text-right">:</td>
                                    <td>{{ $tagihan->penyewa->nim }}</td>
                                </tr>
                                <tr>
                                    <td>BILL TO</td>
                                    <td width="20" class="text-right">:</td>
                                    <td>{{ $tagihan->nama_bill_to }}</td>
                                </tr>
                                <tr>
                                    <td>TIPE ASRAMA</td>
                                    <td width="20" class="text-right">:</td>
                                    <td>{{ $tagihan->kamar->type->nama ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td>LANTAI</td>
                                    <td width="20" class="text-right">:</td>
                                    <td>{{ $tagihan->kamar->lantai }}</td>
                                </tr>
                                <tr>
                                    <td>KAMAR</td>
                                    <td width="20" class="text-right">:</td>
                                    <td>{{ $tagihan->kamar->nomor_kamar }}</td>
                                </tr>
                                <tr>
                                    <td>TANGGAL MASUK</td>
                                    <td width="20" class="text-right">:</td>
                                    <td>{{ \Carbon\Carbon::parse($tagihan->tanggal_masuk)->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <td>TANGGAL KELUAR</td>
                                    <td width="20" class="text-right">:</td>
                                    <td>{{ \Carbon\Carbon::parse($tagihan->tanggal_keluar)->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <td>DURASI</td>
                                    <td width="20" class="text-right">:</td>
                                    <td>{{ $tagihan->durasi }} Bulan</td>
                                </tr>
                                <tr>
                                    <td>TOTAL TAGIHAN</td>
                                    <td width="20" class="text-right">:</td>
                                    <td>RP. {{ number_format($tagihan->total_tagihan, 0, '.', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>TOTAL POTONGAN HARGA</td>
                                    <td width="20" class="text-right">:</td>
                                    <td>RP. {{ number_format($tagihan->total_potongan_harga, 0, '.', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>NET TAGIHAN</td>
                                    <td width="20" class="text-right">:</td>
                                    <td>RP.
                                        {{ number_format($tagihan->total_tagihan - $tagihan->total_potongan_harga, 0, '.', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>TOTAL BAYAR</td>
                                    <td width="20" class="text-right">:</td>
                                    <td>
                                        RP. {{ number_format($tagihan->total_bayar, 0, '.', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>PIUTANG</td>
                                    <td width="20" class="text-right">:</td>
                                    <td>
                                        @php
                                            $hutang =
                                                $tagihan->total_tagihan -
                                                $tagihan->total_potongan_harga -
                                                $tagihan->total_bayar;
                                        @endphp
                                        RP. {{ number_format($hutang, 0, '.', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>STATUS PEMBAYARAN</td>
                                    <td width="20" class="text-right">:</td>
                                    <td>
                                        @php
                                            if ($tagihan->status_pembayaran == 'completed') {
                                                echo '<span class="badge bg-success">Lunas</span>';
                                            } elseif ($tagihan->status_pembayaran == 'pending') {
                                                echo '<span class="badge bg-warning text-dark">Belum Lunas</span>';
                                            } else {
                                                echo '<span class="badge bg-danger">Batal</span>';
                                            }
                                        @endphp
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-xl-8 mb-3">
                        {{-- potongan harga --}}
                        <div class="table-responsive mb-3">
                            <h5 class="mb-3">Potongan Harga</h5>
                            <table class="table m-0" style="width: 100%">
                                <thead class="bg-dark text-light">
                                    <tr>
                                        <th scope="col">POTONGAN HARGA</th>
                                        <th scope="col">TANGGAL DIBUAT</th>
                                        <th scope="col">OPERATOR</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @forelse (\App\Models\Potonganharga::where('no_invoice', $tagihan->no_invoice)->get() as $row)
                                        <tr>
                                            <td>RP. {{ number_format($row->potongan_harga, 0, '.', '.') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($row->created_at)->format('Y-m-d H:i') }}
                                            </td>
                                            <td>{{ $row->user->name }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3">Belum ada Potongan Harga</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- deposit pembayaran --}}
                        <div class="table-responsive mb-3">
                            <h5 class="mb-3">Deposit</h5>
                            <table class="table m-0" style="width: 100%">
                                <thead class="bg-dark text-light">
                                    <tr>
                                        <th scope="col" width="50"></th>
                                        <th scope="col">NO KUITANSI</th>
                                        <th scope="col">TANGGAL PENGGUNAAN</th>
                                        <th scope="col">JUMLAH DIGUNAKAN</th>
                                        <th scope="col">METODE PEMBAYARAN</th>
                                        <th scope="col">OPERATOR</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @forelse (\App\Models\Depositpembayaran::where('no_invoice', '260813-001')->orderBy('created_at', 'DESC')->get() as $row)
                                        <tr>
                                            <td>
                                                @if ($tagihan->status_pembayaran == 'pending')
                                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                                        <button type="button"
                                                            class="btn btn-primary d-flex align-items-center justify-content-center"
                                                            data-bs-toggle="tooltip" title="Kembalikan Saldo"
                                                            onclick="openModalMoveSaldo({{ $row->id }})">
                                                            <i class="fa fa-rotate-left"></i>
                                                        </button>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ $row->deposit->no_transaksi }}</td>
                                            <td>{{ \Carbon\Carbon::parse($row->created_at)->format('Y-m-d H:i') }}
                                            </td>
                                            <td>RP. {{ number_format($row->jumlah_digunakan, 0, '.', '.') }}</td>
                                            <td>{{ $row->no_invoice }}</td>
                                            <td>{{ $row->user->name }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6">Belum ada Penggunaan Deposit</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- pembayaran --}}
                        <div class="table-responsive">
                            <h5 class="mb-3">Pembayaran</h5>
                            <table class="table m-0" style="width: 100%">
                                <thead class="bg-dark text-light">
                                    <tr>
                                        <th scope="col" width="50"></th>
                                        <th scope="col">JENIS PEMBAYARAN</th>
                                        <th scope="col">NO KUITANSI</th>
                                        <th scope="col">TANGGAL PEMBAYARAN</th>
                                        <th scope="col">JUMLAH UANG</th>
                                        <th scope="col">METODE PEMBAYARAN</th>
                                        <th scope="col">TANGGAL REFERENSI BAYAR</th>
                                        <th scope="col">FILE BUKTI</th>
                                        <th scope="col">OPERATOR</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @forelse (\App\Models\Transaksi::where('no_invoice', $tagihan->no_invoice)
                                                                                                     ->orderByRaw("
                                                                                                            COALESCE(
                                                                                                                (SELECT parent.no_transaksi
                                                                                                                    FROM transaksi AS parent
                                                                                                                    WHERE parent.id = transaksi.parent_id)
    ,
                                                                                                                transaksi.no_transaksi
                                                                                                            ) DESC
                                                                                                        ")
                                                                                                        ->orderByRaw("
                                                                                                            CASE
                                                                                                                WHEN transaksi.parent_id IS NULL THEN 0
                                                                                                                ELSE 1
                                                                                                            END ASC
                                                                                                        ")
                                                                                                        ->orderBy('created_at', 'DESC')
                                                                                                        ->get() as $row)
                                        @php
                                            $refund = \App\Models\Transaksi::where('parent_id', $row->id)->sum(
                                                'jumlah_uang',
                                            );

                                            $sisa_uang_transaksi = $row->jumlah_uang + $refund;
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    @if ($tagihan->status_pembayaran == 'pending' && $row->jenis_transaksi == 'Asrama' && $sisa_uang_transaksi > 0)
                                                        <button type="button"
                                                            class="btn btn-primary d-flex align-items-center justify-content-center"
                                                            data-bs-toggle="tooltip" title="Refund Pembayaran"
                                                            onclick="openModalRefund({{ $row->id }}, '{{ $row->no_transaksi }}')">
                                                            <i class="fa fa-rotate-left"></i>
                                                        </button>
                                                    @endif

                                                    @if ($row->jenis_transaksi == 'Asrama')
                                                        <a href="{{ route('transaksi.kwitansi', encrypt($row->no_transaksi)) }}"
                                                            class="btn btn-success fw-bold d-flex align-items-center justify-content-center"
                                                            data-bs-toggle="tooltip" title="Cetak Kwitansi"
                                                            style="width: 40px;" target="_blank">
                                                            <i class="fa fa-receipt"></i>
                                                        </a>
                                                    @elseif ($row->jenis_transaksi == 'Refund')
                                                        <a href="{{ route('transaksi.refundkwitansi', encrypt($row->no_transaksi)) }}"
                                                            class="btn btn-success fw-bold d-flex align-items-center justify-content-center"
                                                            data-bs-toggle="tooltip" title="Cetak Refund"
                                                            style="width: 40px;" target="_blank">
                                                            <i class="fa fa-receipt"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>{{ $row->jenis_transaksi }}</td>
                                            <td>{{ $row->no_transaksi }}</td>
                                            <td>{{ \Carbon\Carbon::parse($row->created_at)->format('Y-m-d H:i') }}
                                            </td>
                                            <td>
                                                @if ($sisa_uang_transaksi == 0)
                                                    <span
                                                        style="text-decoration: line-through; color: red; font-weight: bold">
                                                        RP. {{ number_format($row->jumlah_uang, 0, '.', '.') }}
                                                    </span>
                                                @else
                                                    @if ($row->jenis_transaksi == 'Refund')
                                                        <span style="color: red">
                                                            RP. {{ number_format($row->jumlah_uang, 0, '.', '.') }}
                                                        </span>
                                                    @else
                                                        <span>
                                                            RP. {{ number_format($row->jumlah_uang, 0, '.', '.') }}
                                                        </span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>{{ $row->metode_pembayaran }}</td>
                                            <td>{{ \Carbon\Carbon::parse($row->tanggal_transaksi)->format('Y-m-d H:i') }}
                                            <td>
                                                @if ($row->file_bukti)
                                                    <a href="{{ asset('img/bukti_pembayaran/' . $tagihan->no_invoice . '/' . $row->file_bukti) }}"
                                                        target="_blank"
                                                        class="text-primary text-decoration-none fw-bold">FILE
                                                        BUKTI</a>
                                                @endif
                                            </td>
                                            <td>{{ $row->user->name }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9">Belum ada Transaksi pembayaran</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 border-0" style="background-color: rgb(227 255 230)">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-xl-12">
                        <table class="table m-0" style="width: 100%">
                            <thead class="bg-dark text-light">
                                <tr>
                                    <th scope="col" width="50"></th>
                                    <th scope="col">JENIS SEWA</th>
                                    <th scope="col">NAMA TAGIHAN</th>
                                    <th scope="col">HARGA</th>
                                    <th scope="col">QTY</th>
                                    <th scope="col">JUMLAH TAGIHAN</th>
                                    <th scope="col">POTONGAN HARGA</th>
                                    <th scope="col">NET TAGIHAN</th>
                                    <th scope="col">STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tagihandetail as $row)
                                    <tr>
                                        <td>
                                            @if ($tagihan->status_pembayaran == 'pending' && $row->status == 1)
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    {{-- <button type="button"
                                                        class="btn btn-danger fw-bold d-flex align-items-center justify-content-center"
                                                        data-bs-toggle="tooltip" title="Batalkan" style="width: 40px;"
                                                        onclick="cancelItem('{{ $row->no_invoice }}', {{ $row->id }}, '{{ $row->jenissewa }}')">
                                                        <i class="fa fa-times"></i>
                                                    </button> --}}
                                                    <button type="button"
                                                        class="btn btn-warning fw-bold d-flex align-items-center justify-content-center"
                                                        data-bs-toggle="tooltip" title="Potongan Harga"
                                                        style="width: 40px;"
                                                        onclick="openModalPotonganHarga('{{ $row->no_invoice }}', {{ $row->id }}, '{{ $row->jenissewa }}')">
                                                        <i class="fa fa-percent"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $row->jenissewa }}</td>
                                        <td>{{ $row->hargas->nama_tagihan }}</td>
                                        <td>RP. {{ number_format($row->harga, 0, '.', '.') }}</td>
                                        <td>{{ $row->qty }}</td>
                                        <td>RP. {{ number_format($row->jumlah_pembayaran, 0, '.', '.') }}</td>
                                        <td>RP. {{ number_format($row->potongan_harga, 0, '.', '.') }}</td>
                                        <td>RP.
                                            {{ number_format($row->jumlah_pembayaran - $row->potongan_harga, 0, '.', '.') }}
                                        </td>
                                        <td>{{ $row->status == 1 ? 'AKTIF' : 'DIBATALKAN' }}</td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cetak Kwitansi -->
    <div class="modal fade" id="modalCetakKwitansi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kwitansi Pembayaran</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <iframe id="iframeCetakKwitansi" src="" width="100%" height="600px"
                        style="border:none;"></iframe>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscripts')
    <script>
        $(document).ready(function() {});

        // function cancelItem(no_invoice, id, kategori) {
        //     Swal.fire({
        //         title: "Batalkan Item?",
        //         text: `Yakin ingin batalkan Item ${kategori}?`,
        //         icon: "warning",
        //         showCancelButton: true,
        //         confirmButtonColor: '#25d366',
        //         cancelButtonColor: '#cc0000',
        //         confirmButtonText: 'Ya, batalkan!',
        //         cancelButtonText: 'Batal!'
        //     }).then((result) => {

        //         if (!result.isConfirmed) return;
        //         var formData = new FormData();
        //         formData.append("_token", '{{ csrf_token() }}');
        //         formData.append("no_invoice", no_invoice);
        //         formData.append("id", id);

        //         $.ajax({
        //             url: "{{ route('tagihan.cancelitem') }}",
        //             type: "POST",
        //             data: formData,
        //             processData: false,
        //             contentType: false,
        //             beforeSend: function() {
        //                 Swal.fire({
        //                     title: 'Processing...',
        //                     text: 'Mohon tunggu...',
        //                     allowOutsideClick: false,
        //                     didOpen: () => Swal.showLoading()
        //                 });
        //             },
        //             success: function(response) {
        //                 Swal.close();

        //                 if (response.status == 200) {
        //                     Swal.fire({
        //                         title: "Success",
        //                         icon: response.icon,
        //                         text: response.message,
        //                         timer: 5000,
        //                         showConfirmButton: false
        //                     });

        //                     setTimeout(() => {
        //                         window.location.reload()
        //                     }, 1000);
        //                 } else {
        //                     Swal.fire({
        //                         icon: response.icon,
        //                         text: response.message
        //                     });

        //                 }
        //             },
        //             error: function(xhr) {
        //                 Swal.close();

        //                 let message = 'Terjadi kesalahan';
        //                 let icon = 'error';

        //                 if (xhr.responseJSON) {
        //                     if (xhr.responseJSON.message) {
        //                         message = xhr.responseJSON.message;
        //                     }

        //                     if (xhr.responseJSON.icon) {
        //                         icon = xhr.responseJSON.icon;
        //                     }
        //                 }

        //                 Swal.fire({
        //                     icon: icon,
        //                     text: message
        //                 });
        //             }
        //         });
        //     });
        // }
        async function openModalMoveSaldo(id) {
            $("#universalModalContent").html(`
            <form class="modal-content" autocomplete="off" onsubmit="requestMoveSaldo(event)" id="formmovesaldo">
                <div class="modal-header">
                    <h5 class="modal-title">Kembalikan Saldo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" value="${id}">

                    <div class="mb-3">
                        <label for="jumlah_uang" class="form-label fw-bold">Jumlah Uang <sup class="text-danger">*</sup></label>
                        <div class="input-group">
                            <span class="input-group-text bg-success text-light">RP</span>

                            <input type="text" name="jumlah_uang" id="jumlah_uang"
                                class="form-control text-end bg-warning fw-bold"
                                value="0">
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

        function requestMoveSaldo(e) {
            e.preventDefault();

            let jumlah_uang_raw = $("#jumlah_uang").val();

            // hapus titik (format rupiah)
            let jumlah_uang = parseInt(jumlah_uang_raw.replace(/\./g, '')) || 0;

            let isValid = true;

            // reset error dulu
            $('#jumlah_uang').removeClass('is-invalid');

            // validasi jumlah uang
            if (jumlah_uang <= 0) {
                $('#jumlah_uang').addClass('is-invalid');
                isValid = false;
            }

            if (!isValid) {
                Swal.fire({
                    icon: "warning",
                    text: "Harap isi field sebelum menyimpan data!"
                });
                return;
            }

            var formData = new FormData($("#formmovesaldo")[0]);
            formData.append('_token', '{{ csrf_token() }}')

            $.ajax({
                url: "{{ route('deposit.movesaldo') }}",
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
                        }).then(() => {
                            $("#universalModal").modal("hide");

                            window.location.reload();
                        });
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

        async function openModalRefund(id, no_transaksi) {
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
                            value="Cash" checked>
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
                    <h5 class="modal-title">Refund No Transaksi ${no_transaksi}</h5>
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

            let isValid = true;

            // reset error dulu
            $('#tanggal_bayar').removeClass('is-invalid');
            $('#jumlah_uang').removeClass('is-invalid');

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
                url: "{{ route('transaksi.refund') }}",
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
                        }).then(() => {
                            $("#universalModal").modal("hide");

                            window.location.reload();
                        });
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

        async function openModalPay(nim, no_invoice, total_tagihan) {
            $("#universalModalContent").addClass("modal-lg")
            // // ambil data bank dari API
            // let banks = [];
            // try {
            //     let res = await fetch('https://sia.ubtsu.ac.id/api/bank');
            //     banks = await res.json();
            // } catch (error) {
            //     console.error('Gagal ambil data bank:', error);
            // }

            // const priorityId = 3;
            // banks.sort((a, b) => {
            //     if (a.id === priorityId) return -1;
            //     if (b.id === priorityId) return 1;
            //     return 0;
            // });

            // let bankOptions = '';

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
                            value="Cash" checked>
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
                    url: `/deposit/get/${nim}`,
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
                                <td>NO INVOICE</td>
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

                    <ul class="nav nav-tabs mb-3" id="paymentTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active"
                                id="pembayaran-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#pembayaran"
                                type="button"
                                role="tab">
                                Pembayaran
                            </button>
                        </li>

                        <li class="nav-item" role="presentation">
                            <button class="nav-link"
                                id="deposit-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#deposit-tab-pane"
                                type="button"
                                role="tab">
                                Deposit
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="pembayaran" role="tabpanel">
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

                        <div class="tab-pane fade" id="deposit-tab-pane" role="tabpanel">
                            <div>
                                <label for="deposit" class="form-label fw-bold">Deposit</label>
                                <div class="input-group">
                                    <select class="form-select" name="deposit" id="deposit">
                                        ${depositOptions}
                                    </select>

                                    <button type="button" class="btn btn-primary" onclick="btnGunakanDeposit(${nim},
                                    '${no_invoice}')">
                                        <i class="fa fa-check me-1"></i> Gunakan
                                    </button>
                                </div>
                            </div>
                        </div>
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

            let isValid = true;

            // reset error dulu
            $('#tanggal_bayar').removeClass('is-invalid');
            $('#jumlah_uang').removeClass('is-invalid');
            $('#file_bukti').removeClass('is-invalid');

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
                        }).then(() => {
                            $("#universalModal").modal("hide");

                            window.location.reload();
                        });
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

        function btnGunakanDeposit(nim, no_invoice) {
            if ($("#deposit").val()) {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: `Apakah Anda benar-benar ingin menggunakan deposit ini?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, gunakan!',
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
                        formData.append('nim', nim);
                        formData.append('no_invoice', no_invoice);
                        formData.append('deposit_id', $("#deposit").val());

                        $.ajax({
                            url: '{{ route('deposit.use') }}',
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                Swal.close(); // tutup loading
                                if (response.status == 200) {
                                    Swal.fire({
                                        title: "Success",
                                        icon: response.icon,
                                        text: response.message,
                                        timer: 5000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        $("#universalModal").modal("hide");

                                        window.location.reload();
                                    });
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
        }

        function openModalPotonganHarga(no_invoice, id, kategori) {
            $("#universalModalContent").html(`
                <form class="modal-content" autocomplete="off" onsubmit="requestPotonganHarga(event)" id="formpotonganharga">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Potongan Harga ${kategori}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="no_invoice" value="${no_invoice}">
                        <input type="hidden" name="id" value="${id}">
                        <div class="mb-3">
                            <label for="potongan_harga" class="form-label fw-bold">Potongan Harga <sup
                                    class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text bg-success text-light">RP</span>

                                <input type="text" name="potongan_harga" id="potongan_harga"
                                    class="form-control text-end bg-warning fw-bold"
                                    value="0">
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

            // Money
            new AutoNumeric('#potongan_harga', {
                digitGroupSeparator: '.',
                decimalCharacter: ',',
                decimalPlaces: 0,
                unformatOnSubmit: true
            });

            $("#universalModal").modal("show");
        }

        function requestPotonganHarga(e) {
            e.preventDefault();

            let potongan_harga_raw = $("#potongan_harga").val();

            // hapus titik (format rupiah)
            let potongan_harga = parseInt(potongan_harga_raw.replace(/\./g, '')) || 0;
            let isValid = true;

            // reset error dulu
            $('#potongan_harga').removeClass('is-invalid');

            // validasi jumlah uang
            if (potongan_harga <= 0) {
                $('#potongan_harga').addClass('is-invalid');
                isValid = false;
            }

            if (!isValid) {
                Swal.fire({
                    icon: "warning",
                    text: "Harap isi semua field sebelum menyimpan data!"
                });
                return;
            }

            var formData = new FormData($("#formpotonganharga")[0]);
            formData.append('_token', '{{ csrf_token() }}')

            $.ajax({
                url: "{{ route('tagihan.potongan_harga') }}",
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

                        setTimeout(() => {
                            window.location.reload()
                        }, 1000);

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
