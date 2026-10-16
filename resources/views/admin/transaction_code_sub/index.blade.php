@extends('admin.layout')
@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

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

                    <div class="widget-content widget-content-area">
                        <a href="#" class="btn mb-2 mr-1 btn-success" data-placement="top" data-toggle="modal" data-target="#exampleModal" title="Tambah Data" onclick="clearForm()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus-circle"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                        </a>
                        <a href="{{ route('transaction_code_sub.index', ['transaction_code' => Crypt::encrypt($transactionCode->id)]) }}" class="btn mb-2 mr-1 btn-warning" data-toggle="tooltip" data-placement="top" title="Refresh">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-ccw"><polyline points="1 4 1 10 7 10"></polyline><polyline points="23 20 23 14 17 14"></polyline><path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"></path></svg>
                        </a>
                        <a href="{{ route('transaction_code.index', ['account' => Crypt::encrypt($transactionCode->account_id)]) }}" class="btn mb-2 mr-1 btn-danger" data-toggle="tooltip" data-placement="top" title="Kembali">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left-circle"><circle cx="12" cy="12" r="10"></circle><polyline points="12 8 8 12 12 16"></polyline><line x1="16" y1="12" x2="8" y2="12"></line></svg>
                        </a>
                    </div>

                    @include('admin.transaction_code_sub.create')

                    <div class="widget-content widget-content-area" style="padding-top: 0">
                        <p style="font-size:18px;font-weight:bold;text-align:center">
                            {{ $transactionCode->code }} - {{ $transactionCode->name }}
                        </p>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-12" id="transaction-code-subs-table">
                                <thead>
                                    <tr>
                                        <th style="width: 2%">ID</th>
                                        <th style="width: 2%">No</th>
                                        <th>Kode</th>
                                        <th>Nama Sub Kode Transaksi</th>
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
    var transactionCodeUrl = @json(url('transaction_code_sub', Crypt::encrypt($transactionCode->id)));

    $(document).ready(function () {
        table = $('#transaction-code-subs-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('transaction_code_sub.list', ['transaction_code' => Crypt::encrypt($transactionCode->id)]) }}",
            columns: [
                {data: 'id', name: 'id', visible: false},
                {data: 'number', name: 'number'},
                {data: 'code', name: 'code'},
                {data: 'name', name: 'name'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            order: [[0, 'asc']],
            paging: true,
            pageLength: 50,
            drawCallback: function () {
                var api = this.api();
                var startIndex = api.context[0]._iDisplayStart;
                api.column(1, {page: 'current'}).nodes().each(function (cell, index) {
                    cell.innerHTML = startIndex + index + 1;
                });
            }
        });

        $('#myForm').submit(function (event) {
            event.preventDefault();

            var action = document.getElementById('action').innerText.trim();
            var subId = $('#id_transaction_code_sub').val();

            $.ajax({
                url: transactionCodeUrl + '/validate/' + action,
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                processData: false,
                success: function () {
                    $('.invalid-feedback').html('');

                    if (action === 'Simpan') {
                        saveSubCode();
                    } else {
                        updateSubCode(subId);
                    }
                },
                error: function (xhr) {
                    $('.invalid-feedback').html('');
                    var errors = xhr.responseJSON && xhr.responseJSON.errors;

                    if (errors) {
                        $.each(errors, function (key, messages) {
                            $('#' + key + '-error').html(messages[0]);
                        });
                    }
                }
            });
        });
    });

    function clearForm() {
        document.getElementById('head_title').textContent = 'Tambah {{ __($title) }}';
        $('#myForm')[0].reset();
        $('#id_transaction_code_sub').val('');
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

    function saveSubCode() {
        $.ajax({
            url: transactionCodeUrl + '/store',
            type: 'POST',
            data: new FormData($('#myForm')[0]),
            contentType: false,
            processData: false,
            success: function (response) {
                showSuccessToast(response.message);
                $('#myForm')[0].reset();
                $('#exampleModal').modal('hide');
                table.ajax.reload(null, false);
            },
            error: function (xhr) {
                showFailedToast(xhr.responseJSON?.message || 'Data gagal disimpan.');
                console.error('Error pengiriman formulir:', xhr);
            }
        });
    }

    function getData(id) {
        document.getElementById('head_title').textContent = 'Ubah {{ __($title) }}';
        document.getElementById('action').textContent = 'Update';

        $.ajax({
            url: transactionCodeUrl + '/edit/' + id,
            type: 'GET',
            success: function (response) {
                $('#id_transaction_code_sub').val(response.data.id);
                $('#code').val(response.data.code);
                $('#name').val(response.data.name);
                $('.invalid-feedback').html('');
            },
            error: function (xhr) {
                showFailedToast(xhr.responseJSON?.message || 'Data gagal dimuat.');
                console.error('Error mengambil data:', xhr);
            }
        });
    }

    function updateSubCode(id) {
        var formData = new FormData($('#myForm')[0]);
        formData.append('_method', 'PUT');

        $.ajax({
            url: transactionCodeUrl + '/edit/' + id,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                showSuccessToast(response.message);
                $('#myForm')[0].reset();
                $('#exampleModal').modal('hide');
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
                    url: transactionCodeUrl + '/delete/' + id,
                    type: 'POST',
                    data: {_token: "{{ csrf_token() }}", _method: 'DELETE'},
                    success: function (response) {
                        showSuccessToast(response.message);
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
</script>
@endsection
