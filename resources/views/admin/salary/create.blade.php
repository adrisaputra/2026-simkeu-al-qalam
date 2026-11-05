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
                    <p>{{ __('Tunjangan Transport') }}</p>
                    <input type="text" class="form-control form-control-sm" name="allowance2" id="allowance2" onkeyup="formatRupiah(this, '.')">
                    <div id="allowance2-error" class="fv-plugins-message-container invalid-feedback"></div>
                </div>

                <div class="form-group">
                    <p>{{ __('Tunjangan Komunikasi') }}</p>
                    <input type="text" class="form-control form-control-sm" name="allowance3" id="allowance3" onkeyup="formatRupiah(this, '.')">
                    <div id="allowance3-error" class="fv-plugins-message-container invalid-feedback"></div>
                </div>

                <div class="form-group">
                    <p>{{ __('Tunjangan Keluarga') }}</p>
                    <input type="text" class="form-control form-control-sm" name="allowance4" id="allowance4" onkeyup="formatRupiah(this, '.')">
                    <div id="allowance4-error" class="fv-plugins-message-container invalid-feedback"></div>
                </div>

                <div class="form-group">
                    <p>{{ __('Tunjangan Pensiun') }}</p>
                    <input type="text" class="form-control form-control-sm" name="allowance5" id="allowance5" onkeyup="formatRupiah(this, '.')">
                    <div id="allowance5-error" class="fv-plugins-message-container invalid-feedback"></div>
                </div>

                <div class="form-group">
                    <p>{{ __('Tunjangan Kesehatan') }}</p>
                    <input type="text" class="form-control form-control-sm" name="allowance6" id="allowance6" onkeyup="formatRupiah(this, '.')">
                    <div id="allowance6-error" class="fv-plugins-message-container invalid-feedback"></div>
                </div>

                <div class="form-group">
                    <p>{{ __('Penyesuaian Gaji') }}</p>
                    <input type="text" class="form-control form-control-sm" name="salary_adjustment" id="salary_adjustment" onkeyup="formatRupiah(this, '.')">
                    <div id="salary_adjustment-error" class="fv-plugins-message-container invalid-feedback"></div>
                </div>

                <div class="form-group">
                    <p>{{ __('BOS') }}</p>
                    <input type="text" class="form-control form-control-sm" name="bos" id="bos" onkeyup="formatRupiah(this, '.')">
                    <div id="bos-error" class="fv-plugins-message-container invalid-feedback"></div>
                </div>

                <div class="form-group">
                    <p>{{ __('Tunjangan Makan') }}</p>
                    <input type="text" class="form-control form-control-sm" name="meal_allowance" id="meal_allowance" onkeyup="formatRupiah(this, '.')">
                    <div id="meal_allowance-error" class="fv-plugins-message-container invalid-feedback"></div>
                </div>

                <div class="form-group">
                    <p>{{ __('Pot. Rumbay') }}</p>
                    <input type="text" class="form-control form-control-sm" name="rumbay" id="rumbay" onkeyup="formatRupiah(this, '.')">
                    <div id="rumbay-error" class="fv-plugins-message-container invalid-feedback"></div>
                </div>

                {{--<div class="form-group" style="margin-bottom: 1rem;">
                    <p>{{ __('Status') }}</p>
                    <select class="form-control form-control-sm" name="status" id="status">
                        <option value="">- Pilih - </option>
                        <option value="1">Disesuaikan</option>
                        <option value="0">Tidak Disesuaikan</option>
                    </select>
                    <div id="status-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
                </div>--}}

                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Tutup</button>
                    <button type="submit" class="btn btn-primary" id="action" title="Tambah Data"> Simpan</button>
                </div>
            </div>
        </div>
    </div>

</form>