@extends('layouts.main')

@section('contents')
    <div class="container-fluid">
        <h1 class="mt-4">{{ $judul }}</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('harga') }}">Kembali</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $judul }}</li>
        </ol>

        <div class="card mb-4 border-0" style="background-color: rgb(227 255 230)">
            <div class="card-body">
                <div class="row mb-3 justify-content-center">
                    <div class="col-xl-8">
                        <form action="{{ route('harga.post') }}" method="POST" autocomplete="off">
                            @csrf
                            {{-- tagih --}}
                            <div class="row mb-3">
                                <label for="tagih" class="col-xl-2 col-form-label fw-bold">Tagihan <sup
                                        class="text-danger">*</sup></label>
                                <div class="col-xl-10">
                                    <select name="tagih"
                                        class="form-control form-select-2 @error('tagih') is-invalid @enderror"
                                        id="tagih" style="width: 100%">
                                        <option value="">Pilih Tagihan</option>
                                        @foreach (\App\Models\Tagih::all() as $row)
                                            <option value="{{ $row->id }}"
                                                {{ old('tagih') == $row->id ? 'selected' : '' }}>{{ $row->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tagih')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            {{-- nama tagihan --}}
                            <div class="row mb-3">
                                <label for="nama_tagihan" class="col-xl-2 col-form-label fw-bold">Nama Tagihan <sup
                                        class="text-danger">*</sup></label>
                                <div class="col-xl-10">
                                    <input type="text" name="nama_tagihan"
                                        class="form-control @error('nama_tagihan') is-invalid @enderror"
                                        placeholder="Masukkan nama tagihan" value="{{ old('nama_tagihan') }}"
                                        id="nama_tagihan" />
                                    @error('nama_tagihan')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            {{-- harga --}}
                            <div class="row mb-3">
                                <label for="harga" class="col-xl-2 col-form-label fw-bold">Harga <sup
                                        class="text-danger">*</sup></label>
                                <div class="col-xl-10">
                                    <div class="input-group">
                                        <span class="input-group-text bg-success text-light">RP</span>

                                        <input type="text" name="harga" id="harga"
                                            class="form-control text-end formatrupiah @error('harga') is-invalid @enderror"
                                            value="{{ old('harga') }}">
                                    </div>
                                    @error('harga')
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
