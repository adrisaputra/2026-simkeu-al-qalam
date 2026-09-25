@extends('admin.layout')
@section('content')
<!--  BEGIN CONTENT AREA  -->
<div id="content" class="main-content">
	<div class="layout-px-spacing">

		<div class="row layout-top-spacing">
			<div id="tableHover" class="col-lg-12 col-12 layout-spacing">
				<div class="statbox widget box box-shadow">
					<div class="widget-header">
						<div class="row">
							<div class="col-xl-12 col-md-12 col-sm-12 col-12">
								<h4>{{ __($title)}}</h4>
							</div>
						</div>
					</div>
					<div class="widget-content widget-content-area" style="padding-top: 0px;">
						<!--begin::Form-->
						<form id="myForm" action="{{ url('/'.Request::segment(1)) }}" method="POST" enctype="multipart/form-data" class="form-horizontal">
							{{ csrf_field() }}

							@if ($message = Session::get('status'))
							<div class="alert alert-info mb-4" role="alert">
								<button type="button" class="close" data-dismiss="alert" aria-label="Close">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-dismiss="alert">
										<line x1="18" y1="6" x2="6" y2="18"></line>
										<line x1="6" y1="6" x2="18" y2="18"></line>
									</svg>
								</button>
								<h4 style="color: #ffffff;"><i class="icon fa fa-check"></i> Berhasil !</h4>
								{{ $message }}
							</div>
							@endif

							<input type="hidden" class="form-control" id="id_user" value="{{ Crypt::encrypt($user->id) }}" />
							<div class="form-group row mb-4">
								<label class="col-xl-3 col-sm-3 col-sm-2 col-form-label">{{ __('Nama Pengguna') }} <span class="required" style="color: #dd4b39;">*</span></label>
								<div class="col-xl-9 col-lg-9 col-sm-10">
									<input type="text" class="form-control form-control-sm" placeholder="Nama User" name="name" id="name" value="{{ $user->name }}">
									<input type="hidden" class="form-control" placeholder="Nama User" name="name2" value="{{ $user->name }}">
									<div id="name-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
								</div>
							</div>

							<div class="form-group row mb-4">
								<label class="col-xl-3 col-sm-3 col-sm-2 col-form-label">{{ __('Email') }} <span class="required" style="color: #dd4b39;">*</span></label>
								<div class="col-xl-9 col-lg-9 col-sm-10">
									<input type="email" class="form-control form-control-sm" name="email" id="email" value="{{ $user->email }}">
									<div id="email-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
								</div>
							</div>

							<div class="form-group row mb-4">
								<label class="col-xl-3 col-sm-3 col-sm-2 col-form-label">{{ __('Foto User') }} <span class="required" style="color: #dd4b39;">*</span></label>
								<div class="col-xl-9 col-lg-9 col-sm-10">
									<input type="file" class="form-control form-control-sm" placeholder="Foto" name="photo" id="photo" value="{{ $user->photo }}">
									<span style="font-size:11px"><i>Ukuran File Tidak Boleh Lebih Dari 300 Kb (jpg,jpeg,png)</i></span>
									@if($user->photo)
									<br><img src="{{ asset('storage/upload/photo/'.$user->photo) }}" width="150px" height="150px">
									@endif
									<div id="phone-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
								</div>
							</div>

							<hr style="border-top: 1px solid #d4d8e0;">

							<div class="form-group row mb-4">
								<label class="col-xl-3 col-sm-3 col-sm-2 col-form-label">{{ __('Password Lama') }} <span class="required" style="color: #dd4b39;">*</span></label>
								<div class="col-xl-9 col-lg-9 col-sm-10">
									<input type="password" class="form-control form-control-sm" name="current-password" id="current-password">
									<div id="current-password-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
								</div>
							</div>

							<div class="form-group row mb-4">
								<label class="col-xl-3 col-sm-3 col-sm-2 col-form-label">{{ __('Password Baru') }} <span class="required" style="color: #dd4b39;">*</span></label>
								<div class="col-xl-9 col-lg-9 col-sm-10">
									<input type="password" class="form-control form-control-sm" name="password" id="password">
									<div id="password-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
								</div>
							</div>

							<div class="form-group row mb-4">
								<label class="col-xl-3 col-sm-3 col-sm-2 col-form-label">{{ __('Konfirmasi Password') }} <span class="required" style="color: #dd4b39;">*</span></label>
								<div class="col-xl-9 col-lg-9 col-sm-10">
									<input type="password" class="form-control form-control-sm" name="password_confirmation" id="password_confirmation">
									<div id="password_confirmation-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
								</div>
							</div>

							<button type="submit" class="btn btn-success" id="action" title="Tambah Data">Simpan</button>
							<button type="reset" class="btn btn-warning">Reset</button>
						</form>
					</div>
				</div>
			</div>
		</div>

	</div>
	<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
	<script>
		var table;

		$(document).ready(function() {

			$('#myForm').submit(function(e) {
				e.preventDefault(); // Hindari pengiriman form secara default

				var action = 'edit';
				var id_user = $('#id_user').val();
				var name = $('#name').val();
				var email = $('#email').val();
				var password = $('#password').val();
				var password_confirmation = $('#password_confirmation').val();

				var photoInput = document.getElementById('photo'); // Input file gambar
				var photo = null; // Variabel untuk menyimpan file gambar

				if (photoInput && photoInput.files && photoInput.files.length > 0) {
					photo = photoInput.files[0]; // Ambil file pertama dari daftar file
				}

				// Buat objek FormData untuk mengirim data form, termasuk file
				var formData = new FormData();
				formData.append('id', id_user);
				formData.append('name', name);
				formData.append('email', email);
				formData.append('photo', photo);
				formData.append('password', password);
				formData.append('password_confirmation', password_confirmation);
				formData.append('_token', "{{ csrf_token() }}");

				// Kirim permintaan validasi ke controller via Ajax
				var url = "{{ url('/edit_profil/validate') }}";
				$.ajax({
					url: url + "/" + action,
					type: "POST",
					data: formData,
					contentType: false, // Tidak mengatur contentType secara otomatis
					processData: false, // Tidak memproses data secara otomatis
					success: function(response) {
						$('.fv-plugins-message-container').html(''); // Hapus pesan kesalahan
						$('.is-invalid').removeClass('is-invalid'); // Hapus kelas is-invalid dari bidang-bidang yang divalidasi
						update(id_user);
					},
					error: function(xhr) {
						var errors = xhr.responseJSON.errors;

						// Bersihkan semua pesan kesalahan sebelum menampilkan yang baru
						$('.fv-plugins-message-container').html('');

						// Tampilkan pesan kesalahan untuk setiap bidang jika ada
						if (errors) {
							$.each(errors, function(key, value) {
								$('#' + key + '-error').html(value[0]);
							});
						}
					}
				});
			});


		});

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

		// Update Data
		function update(id) {
			var formData = new FormData($('#myForm')[0]); // Buat objek FormData dari formulir
			formData.append('_token', "{{ csrf_token() }}");
			formData.append('_method', "PUT");

			// Kirim data formulir ke server menggunakan AJAX
			var url = "{{ url('/edit_profil') }}";
			$.ajax({
				url: url + "/" + id,
				type: "POST",
				data: formData,
				contentType: false, // Biarkan jQuery menentukan contentType secara otomatis
				processData: false, // Biarkan jQuery menangani proses data secara otomatis
				success: function(response) {
					showSuccessToast(response.message); // Tampilkan notifikasi toast untuk keberhasilan
					window.location.reload(true);
				},
				error: function(xhr) {
					// Tangani kesalahan jika pengiriman formulir gagal
					console.error("Error pengiriman formulir:", xhr);
				}
			});
		}
	</script>
	@endsection