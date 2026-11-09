@extends('layouts.main')

@section('mystyles')
    <style>
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
                <div class="row justify-content-center">
                    <div class="col-xl-4 mb-3">
                        {{-- data pribadi --}}
                        <table>
                            <tbody>
                                <tr>
                                    <td>NAMA</td>
                                    <td width="20" class="text-right">:</td>
                                    <td>{{ $pengajuanasrama->nama }}</td>
                                </tr>
                                <tr>
                                    <td>NO PENGAJUAN</td>
                                    <td width="20" class="text-right">:</td>
                                    <td>{{ $pengajuanasrama->no_pengajuan }}</td>
                                </tr>
                                <tr>
                                    <td>ASAL PENDAFTARAN</td>
                                    <td width="20" class="text-right">:</td>
                                    <td>{{ $pengajuanasrama->pendaftaran_dari }}</td>
                                </tr>
                                <tr>
                                    <td>NIM/NO PENDAFTARAN SIMABA</td>
                                    <td width="20" class="text-right">:</td>
                                    <td>{{ $pengajuanasrama->nim ?? $pengajuanasrama->nomor_pendaftaran }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>NO KTP</td>
                                    <td width="20" class="text-right">:</td>
                                    <td>{{ $pengajuanasrama->no_ktp }}</td>
                                </tr>
                                <tr>
                                    <td>ALAMAT</td>
                                    <td width="20" class="text-right">:</td>
                                    <td>{!! nl2br(e($pengajuanasrama->alamat)) !!}</td>
                                </tr>
                                <tr>
                                    <td>JENIS KELAMIN</td>
                                    <td width="20" class="text-right">:</td>
                                    <td>{{ $pengajuanasrama->jenis_kelamin == 'L' ? 'Laki-laki' : ($pengajuanasrama->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>AGAMA</td>
                                    <td width="20" class="text-right">:</td>
                                    <td>{{ $pengajuanasrama->agama }}</td>
                                </tr>
                                <tr>
                                    <td>PRODI</td>
                                    <td width="20" class="text-right">:</td>
                                    <td>{{ $pengajuanasrama->prodi }}</td>
                                </tr>
                                <tr>
                                    <td>NO HP</td>
                                    <td width="20" class="text-right">:</td>
                                    <td>{{ $pengajuanasrama->no_hp }}</td>
                                </tr>
                                <tr>
                                    <td>FOTO KTP</td>
                                    <td width="20" class="text-right">:</td>
                                    <td>
                                        @if ($pengajuanasrama->foto_ktp)
                                            <a href="{{ env('SIGEMAS_URL') }}/pengajuanasrama/{{ $pengajuanasrama->no_pengajuan }}/ktp/{{ $pengajuanasrama->foto_ktp }}"
                                                target="_blank">
                                                Lihat File
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>FOTO SELFIE</td>
                                    <td width="20" class="text-right">:</td>
                                    <td>
                                        @if ($pengajuanasrama->foto_selfi)
                                            <a href="{{ env('SIGEMAS_URL') }}/pengajuanasrama/{{ $pengajuanasrama->no_pengajuan }}/selfi/{{ $pengajuanasrama->foto_selfi }}"
                                                target="_blank">
                                                Lihat File
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-xl-8 mb-3">
                        {{-- tarif --}}
                        <div class="table-responsive mb-3">
                            <h5 class="mb-3">Rincian Tarif Asrama</h5>
                            <table class="table m-0" style="width: 100%">
                                <thead class="bg-dark text-light">
                                    <tr>
                                        <th scope="col">NAMA TARIF</th>
                                        <th scope="col">NOMINAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @forelse ($pengajuantarifasrama as $row)
                                        <tr>
                                            <td>{{ $row->nama_tarif }}</td>
                                            <td>RP. {{ number_format($row->nominal, 0, '.', '.') }}</td>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2">Tidak ada tarif</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-6 mb-3">
                        {{-- data orang tua 1 --}}
                        <div class="card border-2 border-dark" style="background-color: rgb(227 255 230)">
                            <div class="card-header fw-bold">Data Orang Tua/Wali 1</div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td>NAMA</td>
                                                    <td width="20" class="text-right">:</td>
                                                    <td>{{ $pengajuanasrama->nama_orangtua1 }}</td>
                                                </tr>
                                                <tr>
                                                    <td>HUBUNGAN</td>
                                                    <td width="20" class="text-right">:</td>
                                                    <td>{{ $pengajuanasrama->hubungan_orangtua1 }}</td>
                                                </tr>
                                                <tr>
                                                    <td>NO HP</td>
                                                    <td width="20" class="text-right">:</td>
                                                    <td>{{ $pengajuanasrama->no_hp_orangtua1 }}</td>
                                                </tr>
                                                <tr>
                                                    <td>ALAMAT</td>
                                                    <td width="20" class="text-right">:</td>
                                                    <td>{!! nl2br(e($pengajuanasrama->alamat_orangtua1)) !!}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6 mb-3">
                        {{-- data orangtua 2 --}}
                        <div class="card border-2 border-dark" style="background-color: rgb(227 255 230)">
                            <div class="card-header fw-bold">Data Orang Tua/Wali 2</div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td>NAMA</td>
                                                    <td width="20" class="text-right">:</td>
                                                    <td>{{ $pengajuanasrama->nama_orangtua2 }}</td>
                                                </tr>
                                                <tr>
                                                    <td>HUBUNGAN</td>
                                                    <td width="20" class="text-right">:</td>
                                                    <td>{{ $pengajuanasrama->hubungan_orangtua2 }}</td>
                                                </tr>
                                                <tr>
                                                    <td>NO HP</td>
                                                    <td width="20" class="text-right">:</td>
                                                    <td>{{ $pengajuanasrama->no_hp_orangtua2 }}</td>
                                                </tr>
                                                <tr>
                                                    <td>ALAMAT</td>
                                                    <td width="20" class="text-right">:</td>
                                                    <td>{!! nl2br(e($pengajuanasrama->alamat_orangtua2)) !!}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-6 mb-3">
                        {{-- rincian pembayaran --}}
                        <div class="card border-2 border-dark" style="background-color: rgb(227 255 230)">
                            <div class="card-header fw-bold">Rincian Pembayaran</div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td>JANGKA PEMBAYARAN</td>
                                                    <td width="20" class="text-right">:</td>
                                                    <td>{{ $pengajuanasrama->jangka_pembayaran }} bulan</td>
                                                </tr>
                                                <tr>
                                                    <td>PEMBAYARAN BULAN</td>
                                                    <td width="20" class="text-right">:</td>
                                                    <td>{{ $pengajuanasrama->pembayaran_bulan }} bulan</td>
                                                </tr>
                                                <tr>
                                                    <td>BIAYA SEWA PER BULAN</td>
                                                    <td width="20" class="text-right">:</td>
                                                    <td>Rp
                                                        {{ number_format($pengajuanasrama->biaya_sewa_per_bulan, 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>TOTAL SEWA</td>
                                                    <td width="20" class="text-right">:</td>
                                                    <td>Rp {{ number_format($pengajuanasrama->total_sewa, 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>TOTAL TARIF</td>
                                                    <td width="20" class="text-right">:</td>
                                                    <td>Rp {{ number_format($pengajuanasrama->total_tarif, 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>TOTAL PEMBAYARAN</td>
                                                    <td width="20" class="text-right">:</td>
                                                    <td><strong>Rp
                                                            {{ number_format($pengajuanasrama->total_pembayaran, 0, ',', '.') }}</strong>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>BANK</td>
                                                    <td width="20" class="text-right">:</td>
                                                    <td>{{ $pengajuanasrama->nama_bank }}</td>
                                                </tr>
                                                <tr>
                                                    <td>NOMOR REKENING</td>
                                                    <td width="20" class="text-right">:</td>
                                                    <td>{{ $pengajuanasrama->nomor_rekening }}</td>
                                                </tr>
                                                <tr>
                                                    <td>NAMA PEMILIK REKENING</td>
                                                    <td width="20" class="text-right">:</td>
                                                    <td>{{ $pengajuanasrama->nama_pemilik }}</td>
                                                </tr>
                                                <tr>
                                                    <td>TANGGAL PEMBAYARAN</td>
                                                    <td width="20" class="text-right">:</td>
                                                    <td>
                                                        {{ $pengajuanasrama->tanggal_pembayaran ? date('d-m-Y H:i', strtotime($pengajuanasrama->tanggal_pembayaran)) : '-' }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 mb-3">
                        {{-- data asrama --}}
                        <div class="card border-2 border-dark" style="background-color: rgb(227 255 230)">
                            <div class="card-header fw-bold">Data Asrama</div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td>TIPE ASRAMA</td>
                                                    <td width="20" class="text-right">:</td>
                                                    <td>{{ $pengajuanasrama->tipe_asrama }}</td>
                                                </tr>
                                                <tr>
                                                    <td>KEBUTUHAN KHUSUS</td>
                                                    <td width="20" class="text-right">:</td>
                                                    <td>{!! nl2br(e($pengajuanasrama->kebutuhan_khusus ?: '-')) !!}</td>
                                                </tr>
                                                <tr>
                                                    <td>METODE PERSETUJUAN</td>
                                                    <td width="20" class="text-right">:</td>
                                                    <td>{{ $pengajuanasrama->metode_persetujuan }}
                                                        @if ($pengajuanasrama->metode_persetujuan == 'dokumen_fisik_f20')
                                                            Dokumen Fisik F-20 (Dicetak,
                                                            ditandatangani basah, dan diserahkan saat check-in)
                                                        @elseif ($pengajuanasrama->metode_persetujuan == 'persetujuan_elektronik')
                                                            Persetujuan Elektronik via
                                                            Portal (Kode OTP dikirim langsung ke WhatsApp Wali)
                                                        @elseif ($pengajuanasrama->metode_persetujuan == 'verifikasi_jarak_jauh')
                                                            Verifikasi Jarak Jauh (Video
                                                            Call langsung dengan Petugas Asrama)
                                                        @elseif ($pengajuanasrama->metode_persetujuan == 'penggunaan_kembali_kip')
                                                            Penggunaan Kembali Dokumen
                                                            KIP (Khusus Penerima Beasiswa KIP-Kuliah)
                                                        @endif
                                                    </td>
                                                </tr>
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
    </div>
@endsection

@push('myscripts')
    <script>
        $(document).ready(function() {});
    </script>
@endpush
