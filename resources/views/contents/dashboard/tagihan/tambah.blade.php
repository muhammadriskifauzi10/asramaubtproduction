@extends('layouts.main')

@section('contents')
    <div class="container-fluid">
        <h1 class="mt-4">{{ $judul }}</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active" aria-current="page">{{ $judul }}</li>
        </ol>

        <div class="card mb-4 border-0" style="background-color: rgb(227 255 230)">
            <div class="card-body">
                <div class="row mb-3 justify-content-center">
                    <div class="col-xl-8">
                        <form action="{{ route('tagihan.posttagihan') }}" method="POST" autocomplete="off">
                            @csrf
                            {{-- asrama --}}
                            <div class="row">
                                {{-- tanggal masuk --}}
                                <div class="col-xl-6 mb-3">
                                    <label for="tanggal_masuk" class="form-label fw-bold">Tanggal Masuk <sup
                                            class="text-danger">*</sup></label>
                                    <input type="text" name="tanggal_masuk"
                                        class="form-control @error('tanggal_masuk') is-invalid @enderror tanggal_flat"
                                        id="tanggal_masuk" value="{{ old('tanggal_masuk') }}">
                                    @error('tanggal_masuk')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                {{-- jumlah bulan --}}
                                <div class="col-xl-6 mb-3">
                                    <label for="jumlah_bulan" class="form-label fw-bold">Jumlah Bulan <sup
                                            class="text-danger">*</sup></label>
                                    <div class="input-group">
                                        <input type="number" name="jumlah_bulan" id="jumlah_bulan"
                                            class="form-control @error('jumlah_bulan') is-invalid @enderror fw-bold"
                                            value="{{ old('jumlah_bulan', 1) }}">
                                        <span class="input-group-text bg-success text-light">Bulan</span>
                                    </div>
                                    @error('jumlah_bulan')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-6 mb-3">
                                    <label class="form-label fw-bold">
                                        Mahasiswa? <sup class="text-danger">*</sup>
                                    </label>

                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input
                                                class="form-check-input @error('jenis_penyewa_mahasiswa') is-invalid @enderror"
                                                type="radio" name="jenis_penyewa_mahasiswa" id="jenis_penyewa_mahasiswa_y"
                                                value="Y"
                                                {{ old('jenis_penyewa_mahasiswa', 'Y') == 'Y' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="jenis_penyewa_mahasiswa_y">Ya</label>
                                        </div>

                                        <div class="form-check form-check-inline">
                                            <input
                                                class="form-check-input @error('jenis_penyewa_mahasiswa') is-invalid @enderror"
                                                type="radio" name="jenis_penyewa_mahasiswa" id="jenis_penyewa_mahasiswa_t"
                                                value="T" {{ old('jenis_penyewa_mahasiswa') == 'T' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="jenis_penyewa_mahasiswa_t">Tidak</label>
                                        </div>

                                        @error('jenis_penyewa_mahasiswa')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                {{-- kamar --}}
                                <div class="col-xl-6 mb-3">
                                    <label for="kamar" class="form-label fw-bold">Kamar <sup
                                            class="text-danger">*</sup></label>
                                    <select name="kamar"
                                        class="form-control form-select-2 @error('kamar') is-invalid @enderror"
                                        id="kamar" style="width: 100%">
                                        <option value="">Pilih kamar</option>
                                        @foreach (\App\Models\Kamar::whereColumn('jumlah_penyewa', '<', 'kapasitas')->orderby('tipe_asrama_id', 'ASC')->orderby('lantai', 'ASC')->orderby('nomor_kamar', 'ASC')->get() as $row)
                                            <option value="{{ $row->id }}"
                                                {{ old('kamar') == $row->id ? 'selected' : '' }}>
                                                Lokasi: {{ $row->type->nama ?? '' }} | Lantai: {{ $row->lantai }}
                                                | Kamar {{ $row->nomor_kamar }} (Tersedia
                                                {{ $row->kapasitas - $row->jumlah_penyewa }} Bed)
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
                            <div class="row" id="parent_mahasiswa">
                                {{-- mahasiswa --}}
                                <div class="col-xl-6 mb-3">
                                    <label for="penyewa" class="form-label fw-bold">Penyewa <sup
                                            class="text-danger">*</sup></label>
                                    <select name="penyewa"
                                        class="form-control form-select-2 @error('penyewa') is-invalid @enderror"
                                        id="penyewa" style="width: 100%">
                                        <option value="">Pilih penyewa</option>
                                        @foreach (\App\Models\Penyewa::where('status_asrama', 0)->orderBy('namalengkap', 'ASC')->get() as $row)
                                            <option value="{{ $row->id }}"
                                                {{ old('penyewa') == $row->id ? 'selected' : '' }}>
                                                {{ $row->namalengkap }} - {{ $row->nama_bill_to }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('penyewa')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row" id="parent_nonmahasiswa">
                                {{-- non mahasiswa --}}
                                <div class="col-xl-6 mb-3">
                                    <label for="nama_lengkap" class="form-label fw-bold">Nama Lengkap <sup
                                            class="text-danger">*</sup></label>
                                    <input type="text" name="nama_lengkap" id="nama_lengkap"
                                        class="form-control @error('nama_lengkap') is-invalid @enderror fw-bold"
                                        value="{{ old('nama_lengkap') }}">
                                    @error('nama_lengkap')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                {{-- no ktp --}}
                                <div class="col-xl-6 mb-3">
                                    <label for="no_ktp" class="form-label fw-bold">No KTP <sup
                                            class="text-danger">*</sup></label>

                                    <input type="text" inputmode="numeric" name="no_ktp" id="no_ktp" maxlength="16"
                                        class="form-control @error('no_ktp') is-invalid @enderror fw-bold"
                                        value="{{ old('no_ktp') }}"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16);">

                                    @error('no_ktp')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                {{-- harga asrama --}}
                                <div class="col-xl-6 mb-3">
                                    <label for="harga_asrama" class="form-label fw-bold">Harga Asrama <sup
                                            class="text-danger">*</sup></label>
                                    <select name="harga_asrama"
                                        class="form-control form-select-2 @error('harga_asrama') is-invalid @enderror"
                                        id="harga_asrama" style="width: 100%">
                                        @foreach (\App\Models\Harga::where('tagih_id', 1)->get() as $row)
                                            <option value="{{ $row->id }}"
                                                {{ old('harga_asrama') == $row->id ? 'selected' : '' }}>
                                                Tagihan: {{ $row->nama_tagihan }}
                                                |
                                                Harga: {{ number_format($row->harga, '2', '.', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('harga_asrama')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                {{-- potongan harga asrama --}}
                                <div class="col-xl-6 mb-3">
                                    <label for="potongan_harga_asrama" class="form-label fw-bold">Potongan Harga Asrama
                                        <sup class="text-danger">*</sup></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-success text-light">RP</span>

                                        <input type="text" name="potongan_harga_asrama" id="potongan_harga_asrama"
                                            class="form-control text-end formatrupiah @error('potongan_harga_asrama') is-invalid @enderror bg-warning fw-bold"
                                            value="{{ old('potongan_harga_asrama', 0) }}">
                                    </div>
                                    @error('potongan_harga_asrama')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-end">
                                <button type="submit" class="btn btn-success" id="btn-submit">
                                    <i class="fa fa-paper-plane me-1"></i> Simpan
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

            toggleJenisPenyewa();

            $("input[name='jenis_penyewa_mahasiswa']").on("change", function() {
                toggleJenisPenyewa();
            });
        })

        function toggleJenisPenyewa() {
            let jenis_penyewa_mahasiswa = $("input[name='jenis_penyewa_mahasiswa']:checked").val();

            if (jenis_penyewa_mahasiswa === 'Y') {
                $("#parent_mahasiswa").show()
                $("#parent_nonmahasiswa").hide()
            } else {
                $("#parent_mahasiswa").hide()
                $("#parent_nonmahasiswa").show()
            }
        }
    </script>
@endpush
