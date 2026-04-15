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
                        <form action="{{ route('tagihan.tambah.postcatering') }}" method="POST" autocomplete="off">
                            @csrf
                            {{-- asrama --}}
                            <div class="row">
                                {{-- tanggal masuk --}}
                                <div class="col-xl-6 mb-3">
                                    <label for="tanggal_masuk" class="form-label fw-bold">Tanggal Masuk <sup
                                            class="text-danger">*</sup></label>
                                    <input type="date" name="tanggal_masuk"
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
                                {{-- penyewa --}}
                                <div class="col-xl-6 mb-3">
                                    <label for="penyewa" class="form-label fw-bold">Penyewa <sup
                                            class="text-danger">*</sup></label>
                                    <select name="penyewa"
                                        class="form-control form-select-2 @error('penyewa') is-invalid @enderror"
                                        id="penyewa" style="width: 100%">
                                        <option value="">Pilih penyewa</option>
                                        @foreach (\App\Models\Penyewa::where('status_asrama', 1)->where('status_catering', 0)->orderBy('namalengkap', 'ASC')->get() as $row)
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
                                {{-- harga catering --}}
                                <div class="col-xl-6 mb-3">
                                    <label for="harga_catering" class="form-label fw-bold">Harga Catering <sup
                                            class="text-danger">*</sup></label>
                                    <select name="harga_catering"
                                        class="form-control form-select-2 @error('harga_catering') is-invalid @enderror"
                                        id="harga_catering" style="width: 100%">
                                        <option value="">Pilih harga catering</option>
                                        @foreach (\App\Models\Harga::where('tagih_id', 2)->get() as $row)
                                            <option value="{{ $row->id }}"
                                                {{ old('harga_catering') == $row->id ? 'selected' : '' }}>
                                                Tagihan: {{ $row->nama_tagihan }}
                                                |
                                                Harga: {{ number_format($row->harga, '2', '.', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('harga_catering')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row justify-content-end">
                                {{-- potongan harga catering --}}
                                <div class="col-xl-6 mb-3">
                                    <label for="potongan_harga_catering" class="form-label fw-bold">Potongan Harga
                                        Catering
                                        <sup class="text-danger">*</sup></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-success text-light">RP</span>

                                        <input type="text" name="potongan_harga_catering" id="potongan_harga_catering"
                                            class="form-control text-end formatrupiah @error('potongan_harga_catering') is-invalid @enderror bg-warning fw-bold"
                                            value="{{ old('potongan_harga_catering', 0) }}">
                                    </div>
                                    @error('potongan_harga_catering')
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
                    @include('contents.dashboard.tagihan.menu.list')
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
        })
    </script>
@endpush
