@extends('layouts.main')

@section('contents')
    <div class="container-fluid">
        <h1 class="mt-4">{{ $judul }}</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dasbor') }}">Kembali</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $judul }}</li>
        </ol>

        <div class="card mb-4 border-0" style="background-color: rgb(227 255 230)">
            <div class="card-body">
                <div class="row mb-3 justify-content-center">
                    <div class="col-xl-8">
                        <form action="{{ route('pengguna.tagihan.posttagihan') }}" method="POST" autocomplete="off"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-3">
                                <label for="tanggal_masuk" class="col-xl-2 col-form-label fw-bold">Tanggal Masuk <sup
                                        class="text-danger">*</sup></label>
                                <div class="col-xl-10">
                                    <input type="text" name="tanggal_masuk"
                                        class="form-control @error('tanggal_masuk') is-invalid @enderror tanggal_flat"
                                        id="tanggal_masuk" value="{{ old('tanggal_masuk') }}">
                                    @error('tanggal_masuk')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            {{-- jumlah bulan --}}
                            <div class="row mb-3">
                                <label for="jumlah_bulan" class="col-xl-2 col-form-label fw-bold">Jumlah Bulan <sup
                                        class="text-danger">*</sup></label>
                                <div class="col-xl-10">
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
                            {{-- harga asrama --}}
                            <div class="row mb-3">
                                <label for="harga_asrama" class="col-xl-2 col-form-label fw-bold">Harga Asrama <sup
                                        class="text-danger">*</sup></label>
                                <div class="col-xl-10">
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
                            </div>

                            {{-- <h5 class="mt-5">Pembayaran</h5>
                            <hr /> --}}

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
            flatpickr(".tanggal_bayar_flat", {
                enableTime: true,
                dateFormat: "d/m/Y H:i",
            });

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

            new AutoNumeric('#jumlah_uang', {
                digitGroupSeparator: '.',
                decimalCharacter: ',',
                decimalPlaces: 0,
                unformatOnSubmit: true
            });

            bankList()
        })

        async function bankList() {
            let banks = [];

            try {
                const res = await fetch('https://sia.ubtsu.ac.id/api/bank');
                banks = await res.json();

                // Hanya tampilkan bank id = 2
                banks = banks.filter(bank => bank.id == 2);

            } catch (error) {
                console.error('Gagal ambil data bank:', error);

                $("#bank-list").html(`
                    <div class="alert alert-danger mb-0">
                        Gagal memuat daftar bank.
                    </div>
                `);

                return;
            }

            let bankOptions = "";

            banks.forEach(bank => {
                bankOptions += `
                    <div class="form-check mb-2">
                        <input
                            class="form-check-input"
                            type="radio"
                            name="metode_pembayaran"
                            id="metode_pembayaran_${bank.id}"
                            value="${bank.name} - ${bank.account_name}"
                            checked
                            required
                        >

                        <label class="form-check-label" for="metode_pembayaran_${bank.id}">
                            <strong>${bank.name}</strong> - ${bank.account_number}
                            <br>
                            <small class="text-muted">${bank.account_name}</small>
                        </label>
                    </div>
                `;
            });

            $("#bank-list").html(bankOptions);
        }
    </script>
@endpush
