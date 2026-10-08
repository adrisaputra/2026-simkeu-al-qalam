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
                    <input type="hidden" class="form-control form-control-sm" name="id" id="expense" />

                    <div class="form-group">
                        <p>{{ __('Tanggal') }}</p>
                        <input type="text" class="form-control form-control-sm" id="date" value="{{ date('Y-m-d') }}" name="date" placeholder="Tanggal" />
                    </div>

                    <div class="form-group" style="margin-bottom: 0rem;">
                        <p>{{ __('Kode Transaksi') }}</p>
                        <select class="basic form-control form-control-sm" name="transaction_code_id" id="transaction_code_id">
                            <option value="">- Pilih - </option>
                            @foreach($transaction_code as $v)
                                <option value="{{ $v->id }}">{{ $v->code }} || {{ $v->name }}</option>
                            @endforeach
                        </select>
                        <div id="transaction_code_id-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0rem;">
                        <p>{{ __('Kategori Uraian') }}</p>
                        <select class="basic form-control form-control-sm" name="desc_category_id" id="desc_category_id">
                            <option value="">- Pilih - </option>
                            @foreach($desc_category as $v)
                                <option value="{{ $v->id }}">{{ $v->name }}</option>
                            @endforeach
                        </select>
                        <div id="desc_category_id-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
                    </div>

                    <div class="form-group">
                        <p>{{ __('Peruntukan') }}</p>
                        <textarea class="form-control form-control-sm" name="desc" id="desc"></textarea>
                        <div id="desc-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
                    </div>

                    <div class="form-group">
                        <p>{{ __('Jumlah') }}</p>
                        <input type="text" class="form-control form-control-sm" name="amount" id="amount" onkeyup="formatRupiah(this, '.')">
                        <div id="amount-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
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