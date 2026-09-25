<!--begin::Form-->
<form id="myForm" action="{{ url('/'.Request::segment(1)) }}" method="POST" enctype="multipart/form-data" class="form-horizontal">
    {{ csrf_field() }}
    <!--begin::Card body-->
    <input type="hidden" class="form-control" id="id_setting" value="{{ $setting->id }}"/>

    <div class="form-group row mb-4">
        <label class="col-xl-3 col-sm-3 col-sm-2 col-form-label">{{ __('Alamat') }}</label>
        <div class="col-xl-9 col-lg-9 col-sm-10">
            <input type="text" name="address" id="address" class="form-control form-control-sm" placeholder="Alamat" value="{{ $setting->address }}"/>
            <div id="address-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
		</div>
    </div>
    
    <div class="form-group row mb-4">
        <label class="col-xl-3 col-sm-3 col-sm-2 col-form-label">{{ __('Telepon') }}</label>
        <div class="col-xl-9 col-lg-9 col-sm-10">
            <input type="text" name="phone" id="phone" class="form-control form-control-sm" placeholder="Telepon" value="{{ $setting->phone }}"/>
            <div id="phone-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
		</div>
    </div>
    
    <div class="form-group row mb-4">
        <label class="col-xl-3 col-sm-3 col-sm-2 col-form-label">{{ __('Email') }}</label>
        <div class="col-xl-9 col-lg-9 col-sm-10">
            <input type="email" name="email" id="email" class="form-control form-control-sm" placeholder="Email" value="{{ $setting->email }}"/>
            <div id="email-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
		</div>
    </div>
        
    <hr>
    
    <div class="form-group row mb-4">
        <label class="col-xl-3 col-sm-3 col-sm-2 col-form-label">{{ __('Nama Aplikasi') }}  <span class="required" style="color: #dd4b39;">*</span></label>
        <div class="col-xl-9 col-lg-9 col-sm-10">
            <input type="text" name="application_name" id="application_name" class="form-control form-control-sm" placeholder="Nama Aplikasi" value="{{ $setting->application_name }}"/>
            <div id="application_name-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
		</div>
    </div>
        
    <div class="form-group row mb-4">
        <label class="col-xl-3 col-sm-3 col-sm-2 col-form-label">{{ __('Singkatan Nama Aplikasi') }}  <span class="required" style="color: #dd4b39;">*</span></label>
        <div class="col-xl-9 col-lg-9 col-sm-10">
            <input type="text" name="short_application_name" id="short_application_name" class="form-control form-control-sm" placeholder="Singkatan Nama Aplikasi" value="{{ $setting->short_application_name }}"/>
            <div id="short_application_name-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
		</div>
    </div>

    <div class="form-group row mb-4">
        <label class="col-xl-3 col-sm-3 col-sm-2 col-form-label">{{ __('Logo Kecil') }}</label>
        <div class="col-xl-9 col-lg-9 col-sm-10">
            <input type="file" name="small_icon" id="small_icon" class="form-control form-control-sm" placeholder="Logo Kecil"/>
            <span style="font-size:11px"><i>Ukuran File Tidak Boleh Lebih Dari 500 Kb (jpg,jpeg,png)</i></span><br>
            <div id="show_small_icon">
            @if($setting->small_icon)
                <img src="{{ asset('storage/upload/setting/'.$setting->small_icon) }}" width="150px" height="150px">
            @endif
            </div>
        </div>
    </div>

    <div class="form-group row mb-4">
        <label class="col-xl-3 col-sm-3 col-sm-2 col-form-label">{{ __('Logo Besar') }}</label>
        <div class="col-xl-9 col-lg-9 col-sm-10">
            <input type="file" name="large_icon" id="large_icon" class="form-control" placeholder="Logo Besar"/>
            <span style="font-size:11px"><i>Ukuran File Tidak Boleh Lebih Dari 500 Kb (jpg,jpeg,png)</i></span><br>
            @if($setting->large_icon)
                <img src="{{ asset('storage/upload/setting/'.$setting->large_icon) }}" width="40%">
            @endif
        </div>
    </div>

    <div class="form-group row mb-4">
        <label class="col-xl-3 col-sm-3 col-sm-2 col-form-label">{{ __('Background Login') }}</label>
        <div class="col-xl-9 col-lg-9 col-sm-10">
            <input type="file" name="background_login" id="background_login" class="form-control" placeholder="Background Login"/>
            <span style="font-size:11px"><i>Ukuran File Tidak Boleh Lebih Dari 500 Kb (jpg,jpeg,png)</i></span><br>
            @if($setting->background_login)
                <img src="{{ asset('storage/upload/setting/'.$setting->background_login) }}" width="40%">
            @endif
        </div>
    </div>

    <button type="submit" class="btn btn-success" id="action" >Simpan</button>
	<button type="reset" class="btn btn-warning">Reset</button>
</form>
<!--end::Form-->