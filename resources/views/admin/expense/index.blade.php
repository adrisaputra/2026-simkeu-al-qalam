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
										<a href="#" class="btn mb-2 mr-1 btn-success" data-placement="top" data-toggle="modal" data-target="#exampleModal" title="Tambah Data" onClick="clearForm()"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus-circle"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg></a>
										{{--<a href="#" class="btn mb-2 mr-1 btn-info" data-placement="top" data-toggle="modal" data-target="#exampleModalImport" title="Import Data"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-upload"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg></a>--}}
										<a href="{{ url(Request::segment(1)) }}" class="btn mb-2 mr-1 btn-warning" data-toggle="tooltip" data-placement="top" title="Refresh"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-ccw"><polyline points="1 4 1 10 7 10"></polyline><polyline points="23 20 23 14 17 14"></polyline><path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"></path></svg></a>
									</div>
								</div>
							</div>
						</form>
						
						@include('admin.expense.create')
								
                            <div class="widget-content widget-content-area" style="padding-top: 0px;">
							<div class="table-responsive">
								<table class="table table-bordered table-hover mb-12" id="transaction-code-table">
									<thead>
										<tr>
											<th style="width: 2%">Number</th>
											<th style="width: 2%">No</th>
											<th style="width: 5%">Kode</th>
											<th style="width: 5%">Hari/Tanggal</th>
											<th style="width: 20%">Uraian</th>
											<th style="width: 10%">Jumlah</th>
											<th style="width: 15%"></th>
										</tr>
									</thead>
								</table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
<script src="{{ asset('backend/assets/js/jquery-3.4.1.min.js')}}"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    var table;

    $(document).ready(function () {
        table = $('#transaction-code-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ url('expense/list') }}",
            columns: [
				{data: 'id', name: 'id', visible: false},
				{data: 'number', name: 'number'}, // Kolom nomor urut
				{data: 'display_transaction_code', name: 'display_transaction_code'}, // Kolom kode transaksi
				{data: 'display_date', name: 'display_date'}, // Kolom tanggal
				{data: 'desc', name: 'desc'}, // Kolom u raian
				{data: 'display_amount', name: 'amount'}, // Kolom jumlah
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
			order: [
				[0, 'desc'] // Mengatur pengurutan kolom pertama (id) secara descending
			],
            paging: true,
            pageLength: 50,
			drawCallback: function () {
                var api = this.api();
                var startIndex = api.context[0]._iDisplayStart; // Indeks baris pertama di halaman
                api.column(1, {page: 'current'}).nodes().each(function (cell, i) {
                    cell.innerHTML = startIndex + i + 1; // Menghitung nomor urut berdasarkan indeks baris dan nomor halaman
                });
            }
        });

        $('#myForm').submit(function (e) {
            e.preventDefault(); // Hindari pengiriman form secara default

            var action = document.getElementById('action').innerText;
            var id_expense = $('#id_expense').val();
            var date = $('#date').val();

            // Buat objek FormData untuk mengirim data form, termasuk file
            var formData = new FormData();
            formData.append('id', id_expense);
            formData.append('date', date);
            formData.append('_token', "{{ csrf_token() }}");

            // Kirim permintaan validasi ke controller via Ajax
            var url = "{{ url('/expense/validate') }}";
            $.ajax({
                url: url + "/" + action,
                type: "POST",
                data: formData,
                contentType: false, // Tidak mengatur contentType secara otomatis
                processData: false, // Tidak memproses data secara otomatis
                success: function (response) {
                   
                    $('.invalid-feedback').html(''); // Hapus pesan kesalahan
                    $('.is-invalid').removeClass('is-invalid'); // Hapus kelas is-invalid dari bidang-bidang yang divalidasi

                    if (action === "Simpan") {
                        send();
                    } else {
                        update(id_expense);
                    }

                },
                error: function (xhr) {
                    var errors = xhr.responseJSON.errors;

                    // Bersihkan semua pesan kesalahan sebelum menampilkan yang baru
                    $('.fv-plugins-message-container').html('');

                    // Tampilkan pesan kesalahan untuk setiap bidang jika ada
                    if (errors) {
                        $.each(errors, function (key, value) {
                            $('#' + key + '-error').html(value[0]);
                        });
                    }
                }
            });
        });

    });

    function clearForm(){
        document.getElementById("head_title").textContent = "Tambah {{ __($title) }}";
        $('#myForm')[0].reset();
        document.getElementById("action").textContent = "Simpan";
    }

    // Fungsi untuk menampilkan notifikasi toast dengan ikon centang
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
    
    // Create Data
    function send() {
        var formData = new FormData($('#myForm')[0]); // Buat objek FormData dari formulir

        // Kirim data formulir ke server menggunakan AJAX
        $.ajax({
            url: "{{ url('expense/store') }}",
            type: "POST",
            data: formData,
            contentType: false, // Biarkan jQuery menentukan contentType secara otomatis
            processData: false, // Biarkan jQuery menangani proses data secara otomatis
            success: function (response) {
                showSuccessToast(response.message); // Tampilkan notifikasi toast
                $('#myForm')[0].reset(); // Reset form setelah berhasil menambahkan data
                $('#exampleModal').modal('hide');
                table.ajax.reload(null, false);
            },
            error: function (xhr) {
                // Tangani kesalahan jika pengiriman formulir gagal
                console.error("Error pengiriman formulir:", xhr);
            }
        });
    }
        
    // Get Data
    function getData(id){
        document.getElementById("head_title").textContent = "Ubah {{ __($title) }}";
        document.getElementById("action").textContent = "Update";
        // Kirim data formulir ke server menggunakan AJAX

        var url = "{{ url('/expense/edit') }}";
        $.ajax({
            url: url + "/" + id,
            type: "GET",
            success: function (response) {
                document.getElementById("id_expense").value = response.data.id;
                document.getElementById("date").value = response.data.date;
                document.getElementById("transaction_code_id").value = response.data.transaction_code_id;
                document.getElementById("desc_category_id").value = response.data.desc_category_id;
                document.getElementById("desc").value = response.data.desc;
                document.getElementById("amount").value = response.data.amount;
            },
            error: function (xhr) {
                // Tangani kesalahan jika pengiriman formulir gagal
                showFailedToast(xhr); // Tampilkan notifikasi toast untuk keberhasilan
                console.error("Error pengiriman formulir:", xhr);
            }
        });
    }

    // Update Data
    function update(id) {
        var formData = new FormData($('#myForm')[0]); // Buat objek FormData dari formulir
        formData.append('_token', "{{ csrf_token() }}");
        formData.append('_method', "PUT");
        
        // Kirim data formulir ke server menggunakan AJAX

        var url = "{{ url('/expense/edit') }}";
        $.ajax({
            url: url + "/" + id,
            type: "POST",
            data: formData,
            contentType: false, // Biarkan jQuery menentukan contentType secara otomatis
            processData: false, // Biarkan jQuery menangani proses data secara otomatis
            success: function (response) {
                showSuccessToast(response.message); // Tampilkan notifikasi toast untuk keberhasilan
                $('#myForm')[0].reset(); // Reset form setelah berhasil memperbarui data
                $('#exampleModal').modal('hide'); // Tutup modal setelah berhasil memperbarui data
                table.ajax.reload(null, false); // Muat ulang DataTables setelah update
            },
            error: function (xhr) {
                // Tangani kesalahan jika pengiriman formulir gagal
                showFailedToast(xhr); // Tampilkan notifikasi toast untuk keberhasilan
                console.error("Error pengiriman formulir:", xhr);
            }
        });
    }
    
    // Delete Data
    function deleteData(id) {
        swal({
			title: 'Apakah Kamu Yakin?',
			text: "Anda tidak akan dapat mengembalikan ini!",
			type: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Delete',
			padding: '2em'
		}).then(function (result) {
			if (result.value) {
				swal(
					'Deleted!',
					'Data Berhasil Dihapus.',
					'success'
				).then(function () {
					var url = "{{ url('/expense/delete') }}";
                    $.ajax({
                        url: url + "/" + id,
                        success: function (response) {
                            showSuccessToast(response.message);
                            $('#myForm')[0].reset();
                            table.ajax.reload(null, false);
                        },
                        error: function (xhr) {
                            showFailedToast(xhr); // Tampilkan notifikasi toast untuk keberhasilan
                            console.error("Error pengiriman formulir:", xhr);
                        }
                    });
				});
			}
		});
	
    }



</script>
@endsection