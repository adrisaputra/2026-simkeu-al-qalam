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
                            <line x1="6" y1="18" x2="18" y2="6"></line>
                        </svg>
                    </button>
                </div>

                <div class="modal-body">
                    <input type="hidden" class="form-control form-control-sm" name="id" id="id_balance_sheet_detail" />
                    {{--<div class="form-group">
                        <p>{{ __('Akun Neraca') }} <span class="required" style="color: #dd4b39;">*</span></p>
                        <select class="form-control form-control-sm" name="balance_sheet_account_id" id="balance_sheet_account_id">
                            <option value="">- Pilih Akun Neraca -</option>
                            @foreach($balance_sheet_accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name }}{{ $account->is_active ? '' : ' (Tidak Aktif)' }}</option>
                            @endforeach
                        </select>
                        <div id="balance_sheet_account_id-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
                    </div>--}}

                    <div class="form-group">
                        <p>{{ __('Tahun') }} <span class="required" style="color: #dd4b39;">*</span></p>
                        <input type="text" class="form-control form-control-sm" name="year" id="year" readonly>
                        <div id="year-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
                    </div>

                    <div class="form-group">
                        <p>{{ __('Jumlah Saldo') }} <span class="required" style="color: #dd4b39;">*</span></p>
                        <input type="text" class="form-control form-control-sm" name="amount" id="amount" onkeyup="formatRupiah(this, '.')">
                        <div id="amount-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
                    </div>

                    <div class="form-group">
                        <p>{{ __('Catatan') }}</p>
                        <textarea class="form-control form-control-sm" name="notes" id="notes"></textarea>
                        <div id="notes-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
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
