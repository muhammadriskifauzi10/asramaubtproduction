@extends('layouts.main')

@section('contents')
    <div class="container-fluid">
        <h1 class="mt-4">{{ $judul }}</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('request.permintaankamar') }}">Kembali</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $judul }}</li>
        </ol>

        <div class="card mb-4 border-0" style="background-color: rgb(227 255 230)">
            <div class="card-body">
                <div class="row mb-3 justify-content-center">
                    <div class="col-xl-8">
                        <form action="{{ route('pengguna.tagihan.posttagihan') }}" method="POST" autocomplete="off"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-xl-12">
                                    <table class="m-0">
                                        <tbody>
                                            <tr>
                                                <td>NO REQUEST</td>
                                                <td width="20" class="text-right">:</td>
                                                <td>{{ $requestpembayaran->no_request }}</td>
                                            </tr>
                                            <tr>
                                                <td>NAMA</td>
                                                <td width="20" class="text-right">:</td>
                                                <td>{{ $requestpembayaran->penyewa->namalengkap }}</td>
                                            </tr>
                                            <tr>
                                                <td>NIM</td>
                                                <td width="20" class="text-right">:</td>
                                                <td>{{ $requestpembayaran->penyewa->nim }}</td>
                                            </tr>
                                            <tr>
                                                <td>BILL TO</td>
                                                <td width="20" class="text-right">:</td>
                                                <td>{{ $requestpembayaran->nama_bill_to }}</td>
                                            </tr>
                                            <tr>
                                                <td>TIPE ASRAMA</td>
                                                <td width="20" class="text-right">:</td>
                                                <td>{{ $requestpembayaran->kamar->type->nama ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>LANTAI</td>
                                                <td width="20" class="text-right">:</td>
                                                <td>{{ $requestpembayaran->kamar->lantai }}</td>
                                            </tr>
                                            <tr>
                                                <td>KAMAR</td>
                                                <td width="20" class="text-right">:</td>
                                                <td>{{ $requestpembayaran->kamar->nomor_kamar }}</td>
                                            </tr>
                                            <tr>
                                                <td>TANGGAL MASUK</td>
                                                <td width="20" class="text-right">:</td>
                                                <td>{{ \Carbon\Carbon::parse($requestpembayaran->tanggal_masuk)->format('d M Y') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>TANGGAL KELUAR</td>
                                                <td width="20" class="text-right">:</td>
                                                <td>{{ \Carbon\Carbon::parse($requestpembayaran->tanggal_keluar)->format('d M Y') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>DURASI</td>
                                                <td width="20" class="text-right">:</td>
                                                <td>{{ $requestpembayaran->durasi }} Bulan</td>
                                            </tr>
                                            <tr>
                                                <td>TOTAL TAGIHAN</td>
                                                <td width="20" class="text-right">:</td>
                                                <td>RP. {{ number_format($requestpembayaran->total_tagihan, 0, '.', '.') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>TOTAL POTONGAN HARGA</td>
                                                <td width="20" class="text-right">:</td>
                                                <td>RP.
                                                    {{ number_format($requestpembayaran->total_potongan_harga, 0, '.', '.') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>NET TAGIHAN</td>
                                                <td width="20" class="text-right">:</td>
                                                <td>RP.
                                                    {{ number_format($requestpembayaran->total_tagihan - $requestpembayaran->total_potongan_harga, 0, '.', '.') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>TOTAL BAYAR</td>
                                                <td width="20" class="text-right">:</td>
                                                <td>
                                                    RP. {{ number_format($requestpembayaran->total_bayar, 0, '.', '.') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>PIUTANG</td>
                                                <td width="20" class="text-right">:</td>
                                                <td>
                                                    @php
                                                        $hutang =
                                                            $requestpembayaran->total_tagihan -
                                                            $requestpembayaran->total_potongan_harga -
                                                            $requestpembayaran->total_bayar;
                                                    @endphp
                                                    RP. {{ number_format($hutang, 0, '.', '.') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>STATUS PEMBAYARAN</td>
                                                <td width="20" class="text-right">:</td>
                                                <td>
                                                    @php
                                                        if ($requestpembayaran->status_pembayaran == 'completed') {
                                                            echo '<span class="badge bg-success">Completed</span>';
                                                        } elseif ($requestpembayaran->status_pembayaran == 'pending') {
                                                            echo '<span class="badge bg-warning text-dark">Pending</span>';
                                                        } else {
                                                            echo '<span class="badge bg-danger">Failed</span>';
                                                        }
                                                    @endphp
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <h5 class="mt-5">Pembayaran</h5>
                            <hr />
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="table-responsive">
                                        <table class="table m-0" id="datatabletagihan" style="width: 100%">
                                            <thead class="bg-dark text-light">
                                                <tr>
                                                    <th scope="col" width="50">NO</th>
                                                    <th scope="col">NO TRANSAKSI</th>
                                                    <th scope="col">TANGGAL TRANSAKSI</th>
                                                    <th scope="col">JUMLAH UANG</th>
                                                    <th scope="col">METODE PEMBAYARAN</th>
                                                    <th scope="col">FILE BUKTI</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $no = 1;
                                                @endphp
                                                @forelse (\App\Models\Requesttransaksi::where('no_request', $requestpembayaran->no_request)->get() as $row)
                                                    <tr>
                                                        <td>{{ $no++ }}</td>
                                                        <td>{{ $row->no_transaksi }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($row->tanggal_transaksi)->format('Y-m-d H:i') }}
                                                        </td>
                                                        <td>RP. {{ number_format($row->jumlah_uang, 0, '.', '.') }}</td>
                                                        <td>{{ $row->metode_pembayaran }}</td>
                                                        <td>
                                                            <a href="{{ asset('img/bukti_pembayaran/request/' . $requestpembayaran->no_request . '/' . $row->file_bukti) }}"
                                                                target="_blank"
                                                                class="text-primary text-decoration-none fw-bold">FILE
                                                                BUKTI</a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6">Tidak ada pembayaran</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <h5 class="mt-5">Update Permintaan</h5>
                            <hr />
                            <div class="row justify-content-end">
                                {{-- kamar --}}
                                <div class="col-xl-6 mb-3">
                                    <label for="kamar" class="form-label fw-bold">Kamar <sup
                                            class="text-danger">*</sup></label>
                                    <select name="kamar"
                                        class="form-control form-select-2 @error('kamar') is-invalid @enderror"
                                        id="kamar" style="width: 100%">
                                        <option value="">Pilih kamar</option>
                                        @foreach (\App\Models\Kamar::whereColumn('jumlah_penyewa', '<', 'kapasitas')->get() as $row)
                                            <option value="{{ $row->id }}"
                                                {{ old('kamar', $requestpembayaran->kamar_id) == $row->id ? 'selected' : '' }}>
                                                {{ $row->type->nama ?? '' }} - Kamar {{ $row->nomor_kamar }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kamar')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-end">
                                <button type="submit" class="btn btn-success" id="btn-submit">
                                    <i class="fa fa-paper-plane me-1"></i> Terima Permintaan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscripts')
    <script>
        $(document).ready(function() {
            flatpickr(".tanggal_bayar_flat", {
                enableTime: true,
                dateFormat: "d/m/Y H:i",
            });

            $("#btn-submit").on("click", function() {
                $("#btn-submit").html(`
                    <div class="spinner-border spinner-border-sm text-light" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                `)
                setTimeout(function() {
                    $("#btn-submit").prop("disabled", true)
                }, 1);
            })

            new AutoNumeric('#jumlah_uang', {
                digitGroupSeparator: '.',
                decimalCharacter: ',',
                decimalPlaces: 0,
                unformatOnSubmit: true
            });

            bankList()
        })

        async function bankList() {
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

            banks.forEach(bank => {
                bankOptions += `
                    <div class="form-check mb-2">
                        <input
                            class="form-check-input"
                            type="radio"
                            name="metode_pembayaran"
                            id="metode_pembayaran_${bank.id}"
                            value="${bank.name} - ${bank.account_name}"
                            checked
                            required
                        >

                        <label class="form-check-label" for="metode_pembayaran_${bank.id}">
                            <strong>${bank.name}</strong> - ${bank.account_number}
                            <br>
                            <small class="text-muted">${bank.account_name}</small>
                        </label>
                    </div>
                `;
            });

            $("#bank-list").html(bankOptions);
        }
    </script>
@endpush
