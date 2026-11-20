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
                    <input type="hidden" class="form-control form-control-sm" name="id" id="id_salary" />
                                        
                    <div class="form-group">
                        <p>{{ __('Nama Pegawai') }} </p>
                        <input type="text" class="form-control form-control-sm" id="employee_name" disabled >
                    </div>
        
                    <div class="form-group">
                        <p>{{ __('Gaji Pokok/Gaji Honor') }} <span class="required" style="color: #dd4b39;">*</span></p>
                        <input type="text" class="form-control form-control-sm" name="salary" id="salary" onkeyup="formatRupiah(this, '.')">
                        <div id="salary-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
                    </div>

                    <div class="form-group">
                        <p>{{ __('Tunjangan Jabatan') }}</p>
                        <input type="text" class="form-control form-control-sm" name="allowance1" id="allowance1" onkeyup="formatRupiah(this, '.')">
                        <div id="allowance1-error" class="fv-plugins-message-container invalid-feedback"></div>
                    </div>

                    <div class="form-group">
                        <p>{{ __('Tunjangan Keluarga') }}</p>
                        <select class="form-control form-control-sm" name="allowance3" id="allowance3">
                            <option value="">- Pilih -</option>
                            <option value="1">Terima Tunjangan</option>
                            <option value="0">Tidak Terima Tunjangan</option>
                        </select>
                        <div id="allowance3-error" class="fv-plugins-message-container invalid-feedback"></div>
                    </div>
                    
                    <div class="form-group">
                        <p>{{ __('Tunjangan Kesehatan') }}</p>
                        <input type="text" class="form-control form-control-sm" name="allowance2" id="allowance2" onkeyup="formatRupiah(this, '.')">
                        <div id="allowance2-error" class="fv-plugins-message-container invalid-feedback"></div>
                    </div>

                    {{--<div class="form-group">
                        <p>{{ __('Bonus TTQ') }}</p>
                        <input type="text" class="form-control form-control-sm" name="ttq" id="ttq" onkeyup="formatRupiah(this, '.')">
                        <div id="ttq-error" class="fv-plugins-message-container invalid-feedback"></div>
                    </div>--}}

                    <div class="form-group">
                        <p>{{ __('Penyesuaian') }}</p>
                        <input type="text" class="form-control form-control-sm" name="salary_adjustment" id="salary_adjustment" onkeyup="formatRupiah(this, '.')">
                        <div id="salary_adjustment-error" class="fv-plugins-message-container invalid-feedback"></div>
                    </div>

                    <div class="form-group">
                        <p>{{ __('BOS') }}</p>
                        <input type="text" class="form-control form-control-sm" name="bos" id="bos" onkeyup="formatRupiah(this, '.')">
                        <div id="bos-error" class="fv-plugins-message-container invalid-feedback"></div>
                    </div>

                    {{--<div class="form-group">
                        <p>{{ __('Kelebihan JP') }}</p>
                        <input type="text" class="form-control form-control-sm" name="jp" id="jp" onkeyup="formatRupiah(this, '.')">
                        <div id="jp-error" class="fv-plugins-message-container invalid-feedback"></div>
                    </div>--}}

                    <div class="form-group">
                        <p>{{ __('Uang Makan') }}</p>
                        <input type="text" class="form-control form-control-sm" name="meal_allowance" id="meal_allowance" onkeyup="formatRupiah(this, '.')">
                        <div id="meal_allowance-error" class="fv-plugins-message-container invalid-feedback"></div>
                    </div>

                    <div class="form-group">
                        <p>{{ __('Pot. Rumbay') }}</p>
                        <input type="text" class="form-control form-control-sm" name="rumbay" id="rumbay" onkeyup="formatRupiah(this, '.')">
                        <div id="rumbay-error" class="fv-plugins-message-container invalid-feedback"></div>
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