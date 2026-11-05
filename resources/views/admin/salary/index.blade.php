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
									<div class="col-xl-5 col-md-12 col-sm-12 col-12">
										<a href="{{ url(Request::segment(1)) }}" class="btn mb-2 mr-1 btn-warning" data-toggle="tooltip" data-placement="top" title="Refresh"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-ccw"><polyline points="1 4 1 10 7 10"></polyline><polyline points="23 20 23 14 17 14"></polyline><path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"></path></svg></a>
										<a href="#" onclick="syncSalary()" class="btn mb-2 mr-1 btn-info" data-toggle="tooltip" data-placement="top" title="Sync Gaji"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-ccw"><polyline points="1 4 1 10 7 10"></polyline><polyline points="23 20 23 14 17 14"></polyline><path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"></path></svg> Sinkronisasi Gaji</a>
									</div>
                                    
                                    <div class="col-xl-2 col-md-12 col-sm-12 col-12">
                                        <select id="get_month" name="get_month" class="basic form-control form-control-sm" style="height: 38px;padding: 5px;">
                                            <option value="01" @if(date('m') == '01') selected @endif>Januari</option>
                                            <option value="02" @if(date('m') == '02') selected @endif>Februari</option>
                                            <option value="03" @if(date('m') == '03') selected @endif>Maret</option>
                                            <option value="04" @if(date('m') == '04') selected @endif>April</option>
                                            <option value="05" @if(date('m') == '05') selected @endif>Mei</option>
                                            <option value="06" @if(date('m') == '06') selected @endif>Juni</option>
                                            <option value="07" @if(date('m') == '07') selected @endif>Juli</option>
                                            <option value="08" @if(date('m') == '08') selected @endif>Agustus</option>
                                            <option value="09" @if(date('m') == '09') selected @endif>September</option>
                                            <option value="10" @if(date('m') == '10') selected @endif>Oktober</option>
                                            <option value="11" @if(date('m') == '11') selected @endif>November</option>
                                            <option value="12" @if(date('m') == '12') selected @endif>Desember</option>
                                        </select>
                                    </div>
                                    <div class="col-xl-2 col-md-12 col-sm-12 col-12">
                                        <select id="get_year" name="get_year" class="basic form-control form-control-sm" style="height: 38px;padding: 5px;">
                                            @for($i=2026;$i<=date('Y');$i++)
                                                <option value="{{ $i }}" @if(date('Y')==$i) selected @endif>{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                        
									<div class="col-xl-3 col-md-12 col-sm-12 col-12">
										<select id="get_work_unit" name="get_work_unit" class="basic form-control form-control-sm" style="height: 38px;padding: 5px;">
											<option value="">Pilih Unit Kerja</option>
											@foreach($work_unit as $v)
												<option value="{{ $v->id }}">{{ $v->name }}</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>
						
						    @include('admin.salary.create')

                            <div class="widget-content widget-content-area" style="padding-top: 0px;">
							@if ($message = Session::get('status'))
								<div class="alert alert-info mb-4" role="alert"> 
									<button type="button" class="close" data-dismiss="alert" aria-label="Close"> 
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
									</button> <h4 style="color: #ffffff;"><i class="icon fa fa-check"></i> Berhasil !</h4>
									{{ $message }}
								</div>     
							@endif
							<div class="table-responsive">
								<table class="table table-bordered table-hover mb-12" id="employee-table">
									<thead>
										<tr>
											<th style="width: 2%">Number</th>
											<th style="width: 2%">No</th>
											<th>NIK / Nama</th>
											<th>NIY</th>
											<th>TMT</th>
											<th>Unit Kerja</th>
											<th>Status Gaji</th>
											<th style="width: 10%"></th>
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
        table = $('#employee-table').DataTable({
            processing: true,
            serverSide: true,
            // ajax: "{{ url('employee/list') }}",
			ajax: {
				url: "{{ route('salary.list') }}",
				data: function (d) {
					d.get_month = $('#get_month').val(); // Kirim nilai combobox office dalam request
                    d.get_year = $('#get_year').val(); // Kirim nilai combobox office dalam request
					d.get_work_unit = $('#get_work_unit').val(); // Kirim nilai combobox office dalam request
                }
			},
            columns: [
				{data: 'id', name: 'id', visible: false},
				{data: 'number', name: 'number'}, // Kolom nomor urut
                {data: 'name_display', name: 'name'}, 
                {data: 'niy', name: 'employees.niy'},
                {data: 'tmt_display', name: 'employees.tmt'}, 
                {data: 'work_unit_name', name: 'work_units.name'}, // ASC/DESC jalan
                {data: 'display_status', name: 'display_status'}, // ASC/DESC jalan
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
			order: [
				[2, 'asc'] // Mengatur pengurutan kolom pertama (id) secara descending
			],
            paging: true,
            pageLength: 25, // 👈 jumlah data per halaman
			drawCallback: function () {
                var api = this.api();
                var startIndex = api.context[0]._iDisplayStart; // Indeks baris pertama di halaman
                api.column(1, {page: 'current'}).nodes().each(function (cell, i) {
                    cell.innerHTML = startIndex + i + 1; // Menghitung nomor urut berdasarkan indeks baris dan nomor halaman
                });
            }
        });
        
        // Tambahkan event listener untuk perubahan combo box office
        $('#get_month').on('change', function () {
            table.draw(); // Panggil ulang DataTable untuk memperbarui data berdasarkan filter office
        });
        $('#get_year').on('change', function () {
            table.draw(); // Panggil ulang DataTable untuk memperbarui data berdasarkan filter office
        });
        $('#get_work_unit').on('change', function () {
            table.draw(); // Panggil ulang DataTable untuk memperbarui data berdasarkan filter office
        });

        $('#myForm').submit(function (e) {
            e.preventDefault(); // Hindari pengiriman form secara default

            var action = document.getElementById('action').innerText;
            var id_salary = $('#id_salary').val();

            // Buat objek FormData untuk mengirim data form, termasuk file
            var formData = new FormData();
            formData.append('id', id_salary);
            formData.append('_token', "{{ csrf_token() }}");

            // Kirim permintaan validasi ke controller via Ajax
            var url = "{{ url('/salary/validate') }}";
            $.ajax({
                url: url,
                type: "POST",
                data: formData,
                contentType: false, // Tidak mengatur contentType secara otomatis
                processData: false, // Tidak memproses data secara otomatis
                success: function (response) {
                   
                    $('.invalid-feedback').html(''); // Hapus pesan kesalahan
                    $('.is-invalid').removeClass('is-invalid'); // Hapus kelas is-invalid dari bidang-bidang yang divalidasi

                    update(id_salary);

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
    
    // Get Data
    function getData(id){
        document.getElementById("head_title").textContent = "Ubah {{ __($title) }}";
        document.getElementById("action").textContent = "Update";
        // Kirim data formulir ke server menggunakan AJAX

        var url = "{{ url('/salary/edit') }}";
        $.ajax({
            url: url + "/" + id,
            type: "GET",
            success: function (response) {
                document.getElementById("id_salary").value = response.data.id;
                document.getElementById("salary").value = formatRupiah2(response.data.salary);
                document.getElementById("allowance1").value = formatRupiah2(response.data.allowance1);
                document.getElementById("allowance2").value = formatRupiah2(response.data.allowance2);
                document.getElementById("allowance3").value = formatRupiah2(response.data.allowance3);
                document.getElementById("allowance4").value = formatRupiah2(response.data.allowance4);
                document.getElementById("allowance5").value = formatRupiah2(response.data.allowance5);
                document.getElementById("allowance6").value = formatRupiah2(response.data.allowance6);
                document.getElementById("salary_adjustment").value = formatRupiah2(response.data.salary_adjustment);
                document.getElementById("bos").value = formatRupiah2(response.data.bos);
                document.getElementById("meal_allowance").value = formatRupiah2(response.data.meal_allowance);
                document.getElementById("rumbay").value = formatRupiah2(response.data.rumbay);
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

        var url = "{{ url('/salary/edit') }}";
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
					var url = "{{ url('/salary/delete') }}";
                    $.ajax({
                        url: url + "/" + id,
                        type: 'POST',
                        data: {_token: "{{ csrf_token() }}", _method: 'DELETE'},
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

    // Delete Data
    function syncSalary() {
        
        var month = $('#get_month').val();
        var year = $('#get_year').val();

        swal({
			title: 'Apakah Kamu Yakin Akan Melakukan Sinkronisasi Gaji Bulan ' + month + ' Tahun ' + year + '?',
			text: "Anda tidak akan dapat mengembalikan ini!",
			type: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Sinkronisasi Gaji',
			padding: '2em'
		}).then(function (result) {
			if (result.value) {
					var url = "{{ url('/salary/sync') }}";
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            month : month,
                            year : year,
                        },
                        success: function (response) {
                            swal(
                                'Berhasil!',
                                'Data Gaji Berhasil Disinkronisasi.',
                                'success'
                            ).then(function () {
                                        showSuccessToast(response.message);
                                        $('#myForm')[0].reset();
                                        table.ajax.reload(null, false);
                                        
                            });
                        },
                        error: function (xhr) {
                            showFailedToast(xhr); // Tampilkan notifikasi toast untuk keberhasilan
                            console.error("Error pengiriman formulir:", xhr);
                        }
                    });
			}
		});
	
    }
     
</script>
@endsection