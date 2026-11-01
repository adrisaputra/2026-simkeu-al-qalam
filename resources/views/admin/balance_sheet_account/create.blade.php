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
                    <input type="hidden" class="form-control form-control-sm" name="id" id="id_balance_sheet_account" />

                    <div class="form-group">
                        <p>{{ __('Kode Transaksi') }} <span class="required" style="color: #dd4b39;">*</span></p>
                        <select class="form-control form-control-sm" name="transaction_code_id" id="transaction_code_id" onchange="getTransactionCodeSub()">
                            <option value="">- Pilih Kode Transaksi -</option>
                            @foreach($transaction_code as $v)
                                <option value="{{ $v->id }}">{{ $v->code }} - {{ $v->name }}</option>
                            @endforeach
                        </select>
                        <div id="transaction_code_id-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
                    </div>

                    <div class="form-group">
                        <p>{{ __('Sub Kode Transaksi') }} <span class="required" style="color: #dd4b39;">*</span></p>
                        <select class="form-control form-control-sm" name="transaction_code_sub_id" id="transaction_code_sub_id">
                            <option value="">- Pilih Sub Kode Transaksi -</option>
                        </select>
                        <div id="transaction_code_sub_id-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
                    </div>

                    <div class="form-group">
                        <p>{{ __('Nama Akun Neraca') }} <span class="required" style="color: #dd4b39;">*</span></p>
                        <input type="text" class="form-control form-control-sm" name="name" id="name">
                        <div id="name-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
                    </div>

                    <div class="form-group">
                        <p>{{ __('Kategori') }} <span class="required" style="color: #dd4b39;">*</span></p>
                        <select class="form-control form-control-sm" name="category" id="category">
                            <option value="">- Pilih Kategori -</option>
                            <option value="asset">Aset</option>
                            <option value="liability">Liabilitas</option>
                            <option value="net_asset">Aset Neto</option>
                        </select>
                        <div id="category-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
                    </div>

                    <div class="form-group">
                        <p>{{ __('Status') }} <span class="required" style="color: #dd4b39;">*</span></p>
                        <select class="form-control form-control-sm" name="is_active" id="is_active">
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                        <div id="is_active-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
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
