@extends('layouts.main')

@section('contents')
    <div class="container-fluid">
        <h1 class="mt-4">{{ $judul }}</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">{{ $judul }}</li>
        </ol>
        <div class="mb-2 d-flex align-content-center justify-content-end gap-2">
            <a href="javascript:void(0)" class="btn btn-info" onclick="onRefresh()">
                <i class="fa-solid fa-arrows-rotate me-1"></i>
                Refresh
            </a>
        </div>
        <div class="card mb-4 border-0" style="background-color: rgb(227 255 230)">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-xl-12">
                        <table class="table m-0" id="datatablepengguna" style="width: 100%">
                            <thead class="bg-dark text-light">
                                <tr>
                                    <th scope="col" width="50"></th>
                                    <th scope="col">ROLE</th>
                                    <th scope="col">NAMA LENGKAP</th>
                                    <th scope="col">NIP/NIM</th>
                                    <th scope="col">EMAIL</th>
                                    <th scope="col">JABATAN</th>
                                    <th scope="col">STATUS</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscripts')
    <script>
        var table
        $(document).ready(function() {
            table = $("#datatablepengguna").DataTable({
                processing: true,
                paging: false,
                ajax: {
                    url: "{{ route('pengguna.datatablepengguna') }}",
                    type: "POST",
                    // dataSrc: ""
                    dataType: "json"
                },
                columns: [{
                        data: "aksi",
                    },
                    {
                        data: "role",
                    },
                    {
                        data: "namalengkap",
                    },
                    {
                        data: "identifier",
                    },
                    {
                        data: "email",
                    },
                    {
                        data: "type",
                    },
                    {
                        data: "status",
                    },
                ],
                // "order": [
                //     [1, 'asc']
                // ],
                // scrollY: "700px",
                scrollX: true,
                // scrollCollapse: true,
                // paging:         false,
                // fixedColumns: {
                //     left: 3,
                // }
                drawCallback: function() {
                    var tooltipTriggerList = [].slice.call(
                        document.querySelectorAll('[data-bs-toggle="tooltip"]')
                    );

                    tooltipTriggerList.map(function(tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });
                },
            });
        });

        function onRefresh() {
            table.ajax.reload()
        }

        function openModalRole(id, role_id) {
            $("#universalModalContent").html(`
                <form class="modal-content" autocomplete="off" onsubmit="requestEditRole(event)" id="formeditrole">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="user_id" id="user_id" value="${id}">

                        <div class="mb-3">
                            <label for="role_id" class="fw-bold">Role <sup class="text-danger">*</sup></label>
                            <select class="form-select form-select-role" name="role_id" id="role_id" style="width: 100%">
                                <option value="">Pilih Role</option>
                                @foreach (\App\Models\Role::all() as $row)
                                    <option value="{{ $row->id }}">
                                        {{ $row->nama }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="error-role_id"></div>
                        </div>

                        <div>
                            <button type="submit" class="btn btn-success w-100" id="btn-submit">
                                <i class="fa fa-paper-plane me-1"></i> Simpan
                            </button>
                        </div>
                    </div>
                </form>
            `);

            $(".form-select-role").select2({
                dropdownParent: $("#universalModal"),
                theme: "bootstrap-5",
            });

            if (role_id != 0) {
                $("#role_id").val(role_id).trigger('change');
            }

            $("#universalModal").modal("show");
        }

        function requestEditRole(e) {
            e.preventDefault();

            var data = new FormData($("#formeditrole")[0])

            $.ajax({
                url: "{{ route('pengguna.editrole') }}",
                type: "POST",
                data: data,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $("#btn-submit").prop("disabled", true)
                    $("#btn-submit").html(`
                        <div class="spinner-border spinner-border-sm text-light" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    `)
                },
                success: function(response) {
                    if (response.status == 200) {
                        Swal.fire({
                            title: "Success",
                            icon: response.icon,
                            text: response.message,
                            timer: 5000,
                            showConfirmButton: false
                        });

                        $("#universalModal").modal("hide");

                        table.ajax.reload()
                    } else {
                        Swal.fire({
                            icon: response.icon,
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    // reset dulu
                    $(".form-select").removeClass("is-invalid");
                    $(".invalid-feedback").text("");

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;

                        if (errors.role_id) {
                            $("#role_id").addClass("is-invalid");
                            $("#error-role_id").text(errors.role_id[0]);
                        }

                    } else {
                        Swal.fire({
                            icon: "error",
                            text: "Terjadi kesalahan!"
                        });
                    }
                },
                complete: function() {
                    $("#btn-submit").html(`
                        <i class="fa fa-paper-plane me-1"></i> Simpan
                    `).prop("disabled", false);
                }
            });
        }

        function onStatus(el, id) {
            let status = $(el).val();

            $.ajax({
                url: "{{ route('pengguna.status') }}",
                type: "POST",
                data: {
                    user_id: id,
                    status: status
                },
                success: function(response) {
                    if (response.status == 200) {
                        Swal.fire({
                            title: "Success",
                            icon: response.icon,
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });

                        table.ajax.reload()
                    } else {
                        Swal.fire({
                            icon: response.icon,
                            text: response.message
                        });
                    }
                }
            });
        }
    </script>
@endpush
