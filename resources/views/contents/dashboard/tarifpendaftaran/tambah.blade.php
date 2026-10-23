@extends('layouts.main')

@section('contents')
    <div class="container-fluid">
        <h1 class="mt-4">{{ $judul }}</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('tagih') }}">Kembali</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $judul }}</li>
        </ol>

        <div class="card mb-4 border-0" style="background-color: rgb(227 255 230)">
            <div class="card-body">
                <div class="row mb-3 justify-content-center">
                    <div class="col-xl-8">
                        <form action="{{ route('tarifpendaftaran.post') }}" method="POST" autocomplete="off">
                            @csrf
                            {{-- nama --}}
                            <div class="row mb-3">
                                <label for="nama" class="col-xl-2 col-form-label fw-bold">Nama Tarif <sup
                                        class="text-danger">*</sup></label>
                                <div class="col-xl-10">
                                    <input type="text" name="nama"
                                        class="form-control @error('nama') is-invalid @enderror"
                                        placeholder="Masukkan nama tagihan" value="{{ old('nama') }}" id="nama"
                                        autofocus />
                                    @error('nama')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            {{-- jenis --}}
                            <div class="row mb-3">
                                <label for="jenis" class="col-xl-2 col-form-label fw-bold">Jenis <sup
                                        class="text-danger">*</sup></label>
                                <div class="col-xl-10">
                                    <select name="jenis"
                                        class="form-control form-select-2 @error('jenis') is-invalid @enderror"
                                        id="jenis" style="width: 100%">
                                        <option value="">Pilih Jenis</option>
                                        <option value="kip" {{ old('jenis') == 'kip' ? 'selected' : '' }}>KIP</option>
                                        <option value="nonkip" {{ old('jenis') == 'nonkip' ? 'selected' : '' }}>Non KIP
                                        </option>
                                    </select>
                                    @error('jenis')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            {{-- jenis --}}
                            <div class="row mb-3">
                                <label for="kategori" class="col-xl-2 col-form-label fw-bold">Kategori <sup
                                        class="text-danger">*</sup></label>
                                <div class="col-xl-10">
                                    <select name="kategori"
                                        class="form-control form-select-2 @error('kategori') is-invalid @enderror"
                                        id="kategori" style="width: 100%">
                                        <option value="">Pilih Kategori</option>
                                        <option value="Deposit" {{ old('kategori') == 'Deposit' ? 'selected' : '' }}>
                                            Deposit
                                        </option>
                                        <option value="Perlengkapan"
                                            {{ old('kategori') == 'Perlengkapan' ? 'selected' : '' }}>
                                            Perlengkapan</option>
                                    </select>
                                    @error('jenis')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            {{-- nominal --}}
                            <div class="row mb-3">
                                <label for="nominal" class="col-xl-2 col-form-label fw-bold">Nominal <sup
                                        class="text-danger">*</sup></label>
                                <div class="col-xl-10">
                                    <div class="input-group">
                                        <span class="input-group-text bg-success text-light">RP</span>

                                        <input type="text" name="nominal" id="nominal"
                                            class="form-control text-end formatrupiah @error('nominal') is-invalid @enderror"
                                            value="{{ old('nominal') }}">
                                    </div>
                                    @error('nominal')
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
        })
    </script>
@endpush
