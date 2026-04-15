@extends('layouts.main')

@section('contents')
    <div class="container-fluid">
        <h1 class="mt-4">{{ $judul }}</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('kamar') }}">Kembali</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $judul }}</li>
        </ol>

        <div class="card mb-4 border-0" style="background-color: rgb(227 255 230)">
            <div class="card-body">
                <div class="row mb-3 justify-content-center">
                    <div class="col-xl-8">
                        <form action="{{ route('kamar.update', encrypt($datakamar->id)) }}" method="POST"
                            autocomplete="off">
                            @method('PUT')
                            @csrf
                            {{-- token listrik --}}
                            <div class="row mb-3">
                                <label for="token_listrik" class="col-xl-2 col-form-label fw-bold">Token Listrik <sup
                                        class="text-danger">*</sup></label>
                                <div class="col-xl-10">
                                    <input type="text" name="token_listrik"
                                        class="form-control @error('token_listrik') is-invalid @enderror"
                                        placeholder="Masukkan nomor token listrik"
                                        value="{{ old('token_listrik', $datakamar->token_listrik) }}" id="token_listrik"
                                        autofocus />
                                    @error('token_listrik')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            {{-- type --}}
                            <div class="row mb-3">
                                <label for="tipeasrama" class="col-xl-2 col-form-label fw-bold">Tipe Asrama <sup
                                        class="text-danger">*</sup></label>
                                <div class="col-xl-10">
                                    <select name="tipeasrama"
                                        class="form-control form-select-2 @error('tipeasrama') is-invalid @enderror"
                                        id="tipeasrama" style="width: 100%">
                                        <option value="">Pilih Tipe Asrama</option>
                                        @foreach (\App\Models\Tipeasrama::all() as $row)
                                            <option value="{{ $row->id }}"
                                                {{ old('tipeasrama', $datakamar->tipeasrama_id) == $row->id ? 'selected' : '' }}>
                                                {{ $row->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tipeasrama')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            {{-- lantai --}}
                            <div class="row mb-3">
                                <label for="lantai" class="col-xl-2 col-form-label fw-bold">Lantai <sup
                                        class="text-danger">*</sup></label>
                                <div class="col-xl-10">
                                    <select name="lantai"
                                        class="form-control form-select-2 @error('lantai') is-invalid @enderror"
                                        id="lantai" style="width: 100%">
                                        <option value="">Pilih Lantai</option>
                                        @foreach (\App\Models\Lantai::all() as $row)
                                            <option value="{{ $row->id }}"
                                                {{ old('lantai', $datakamar->lantai_id) == $row->id ? 'selected' : '' }}>
                                                {{ $row->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('lantai')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            {{-- nomor kamar / Nama Kamar --}}
                            <div class="row mb-3">
                                <label for="nomor_kamar" class="col-xl-2 col-form-label fw-bold">Nomor / Nama Kamar
                                    <sup class="text-danger">*</sup></label>
                                <div class="col-xl-10">
                                    <input type="text" name="nomor_kamar"
                                        class="form-control @error('nomor_kamar') is-invalid @enderror"
                                        placeholder="Masukkan nomor kamar / nama kamar"
                                        value="{{ old('nomor_kamar', $datakamar->nomor_kamar) }}" id="nomor_kamar" />
                                    @error('nomor_kamar')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            {{-- kapasitas --}}
                            <div class="row mb-3">
                                <label for="kapasitas" class="col-xl-2 col-form-label fw-bold">Kapasitas
                                    <sup class="text-danger">*</sup></label>
                                <div class="col-xl-10">
                                    <input type="number" name="kapasitas"
                                        class="form-control @error('kapasitas') is-invalid @enderror"
                                        placeholder="Masukkan jumlah kapasitas kamar"
                                        value="{{ old('kapasitas', $datakamar->kapasitas) }}" id="kapasitas" />
                                    @error('kapasitas')
                                        <div class="invalid-feedback">
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
