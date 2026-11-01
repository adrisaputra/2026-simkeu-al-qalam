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

							<div class="widget-content widget-content-area">
								<div class="row">
                                    @if(session('success'))
                                        <div class="alert alert-success">{{ session('success') }}</div>
                                    @endif
									<div class="col-xl-9 col-md-12 col-sm-12 col-12">
                                        <a href="{{ route('balance_sheet_details.input') }}" class="btn mb-2 mr-1 btn-success">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus-circle"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                                            Input Detail Saldo Neraca
                                        </a>
                                        <a href="{{ route('balance_sheet_details.index') }}" class="btn mb-2 mr-1 btn-warning" title="Refresh">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-ccw"><polyline points="1 4 1 10 7 10"></polyline><polyline points="23 20 17 20 17 14"></polyline><path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"></path></svg>
                                        </a>
                                    </div>
									<div class="col-xl-3 col-md-12 col-sm-12 col-12">
                                        <select id="filter_year" class="form-control form-control-sm" style="height: 38px;padding: 5px;">
                                            <option value="">- Semua Tahun -</option>
                                            @foreach($years as $year)
                                                <option value="{{ $year }}" @if(date('Y')==$year) selected @endif>{{ $year }}</option>
                                            @endforeach
                                        </select>
                                    </div>
								</div>
							</div>

                            @include('admin.balance_sheet_details.create')

                            <div class="widget-content widget-content-area" style="padding-top: 0px;">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover mb-12" id="balance-sheet-details-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 2%">Number</th>
                                                <th style="width: 2%">No</th>
                                                <th>Nama Akun Neraca</th>
                                                <th>Tahun</th>
                                                <th>Jumlah Saldo</th>
                                                <th>Catatan</th>
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
        table = $('#balance-sheet-details-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('balance_sheet_details.list') }}",
                type: 'GET',
                dataType: 'json',
                data: function (d) {
                    d.year = $('#filter_year').val();
                }
            },
            columns: [
                {data: 'id', name: 'id', visible: false},
                {data: 'number', name: 'number'},
                {data: 'account_name', name: 'account_name', orderable: false, searchable: false},
                {data: 'year', name: 'year'},
                {data: 'display_amount', name: 'amount'},
                {data: 'notes', name: 'notes', defaultContent: '-'},
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

            var action = document.getElementById('action').innerText;
            var id_balance_sheet_detail = $('#id_balance_sheet_detail').val();
            var formData = new FormData();
            formData.append('id', id_balance_sheet_detail);
            formData.append('balance_sheet_account_id', $('#balance_sheet_account_id').val());
            formData.append('year', $('#year').val());
            formData.append('amount', $('#amount').val());
            formData.append('notes', $('#notes').val());
            formData.append('_token', "{{ csrf_token() }}");

            var url = "{{ url('/balance_sheet_details/validate') }}";
            $.ajax({
                url: url + '/' + action,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function () {
                    $('.invalid-feedback').html('');
                    $('.is-invalid').removeClass('is-invalid');

                    if (action === 'Simpan') {
                        send();
                    } else {
                        update(id_balance_sheet_detail);
                    }
                },
                error: function (xhr) {
                    var errors = xhr.responseJSON.errors;
                    $('.fv-plugins-message-container').html('');

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
        $('#id_balance_sheet_detail').val('');
        $('#balance_sheet_account_id').val('');
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
            url: "{{ url('balance_sheet_details/store') }}",
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
                showFailedToast(xhr.responseJSON?.message || 'Data gagal disimpan.');
                console.error('Error pengiriman formulir:', xhr);
            }
        });
    }

    function getData(id) {
        document.getElementById('head_title').textContent = 'Ubah {{ __($title) }}';
        document.getElementById('action').textContent = 'Update';

        var url = "{{ url('/balance_sheet_details/edit') }}";
        $.ajax({
            url: url + '/' + id,
            type: 'GET',
            success: function (response) {
                document.getElementById('id_balance_sheet_detail').value = response.data.id;
                document.getElementById('balance_sheet_account_id').value = response.data.balance_sheet_account_id;
                document.getElementById('year').value = response.data.year;
                document.getElementById('amount').value = response.data.amount;
                document.getElementById('notes').value = response.data.notes;
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

        var url = "{{ url('/balance_sheet_details/edit') }}";
        $.ajax({
            url: url + '/' + id,
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
                swal('Deleted!', 'Data Berhasil Dihapus.', 'success').then(function () {
                    var url = "{{ url('/balance_sheet_details/delete') }}";
                    $.ajax({
                        url: url + '/' + id,
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
                });
            }
        });
    }
</script>
@endsection
