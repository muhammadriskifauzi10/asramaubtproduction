@extends('layouts.main')

@section('contents')
    <div class="container-fluid">
        <h1 class="mt-4">{{ $judul }}</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('tipecatering') }}">Kembali</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $judul }}</li>
        </ol>

        <div class="card mb-4 border-0" style="background-color: rgb(227 255 230)">
            <div class="card-body">
                <div class="row mb-3 justify-content-center">
                    <div class="col-xl-8">
                        <form action="{{ route('tipecatering.post') }}" method="POST" autocomplete="off">
                            @csrf
                            {{-- jenis tagih --}}
                            <div class="row mb-3">
                                <label for="jenis_tagih" class="col-xl-2 col-form-label fw-bold">Jenis Tagihan Catering <sup
                                        class="text-danger">*</sup></label>
                                <div class="col-xl-10">
                                    <select name="jenis_tagih"
                                        class="form-control form-select-2 @error('jenis_tagih') is-invalid @enderror"
                                        id="jenis_tagih" style="width: 100%">
                                        <option value="">Pilih Jenis Tagihan Catering</option>
                                        @foreach (\App\Models\Harga::where('tagih_id', 2)->get() as $row)
                                            <option value="{{ $row->id }}"
                                                {{ old('jenis_tagih') == $row->id ? 'selected' : '' }}>
                                                {{ $row->nama_tagihan }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('jenis_tagih')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            {{-- nama tipe catering --}}
                            <div class="row mb-3">
                                <label for="tipe_catering" class="col-xl-2 col-form-label fw-bold">Tipe Catering <sup
                                        class="text-danger">*</sup></label>
                                <div class="col-xl-10">
                                    <input type="text" name="tipe_catering"
                                        class="form-control @error('tipe_catering') is-invalid @enderror"
                                        placeholder="Masukkan nama tipe catering" value="{{ old('tipe_catering') }}"
                                        id="tipe_catering" />
                                    @error('tipe_catering')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            {{-- jumlah porsi --}}
                            <div class="row mb-3">
                                <label for="jumlah_porsi" class="col-xl-2 col-form-label fw-bold">Jumlah Porsi
                                    <sup class="text-danger">*</sup></label>
                                <div class="col-xl-10">
                                    <input type="number" name="jumlah_porsi"
                                        class="form-control @error('jumlah_porsi') is-invalid @enderror"
                                        placeholder="Masukkan jumlah porsi" value="{{ old('jumlah_porsi') }}"
                                        id="jumlah_porsi" />
                                    @error('jumlah_porsi')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            {{-- pagi --}}
                            <div class="row mb-3">
                                <label class="col-xl-2 col-form-label fw-bold">
                                    Pagi <sup class="text-danger">*</sup>
                                </label>

                                <div class="col-xl-10">
                                    <div class="form-check form-check-inline">
                                        <input class="@error('pagi') is-invalid @enderror" type="radio" name="pagi"
                                            id="pagi_y" value="Y" {{ old('pagi') == 'Y' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="pagi_y">Ya</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input class="@error('pagi') is-invalid @enderror" type="radio" name="pagi"
                                            id="pagi_t" value="T" {{ old('pagi') == 'T' ? 'checked' : '' }}
                                            checked>
                                        <label class="form-check-label" for="pagi_t">Tidak</label>
                                    </div>

                                    @error('pagi')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            {{-- siang --}}
                            <div class="row mb-3">
                                <label class="col-xl-2 col-form-label fw-bold">
                                    Siang <sup class="text-danger">*</sup>
                                </label>

                                <div class="col-xl-10">
                                    <div class="form-check form-check-inline">
                                        <input class="@error('siang') is-invalid @enderror" type="radio" name="siang"
                                            id="siang_y" value="Y" {{ old('siang') == 'Y' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="siang_y">Ya</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input class="@error('siang') is-invalid @enderror" type="radio" name="siang"
                                            id="siang_t" value="T" {{ old('siang') == 'T' ? 'checked' : '' }}
                                            checked>
                                        <label class="form-check-label" for="siang_t">Tidak</label>
                                    </div>

                                    @error('siang')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            {{-- malam --}}
                            <div class="row mb-3">
                                <label class="col-xl-2 col-form-label fw-bold">
                                    Malam <sup class="text-danger">*</sup>
                                </label>

                                <div class="col-xl-10">
                                    <div class="form-check form-check-inline">
                                        <input class="@error('malam') is-invalid @enderror" type="radio" name="malam"
                                            id="malam_y" value="Y" {{ old('malam') == 'Y' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="malam_y">Ya</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input class="@error('malam') is-invalid @enderror" type="radio" name="malam"
                                            id="malam_t" value="T" {{ old('malam') == 'T' ? 'checked' : '' }}
                                            checked>
                                        <label class="form-check-label" for="malam_t">Tidak</label>
                                    </div>

                                    @error('malam')
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
