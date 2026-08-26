@extends('layouts.main')

@section('mystyles')
    <style>
        input[name="metode_pembayaran"] {
            appearance: none;
            -webkit-appearance: none;

            width: 16px;
            height: 16px;

            border: 2px solid var(--bs-secondary-color);
            border-radius: 50%;

            vertical-align: middle;
            position: relative;
            cursor: pointer;
        }

        /* Belum dipilih + validasi gagal */
        input[name="metode_pembayaran"].is-invalid {
            border-color: var(--bs-danger);
        }

        /* Dipilih */
        input[name="metode_pembayaran"]:checked {
            border-color: var(--bs-primary);
        }

        /* Titik tengah */
        input[name="metode_pembayaran"]:checked::after {
            content: "";
            position: absolute;

            width: 8px;
            height: 8px;

            background-color: var(--bs-primary);
            border-radius: 50%;

            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
@endsection

@section('contents')
    <div class="container-fluid">
        <h1 class="mt-4">{{ $judul }}</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('deposit') }}">Kembali</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $judul }}</li>
        </ol>

        <div class="card mb-4 border-0" style="background-color: rgb(227 255 230)">
            <div class="card-body">
                <div class="row mb-3 justify-content-center">
                    <div class="col-xl-8">
                        <form action="{{ route('deposit.post') }}" method="POST" autocomplete="off"
                            enctype="multipart/form-data">
                            @csrf
                            {{-- penyewa --}}
                            <div class="row mb-3">
                                <label for="nim" class="col-xl-3 col-form-label fw-bold">Penyewa <sup
                                        class="text-danger">*</sup></label>
                                <div class="col-xl-9">
                                    <select name="nim"
                                        class="form-control form-select-2 @error('nim') is-invalid @enderror" id="nim"
                                        style="width: 100%">
                                        <option value="">Pilih Penyewa</option>
                                        @foreach (\App\Models\Penyewa::all() as $row)
                                            <option value="{{ $row->nim }}"
                                                {{ old('nim') == $row->nim ? 'selected' : '' }}>Nama lengkap:
                                                {{ $row->namalengkap }} | NIM: {{ $row->nim }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('nim')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            {{-- tanggal referensi bayar --}}
                            <div class="row mb-3">
                                <label for="tanggal_bayar" class="col-xl-3 col-form-label fw-bold">Tanggal
                                    Referensi Bayar <sup class="text-danger">*</sup></label>
                                <div class="col-xl-9">
                                    <input type="text" name="tanggal_bayar" id="tanggal_bayar"
                                        class="form-control @error('tanggal_bayar') is-invalid @enderror tanggal_flat"
                                        value="{{ old('tanggal_bayar') }}">
                                    @error('tanggal_bayar')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            {{-- jumlah uang --}}
                            <div class="row mb-3">
                                <label for="jumlah_uang" class="col-xl-3 col-form-label fw-bold">Jumlah Uang <sup
                                        class="text-danger">*</sup></label>
                                <div class="col-xl-9">
                                    <div class="input-group">
                                        <span class="input-group-text bg-success text-light">RP</span>

                                        <input type="text" name="jumlah_uang" id="jumlah_uang"
                                            class="form-control text-end @error('jumlah_uang') is-invalid @enderror"
                                            value="{{ old('jumlah_uang') }}">
                                    </div>
                                    @error('jumlah_uang')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            {{-- file bukti --}}
                            <div class="row mb-3">
                                <label for="file_bukti" class="col-xl-3 col-form-label fw-bold">File Bukti <sup
                                        class="text-danger">(PDF, JPG, JPEG, PNG)</sup></label>
                                <div class="col-xl-9">
                                    <input type="file" name="file_bukti" id="file_bukti"
                                        class="form-control @error('file_bukti') is-invalid @enderror">
                                    @error('file_bukti')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            {{-- metode pembayaran --}}
                            <div class="row mb-3">
                                <label for="metode_pembayaran" class="col-xl-3 col-form-label fw-bold">Metode Pembayaran
                                    <sup class="text-danger">*</sup></label>
                                <div class="col-xl-9">
                                    <div id="bank-list">
                                    </div>
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

            flatpickr(".tanggal_flat", {
                enableTime: true,
                dateFormat: "d/m/Y H:i",
                maxDate: "today"
            });

            new AutoNumeric('#jumlah_uang', {
                digitGroupSeparator: '.',
                decimalCharacter: ',',
                decimalPlaces: 0,
                unformatOnSubmit: true
            });

            bankList()
        })

        async function bankList() {
            const defaultBankId = 2;
            let banks = [];

            // Ambil old value dari Laravel
            const oldMetodePembayaran = @json(old('metode_pembayaran'));

            try {
                const res = await fetch('https://sia.ubtsu.ac.id/api/bank');
                banks = await res.json();

                // Hanya tampilkan bank id = 2
                banks = banks.filter(bank => bank.id == 2);

                let bankOptions = "";
                banks.forEach((bank) => {
                    const bankValue = `${bank.name} - ${bank.account_name}`;

                    bankOptions += `
                        <div class="form-check mb-3">
                            <input type="radio"
                                name="metode_pembayaran"
                                id="metode_pembayaran_0"
                                class="@error('metode_pembayaran') is-invalid @enderror"
                                value="Cash"
                                ${oldMetodePembayaran === 'Cash' ? 'checked' : ''}>
                            <label class="form-check-label" for="metode_pembayaran_0">
                                Cash
                            </label>
                        </div>
                        <div class="form-check">
                            <input type="radio"
                                name="metode_pembayaran"
                                id="metode_pembayaran_${bank.id}"
                                class="@error('metode_pembayaran') is-invalid @enderror"
                                value="${bankValue}"
                                ${oldMetodePembayaran === bankValue ? 'checked' : ''}>
                            <label class="form-check-label" for="metode_pembayaran_${bank.id}">
                                ${bank.name} - ${bank.account_number}
                                <br>
                                <small class="text-muted">${bank.account_name}</small>
                            </label>
                        </div>
                    `;
                });

                // Tampilkan ke halaman
                $("#bank-list").html(bankOptions);
            } catch (error) {
                console.error('Gagal ambil data bank:', error);

                $("#bank-list").html(`
                    <div class="alert alert-danger mb-0">
                        Gagal memuat daftar bank.
                    </div>
                `);

                return;
            }
        }
    </script>
@endpush
