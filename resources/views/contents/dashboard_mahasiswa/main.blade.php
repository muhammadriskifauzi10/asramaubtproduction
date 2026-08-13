@extends('layouts.main')

@section('mystyles')
    <style>
        /* Desktop */
        .timeline-line {
            position: absolute;
            top: 35px;
            left: 0;
            width: 100%;
            height: 4px;
            background: #dee2e6;
            z-index: 0;
        }

        /* Mobile */
        @media (max-width: 767.98px) {
            .timeline-line {
                width: 4px;
                height: 100%;
                left: 50%;
                top: 0;
                transform: translateX(-50%);
            }
        }
    </style>
@endsection

@section('contents')
    <div class="container-fluid mt-3 mb-3">
        <div class="row">
            <div class="col-xl-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">{{ $judul }}</li>
                    </ol>
                </nav>

                {{-- buat disini berupa card time line --}}
                <div class="card mb-4 border-0" style="background-color: rgb(255 227 248)">
                    <div class="card-body">
                        <h5 class="fw-bold mb-4">
                            Timeline Permintaan Kamar
                        </h5>

                        <div
                            class="d-flex flex-column flex-md-row justify-content-between align-items-center position-relative">
                            <!-- garis -->
                            <div class="timeline-line"></div>

                            {{-- step 1 --}}
                            <div class="text-center position-relative mb-4 mb-md-0"
                                style="z-index:1;width:100%;max-width:300px;">
                                <a href="{{ route('pengguna.permintaankamar', encrypt($penyewa->id)) }}"
                                    class="text-decoration-none">
                                    <div class="rounded-circle bg-danger text-white d-inline-flex justify-content-center align-items-center shadow"
                                        style="width:70px;height:70px;">
                                        <i class="fas fa-bed fa-2x"></i>
                                    </div>
                                    <h6 class="mt-3 fw-bold text-dark">
                                        {{ \App\Models\Requestpembayaran::where('penyewa_id', $penyewa->id)->where('status_verifikasi', 0)->get()->count() }}
                                        Permintaan Kamar
                                    </h6>
                                    <small class="text-muted">
                                        Klik untuk melihat semua permintaan
                                    </small>
                                </a>
                            </div>

                            {{-- step 2 --}}
                            <div class="text-center position-relative mb-4 mb-md-0"
                                style="z-index:1;width:100%;max-width:300px;">
                                <a href="{{ route('pengguna.permintaankamar', encrypt($penyewa->id)) }}"
                                    class="text-decoration-none">
                                    <div class="rounded-circle bg-warning text-white d-inline-flex justify-content-center align-items-center shadow"
                                        style="width:70px;height:70px;">
                                        <i class="fas fa-receipt fa-2x"></i>
                                    </div>
                                    <h6 class="mt-3 fw-bold text-dark">
                                        Bukti Pembayaran
                                    </h6>
                                    <small class="text-muted">
                                        Klik untuk upload bukti pembayaran
                                    </small>
                                </a>
                            </div>

                            {{-- step 3 --}}
                            <div class="text-center position-relative" style="z-index:1;width:100%;max-width:300px;">
                                <a href="{{ route('pengguna.verifikasipermintaankamar', encrypt($penyewa->id)) }}"
                                    class="text-decoration-none">
                                    <div class="rounded-circle bg-success text-white d-inline-flex justify-content-center align-items-center shadow"
                                        style="width:70px;height:70px;">
                                        <i class="fas fa-check-circle fa-2x"></i>
                                    </div>
                                    <h6 class="mt-3 fw-bold text-dark">
                                        Verifikasi Berhasil
                                    </h6>
                                    <small class="text-muted">
                                        Pengajuan telah diverifikasi dan kamar telah ditetapkan
                                    </small>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4 border-0" style="background-color: rgb(227 255 230)">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-xl-12">
                                <h5 class="fw-bold mb-4">
                                    Daftar Tagihan
                                </h5>
                                <div class="table-responsive">
                                    <table class="table m-0" style="width: 100%">
                                        <thead class="bg-dark text-light">
                                            <tr>
                                                <th scope="col" width="50">NO</th>
                                                <th scope="col">NO INVOICE</th>
                                                {{-- <th scope="col">NAMA</th>
                                                <th scope="col">NIM</th> --}}
                                                {{-- <th scope="col">BILL TO</th> --}}
                                                <th scope="col">KAMAR</th>
                                                <th scope="col">DURASI</th>
                                                <th scope="col">ASRAMA</th>
                                                <th scope="col">CATERING</th>
                                                <th scope="col">TAGIHAN</th>
                                                <th scope="col">POTONGAN ASRAMA</th>
                                                <th scope="col">POTONGAN CATERING</th>
                                                <th scope="col">TOTAL POTONGAN HARGA</th>
                                                <th scope="col">TOTAL TAGIHAN</th>
                                                <th scope="col">TOTAL BAYAR</th>
                                                <th scope="col">PIUTANG</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $no = 1;
                                            @endphp
                                            @forelse ($tagihan as $row)
                                                <tr>
                                                    <td>{{ $no++ }}</td>
                                                    <td>{{ $row->no_invoice }}</td>
                                                    {{-- <td>{{ $row->penyewa->namalengkap }}</td>
                                                    <td>{{ $row->penyewa->nim }}</td> --}}
                                                    {{-- <td>{{ $row->nama_bill_to }}</td> --}}
                                                    <td>{{ $row->kamar->nomor_kamar }}</td>
                                                    <td>{{ $row->durasi }} Bulan</td>
                                                    <td>RP. {{ number_format($row->asrama, 0, '.', '.') }}</td>
                                                    <td>RP. {{ number_format($row->catering, 0, '.', '.') }}</td>
                                                    <td>RP. {{ number_format($row->tagihan, 0, '.', '.') }}</td>
                                                    <td>RP. {{ number_format($row->potongan_asrama, 0, '.', '.') }}</td>
                                                    <td>RP. {{ number_format($row->potongan_catering, 0, '.', '.') }}</td>
                                                    <td>RP. {{ number_format($row->total_potongan_harga, 0, '.', '.') }}
                                                    </td>
                                                    <td>RP. {{ number_format($row->total_tagihan, 0, '.', '.') }}</td>
                                                    <td>RP. {{ number_format($row->total_bayar, 0, '.', '.') }}</td>
                                                    <td>RP.
                                                        {{ number_format($row->total_tagihan - $row->total_bayar, 0, '.', '.') }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="13">Data tidak ada</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscripts')
@endpush
