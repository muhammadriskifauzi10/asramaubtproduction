@extends('layouts.main')

@section('contents')
    <div class="container-fluid">
        <h1 class="mt-4">{{ $judul }}</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('lantai') }}">Kembali</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $judul }}</li>
        </ol>

        <div class="card mb-4 border-0" style="background-color: rgb(227 255 230)">
            <div class="card-body">
                <div class="row mb-3 justify-content-center">
                    <div class="col-xl-8">
                        <form action="{{ route('lantai.update', encrypt($datalantai->id)) }}" method="POST"
                            autocomplete="off">
                            @method('PUT')
                            @csrf
                            {{-- nama --}}
                            <div class="row mb-3">
                                <label for="nama" class="col-xl-2 col-form-label fw-bold">Nama Lantai <sup
                                        class="text-danger">*</sup></label>
                                <div class="col-xl-10">
                                    <input type="text" name="nama"
                                        class="form-control @error('nama') is-invalid @enderror"
                                        placeholder="Masukkan nama lantai" value="{{ old('nama', $datalantai->nama) }}"
                                        id="nama" autofocus />
                                    @error('nama')
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
