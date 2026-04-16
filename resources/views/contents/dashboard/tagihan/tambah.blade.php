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
                                {{-- kamar --}}
                                <div class="col-xl-6 mb-3">
                                    <label for="kamar" class="form-label fw-bold">Kamar <sup
                                            class="text-danger">*</sup></label>
                                    <select name="kamar"
                                        class="form-control form-select-2 @error('kamar') is-invalid @enderror"
                                        id="kamar" style="width: 100%">
                                        <option value="">Pilih kamar</option>
                                        @foreach (\App\Models\Kamar::whereColumn('jumlah_penyewa', '<', 'kapasitas')->get() as $row)
                                            <option value="{{ $row->id }}"
                                                {{ old('kamar') == $row->id ? 'selected' : '' }}>
                                                {{ $row->type->nama ?? '' }} - Kamar {{ $row->nomor_kamar }}
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
                            <div class="row">
                                {{-- harga asrama --}}
                                <div class="col-xl-6 mb-3">
                                    <label for="harga_asrama" class="form-label fw-bold">Harga Asrama <sup
                                            class="text-danger">*</sup></label>
                                    <select name="harga_asrama"
                                        class="form-control form-select-2 @error('harga_asrama') is-invalid @enderror"
                                        id="harga_asrama" style="width: 100%">
                                        <option value="">Pilih harga asrama</option>
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
                                    <label for="potongan_harga_asrama" class="form-label fw-bold">Potongan Harga Asrama <sup
                                            class="text-danger">*</sup></label>
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
                            {{-- catering --}}
                            <div class="row">
                                <div class="col-xl-6 mb-3">
                                    <label class="form-label fw-bold">
                                        Catering? <sup class="text-danger">*</sup>
                                    </label>

                                    <div>
                                        {{-- YA --}}
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input @error('catering') is-invalid @enderror"
                                                type="radio" name="catering" id="catering_y" value="Y"
                                                {{ old('catering', 'T') == 'Y' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="catering_y">Ya</label>
                                        </div>

                                        {{-- TIDAK --}}
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input @error('catering') is-invalid @enderror"
                                                type="radio" name="catering" id="catering_t" value="T"
                                                {{ old('catering', 'T') == 'T' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="catering_t">Tidak</label>
                                        </div>

                                        @error('catering')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                {{-- harga catering --}}
                                <div class="col-xl-6 mb-3">
                                    <label for="harga_catering" class="form-label fw-bold">Harga Catering <sup
                                            class="text-danger">*</sup></label>
                                    <select name="harga_catering"
                                        class="form-control form-select-2 @error('harga_catering') is-invalid @enderror"
                                        id="harga_catering" style="width: 100%" disabled>
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
                                {{-- potongan harga catering --}}
                                <div class="col-xl-6 mb-3">
                                    <label for="potongan_harga_catering" class="form-label fw-bold">Potongan Harga
                                        Catering
                                        <sup class="text-danger">*</sup></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-success text-light">RP</span>

                                        <input type="text" name="potongan_harga_catering" id="potongan_harga_catering"
                                            class="form-control text-end formatrupiah @error('potongan_harga_catering') is-invalid @enderror bg-warning fw-bold"
                                            value="{{ old('potongan_harga_catering', 0) }}" disabled>
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

            toggleCatering();

            $("input[name='catering']").on("change", function() {
                toggleCatering();
            });
        })

        function toggleCatering() {
            let catering = $("input[name='catering']:checked").val();

            if (catering === 'Y') {
                $("#harga_catering").prop('disabled', false);
                $("#potongan_harga_catering").prop('disabled', false);
            } else {
                $("#harga_catering").prop('disabled', true).val('').trigger('change');
                $("#potongan_harga_catering").prop('disabled', true).val(0);
            }
        }
    </script>
@endpush
