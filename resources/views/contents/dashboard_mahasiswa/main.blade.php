@extends('layouts.main')

@section('mystyles')
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
                <div class="card mb-4 border-0" style="background-color: rgb(255 227 248)">
                    <div class="card-body">
                        <div class="row justify-content-start">
                            <div class="col-xl-4">
                                <table class="50">
                                    <tbody>
                                        <tr>
                                            <td>NAMA</td>
                                            <td width="20" class="text-right">:</td>
                                            <td>{{ auth()->user()->name }}</td>
                                        </tr>
                                        <tr>
                                            <td>NIM</td>
                                            <td width="20" class="text-right">:</td>
                                            <td>{{ auth()->user()->identifier }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4 border-0" style="background-color: rgb(227 255 230)">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-xl-12">
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
