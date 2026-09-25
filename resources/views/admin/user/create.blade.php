<form id="myForm" action="{{ url('/'.Request::segment(1)) }}" method="POST" enctype="multipart/form-data" class="form-horizontal">
	{{ csrf_field() }}

	<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="head_title">Modal title</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
							<line x1="18" y1="6" x2="6" y2="18"></line>
							<line x1="6" y1="6" x2="18" y2="18"></line>
						</svg>
					</button>
				</div>
				<div class="modal-body">
					<input type="hidden" class="form-control" name="id" id="id_user" />
					<div class="form-group">
						<p>{{ __('Nama Pengguna') }} <span class="required" style="color: #dd4b39;">*</span></p>
						<input type="text" class="form-control form-control-sm" name="name" id="name">
						<div id="name-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
					</div>

					<div class="form-group">
						<p>{{ __('Email') }} <span class="required" style="color: #dd4b39;">*</span></p>
						<input type="text" class="form-control form-control-sm" name="email" id="email">
						<div id="email-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
					</div>

					<div class="form-group">
						<p>{{ __('Password') }} <span class="required" style="color: #dd4b39;">*</span></p>
						<div class="input-group">
							<input type="password" class="form-control form-control-sm" name="password" id="password">
							<div class="input-group-append">
								<span class="input-group-text">
									<a href="#" onclick="togglePassword('password', this); return false;">
										<svg xmlns="http://www.w3.org/2000/svg" style="top: 48px;" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" id="toggle-password" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
									</a>
								</span>
							</div>
						</div>
						<div id="password-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
					</div>

					<div class="form-group">
						<p>{{ __('Konfirmasi Password') }} <span class="required" style="color: #dd4b39;">*</span></p>
						<div class="input-group">
							<input type="password" class="form-control form-control-sm" name="password_confirmation" id="password_confirmation">
							<div class="input-group-append">
								<span class="input-group-text">
									<a href="#" onclick="togglePassword('password_confirmation', this); return false;">
										<svg xmlns="http://www.w3.org/2000/svg" style="top: 48px;" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" id="toggle-password" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
									</a>
								</span>
							</div>
						</div>
						<div id="password_confirmation-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
					</div>

					<div class="form-group">
						<p>{{ __('Grup') }} <span class="required" style="color: #dd4b39;">*</span></p>
						<select class="form-control form-control-sm" name="group_id" id="group_id" onchange=" if (this.selectedIndex==1){ 
								document.getElementById('show_work_unit').style.display = 'none'; 
							} else if (this.selectedIndex==2){ 
								document.getElementById('show_work_unit').style.display = 'inline'; 
							};">
							<option value="">- Pilih -</option>
							<option value="4">Admin KPI</option>
							<option value="5">Admin Unit</option>
						</select>
						<div id="group_id-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
					</div>


					<div class="form-group" id="show_work_unit" style="display:none">
						<p>{{ __('Unit Kerja') }} <span class="required" style="color: #dd4b39;">*</span></p>
						<select class="form-control form-control-sm" name="work_unit_id" id="work_unit_id">
							<option value="">- Pilih -</option>
							@foreach($work_unit as $v)
								<option value="{{ $v->id }}">{{ $v->name }}</option>
							@endforeach
						</select>
						<div id="work_unit_id-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
					</div>

					<div class="form-group" style="margin-top:20px">
						<p>{{ __('Status') }} <span class="required" style="color: #dd4b39;">*</span></p>
						<select class="form-control form-control-sm" name="status" id="status">
							<option value="">- Pilih Status -</option>
							<option value="Active">Aktif</option>
							<option value="Non Active">Tidak Aktif</option>
						</select>
						<div id="status-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
					</div>

				</div>
				<div class="modal-footer">
					<button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Tutup</button>
					<button type="submit" class="btn btn-primary" id="action" title="Tambah Data"> Simpan</button>
				</div>
			</div>
		</div>
	</div>

</form>
<script>
    function togglePassword(fieldId, toggleElement) {
        const input = document.getElementById(fieldId);
        if (input.type === "password") {
            input.type = "text";
        } else {
            input.type = "password";
        }
    }
</script>
