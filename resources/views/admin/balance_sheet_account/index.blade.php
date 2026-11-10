@extends('admin.layout')
@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

        <!--  BEGIN CONTENT AREA  -->
        <div id="content" class="main-content">
            <div class="layout-px-spacing">
                <div class="row layout-top-spacing">
                    <div id="tableHover" class="col-lg-12 col-12 layout-spacing">
                        <div class="statbox widget box box-shadow">
                            <div class="widget-header">
                                <div class="row">
                                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                        <h4>Data {{ __($title) }}</h4>
                                    </div>
                                </div>
                            </div>

                            <form action="{{ url(Request::segment(1).'/search') }}" method="GET">
                                <div class="widget-content widget-content-area">
                                    <div class="row">
                                        <div class="col-xl-8 col-md-12 col-sm-12 col-12">
                                            <a href="#" class="btn mb-2 mr-1 btn-success" data-placement="top" data-toggle="modal" data-target="#exampleModal" title="Tambah Data" onClick="clearForm()">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus-circle"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                                            </a>
                                            <a href="{{ url(Request::segment(1)) }}" class="btn mb-2 mr-1 btn-warning" data-toggle="tooltip" data-placement="top" title="Refresh">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-ccw"><polyline points="1 4 1 10 7 10"></polyline><polyline points="23 20 23 14 17 14"></polyline><path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"></path></svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            @include('admin.balance_sheet_account.create')

                            <div class="widget-content widget-content-area" style="padding-top: 0px;">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover mb-12" id="balance-sheet-account-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 2%">Number</th>
                                                <th style="width: 2%">No</th>
                                                <th>Kode Transaksi</th>
                                                <th>Sub Kode Transaksi</th>
                                                <th>Nama Akun Neraca</th>
                                                <th>Kategori</th>
                                                <th>Status</th>
                                                <th style="width: 15%"></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    var table;

    $(document).ready(function () {
        table = $('#balance-sheet-account-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('balance_sheet_account.list') }}",
                type: 'GET',
                data: function (d) {
                    d.year = $('#filter_year').val();
                }
            },
            columns: [
                {data: 'id', name: 'id', visible: false},
                {data: 'number', name: 'number'},
                {data: 'transaction_code', name: 'transaction_code', orderable: false, searchable: false},
                {data: 'transaction_code_sub', name: 'transaction_code_sub', orderable: false, searchable: false},
                {data: 'display_name', name: 'display_name'},
                {data: 'display_category', name: 'category'},
                {data: 'display_is_active', name: 'is_active', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            order: [[0, 'asc']],
            paging: true,
            pageLength: 50,
            drawCallback: function () {
                var api = this.api();
                var startIndex = api.context[0]._iDisplayStart;
                api.column(1, {page: 'current'}).nodes().each(function (cell, i) {
                    cell.innerHTML = startIndex + i + 1;
                });
            }
        });

        $('#filter_year').on('change', function () {
            table.ajax.reload();
        });

        $('#myForm').submit(function (e) {
            e.preventDefault();

            var action = document.getElementById('action').innerText.trim();
            var id = $('#id_balance_sheet_account').val();
            var formData = new FormData(this);

            $.ajax({
                url: "{{ url('/balance_sheet_account/validate') }}/" + action,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function () {
                    $('.invalid-feedback').html('');

                    if (action === 'Simpan') {
                        send();
                    } else {
                        update(id);
                    }
                },
                error: function (xhr) {
                    $('.fv-plugins-message-container').html('');
                    var errors = xhr.responseJSON && xhr.responseJSON.errors;

                    if (errors) {
                        $.each(errors, function (key, value) {
                            $('#' + key + '-error').html(value[0]);
                        });
                    }
                }
            });
        });
    });

    function clearForm() {
        document.getElementById('head_title').textContent = 'Tambah {{ __($title) }}';
        $('#myForm')[0].reset();
        $('#id_balance_sheet_account').val('');
        $('#transaction_code_sub_id').html('<option value="">- Pilih Sub Kode Transaksi -</option>');
        document.getElementById('action').textContent = 'Simpan';
        $('.invalid-feedback').html('');
    }

    function showSuccessToast(message) {
        Snackbar.show({
            text: message,
            showAction: false,
            actionTextColor: '#fff',
            backgroundColor: '#8dbf42',
            pos: 'top-right'
        });
    }

    function showFailedToast(message) {
        Snackbar.show({
            text: message,
            showAction: false,
            actionTextColor: '#fff',
            backgroundColor: '#e7515a',
            pos: 'top-right'
        });
    }

    function send() {
        var formData = new FormData($('#myForm')[0]);

        $.ajax({
            url: "{{ url('balance_sheet_account/store') }}",
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                showSuccessToast(response.message);
                $('#myForm')[0].reset();
                $('#exampleModal').modal('hide');
                $('#transaction_code_sub_id').html('<option value="">- Pilih Sub Kode Transaksi -</option>');
                table.ajax.reload(null, false);
            },
            error: function (xhr) {
                showFailedToast(xhr.responseJSON?.message || 'Data gagal disimpan.');
                console.error('Error menyimpan data:', xhr);
            }
        });
    }

    function getData(id) {
        document.getElementById('head_title').textContent = 'Ubah {{ __($title) }}';
        document.getElementById('action').textContent = 'Update';

        $.ajax({
            url: "{{ url('/balance_sheet_account/edit') }}/" + id,
            type: 'GET',
            success: function (response) {
                var data = response.data;
                $('#id_balance_sheet_account').val(data.id);
                // $('#transaction_code_id').val(data.transaction_code_id);
                // getTransactionCodeSub(data.transaction_code_sub_id);
                $('#name').val(data.name);
                $('#category').val(data.category);
                $('#is_active').val(data.is_active ? '1' : '0');
                // $('.invalid-feedback').html('');

                
                $("#account_id").val(String(data.account_id));

                $("#transaction_code_id").html(
                    '<option value="">- Pilih -</option>'
                );

                $("#transaction_code_sub_id").html(
                    '<option value="">- Pilih -</option>'
                );

                var codeUrl = "{{ url('/transaction_code/get') }}";

                $.ajax({
                    url: codeUrl + "/" + data.account_id + "/" + data.transaction_code_id,
                    type: "GET",

                    success: function(html) {
                        $("#transaction_code_id").html(html);

                        // Beri jeda sebelum memilih kode transaksi
                        setTimeout(function() {
                            $("#transaction_code_id")
                                .val(String(data.transaction_code_id))
                                .trigger("change.select2");

                            var subUrl = "{{ url('/transaction_code_sub/get') }}";

                            $.ajax({
                                url: subUrl + "/" + data.transaction_code_id + "/" +
                                    (data.transaction_code_sub_id || ""),
                                type: "GET",

                                success: function(htmlSub) {
                                    var $sub = $("#transaction_code_sub_id");

                                    $sub.html(htmlSub);

                                    // Beri jeda sebelum memilih subkode
                                    setTimeout(function() {
                                        var selectedSubId =
                                            data.transaction_code_sub_id != null
                                                ? String(data.transaction_code_sub_id)
                                                : "";

                                        $sub.val(selectedSubId)
                                            .trigger("change.select2");

                                        console.log(
                                            "Subkode tersimpan:",
                                            selectedSubId
                                        );

                                        console.log(
                                            "Subkode terpilih:",
                                            $sub.val()
                                        );
                                    }, 300);
                                },

                                error: function(xhr) {
                                    console.error(
                                        "Gagal memuat subkode:",
                                        xhr.responseText
                                    );
                                }
                            });
                        }, 300);
                    },

                    error: function(xhr) {
                        console.error(
                            "Gagal memuat kode transaksi:",
                            xhr.responseText
                        );
                    }
                });
            },
            error: function (xhr) {
                showFailedToast(xhr.responseJSON?.message || 'Data gagal dimuat.');
                console.error('Error mengambil data:', xhr);
            }
        });
    }

    function update(id) {
        var formData = new FormData($('#myForm')[0]);
        formData.append('_token', "{{ csrf_token() }}");
        formData.append('_method', 'PUT');

        $.ajax({
            url: "{{ url('/balance_sheet_account/edit') }}/" + id,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                showSuccessToast(response.message);
                $('#myForm')[0].reset();
                $('#exampleModal').modal('hide');
                $('#transaction_code_sub_id').html('<option value="">- Pilih Sub Kode Transaksi -</option>');
                table.ajax.reload(null, false);
            },
            error: function (xhr) {
                showFailedToast(xhr.responseJSON?.message || 'Data gagal diperbarui.');
                console.error('Error memperbarui data:', xhr);
            }
        });
    }

    function deleteData(id) {
        swal({
            title: 'Apakah Kamu Yakin?',
            text: 'Anda tidak akan dapat mengembalikan ini!',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Delete',
            padding: '2em'
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: "{{ url('/balance_sheet_account/delete') }}/" + id,
                    success: function (response) {
                        showSuccessToast(response.message);
                        $('#myForm')[0].reset();
                        table.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        showFailedToast(xhr.responseJSON?.message || 'Data gagal dihapus.');
                        console.error('Error menghapus data:', xhr);
                    }
                });
            }
        });
    }

    
    function getTransactionCode() {
        var account_id = $("#account_id").val();

        // Reset kode transaksi dan subkode
        $("#transaction_code_id").html(
            '<option value="">- Pilih -</option>'
        );

        $("#transaction_code_sub_id").html(
            '<option value="">- Pilih -</option>'
        );

        if (!account_id) {
            return false;
        }

        var url = "{{ url('/transaction_code/get') }}";

        $.ajax({
            url: url + "/" + account_id,
            type: "GET",

            success: function(html) {
                $("#transaction_code_id").html(html);
            },

            error: function(xhr) {
                console.error(xhr.responseText);
            }
        });

        return false;
    }

    function getTransactionCode2() {
        var account_id = $("#import_account_id").val();

        // Reset kode transaksi dan subkode
        $("#import_transaction_code_id").html(
            '<option value="">- Pilih -</option>'
        );

        $("#import_transaction_code_sub_id").html(
            '<option value="">- Pilih -</option>'
        );

        if (!account_id) {
            return false;
        }

        var url = "{{ url('/transaction_code/get') }}";

        $.ajax({
            url: url + "/" + account_id,
            type: "GET",

            success: function(html) {
                $("#import_transaction_code_id").html(html);
            },

            error: function(xhr) {
                console.error(xhr.responseText);
            }
        });

        return false;
    }

    function getTransactionCodeSub() {
        var transaction_code_id = $("#transaction_code_id").val();

        $("#transaction_code_sub_id").html(
            '<option value="">- Pilih -</option>'
        );

        if (!transaction_code_id) {
            return false;
        }

        var url = "{{ url('/transaction_code_sub/get') }}";

        $.ajax({
            url: url + "/" + transaction_code_id,
            type: "GET",

            success: function(html) {
                $("#transaction_code_sub_id").html(html);
            },

            error: function(xhr) {
                console.error(xhr.responseText);
            }
        });

        return false;
    }

    function getTransactionCodeSub2() {
        var transaction_code_id = $("#import_transaction_code_id").val();

        $("#import_transaction_code_sub_id").html(
            '<option value="">- Pilih -</option>'
        );

        if (!transaction_code_id) {
            return false;
        }

        var url = "{{ url('/transaction_code_sub/get') }}";

        $.ajax({
            url: url + "/" + transaction_code_id,
            type: "GET",

            success: function(html) {
                $("#import_transaction_code_sub_id").html(html);
            },

            error: function(xhr) {
                console.error(xhr.responseText);
            }
        });

        return false;
    }

</script>
@endsection
