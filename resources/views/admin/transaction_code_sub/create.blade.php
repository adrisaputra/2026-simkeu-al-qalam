<form id="myForm" action="{{ route('transaction_code_sub.store', ['transaction_code' => Crypt::encrypt($transactionCode->id)]) }}" method="POST" class="form-horizontal">
    @csrf

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
                    <input type="hidden" name="id" id="id_transaction_code_sub">

                    <div class="form-group">
                        <p>{{ __('Kode Sub Transaksi') }} <span class="required" style="color: #dd4b39;">*</span></p>
                        <input type="text" class="form-control form-control-sm" name="code" id="code" maxlength="20">
                        <div id="code-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
                    </div>

                    <div class="form-group">
                        <p>{{ __('Nama Sub Kode Transaksi') }} <span class="required" style="color: #dd4b39;">*</span></p>
                        <input type="text" class="form-control form-control-sm" name="name" id="name">
                        <div id="name-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Tutup</button>
                    <button type="submit" class="btn btn-primary" id="action" title="Tambah Data"> Simpan</button>
                </div>
            </div>
        </div>
    </div>
</form>
