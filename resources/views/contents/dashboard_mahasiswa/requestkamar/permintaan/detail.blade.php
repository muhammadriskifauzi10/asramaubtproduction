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
            <li class="breadcrumb-item"><a href="javascript:history.back()">Kembali</a></li>
            <li class="breadcrumb-item active">{{ $judul }}</li>
        </ol>

        <div class="card mb-4 border-0" style="background-color: rgb(255 227 248)">
            <div class="card-body">
                <div class="row justify-content-start">
                    <div class="col-xl-4 mb-3">
                        <table class="50">
                            <tbody>
                                <tr>
                                    <td>NO REQUEST</td>
                                    <td width="20" class="text-right">:</td>
                                    <td>{{ $tagihan->no_request }}</td>
                                </tr>
                                <tr>
                                    <td>NAMA</td>
                                    <td width="20" class="text-right">:</td>
                                    <td>{{ $tagihan->penyewa->namalengkap }}</td>
                                </tr>
                                <tr>
                                    <td>NO KTP</td>
                                    <td width="20" class="text-right">:</td>
                                    <td>{{ $tagihan->penyewa->noktp }}</td>
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
                                    <td>{{ $tagihan->kamar->lantai ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td>KAMAR</td>
                                    <td width="20" class="text-right">:</td>
                                    <td>{{ $tagihan->kamar->nomor_kamar ?? '' }}</td>
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
                        {{-- pembayaran --}}
                        <div class="table-responsive">
                            <h5 class="mb-3">Pembayaran</h5>
                            <table class="table m-0" style="width: 100%">
                                <thead class="bg-dark text-light">
                                    <tr>
                                        <th scope="col" width="50"></th>
                                        <th scope="col">NO KUITANSI</th>
                                        <th scope="col">TANGGAL PEMBAYARAN</th>
                                        <th scope="col">JUMLAH UANG</th>
                                        <th scope="col">TANGGAL REFERENSI BAYAR</th>
                                        <th scope="col">JENIS PEMBAYARAN</th>
                                        <th scope="col">METODE PEMBAYARAN</th>
                                        <th scope="col">FILE BUKTI</th>
                                        <th scope="col">OPERATOR</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @forelse (\App\Models\Requesttransaksi::where('no_request', $tagihan->no_request)->orderBy('created_at', 'DESC')->get() as $row)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <a href="{{ route('transaksi.kwitansi', encrypt($row->no_transaksi)) }}"
                                                        class="btn btn-success fw-bold d-flex align-items-center justify-content-center"
                                                        data-bs-toggle="tooltip" title="Cetak Kwitansi" style="width: 40px;"
                                                        target="_blank">
                                                        <i class="fa fa-receipt"></i>
                                                    </a>
                                                </div>
                                            </td>
                                            <td>{{ $row->no_transaksi }}</td>
                                            <td>{{ \Carbon\Carbon::parse($row->created_at)->format('Y-m-d H:i') }}
                                            </td>
                                            <td>
                                                <span>
                                                    RP. {{ number_format($row->jumlah_uang, 0, '.', '.') }}
                                                </span>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($row->tanggal_transaksi)->format('Y-m-d H:i') }}
                                            </td>
                                            <td>{{ $row->jenis_transaksi }}</td>
                                            <td>{{ $row->metode_pembayaran }}</td>
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
                        @php
                            $no = 1;
                        @endphp
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
                                            {{ $no++ }}
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
@endsection

@push('myscripts')
    <script>
        $(document).ready(function() {});
    </script>
@endpush
