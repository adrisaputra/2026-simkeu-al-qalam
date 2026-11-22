<form action="{{ url('/'.Request::segment(1).'/import') }}" method="POST" enctype="multipart/form-data" class="form-horizontal">
    {{ csrf_field() }}

    <div class="modal fade" id="exampleModalImport" tabindex="-1" role="dialog" aria-labelledby="exampleModalImportLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalImportLabel">Import {{ __($title) }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="18" x2="18" y2="6"></line>
                        </svg>
                    </button>
                </div>

                <div class="modal-body">
                    
                    <div class="form-group" style="margin-bottom: 1rem;">
                        <p>{{ __('Kode Transaksi') }} <span class="required" style="color: #dd4b39;">*</span></p>
                        <select class="form-control form-control-sm" name="transaction_code_id" id="transaction_code_id" onchange="getTransactionCodeSub()">
                            <option value="">- Pilih - </option>
                            @foreach($transaction_code as $v)
                                <option value="{{ $v->id }}">{{ $v->code }} || {{ $v->name }}</option>
                            @endforeach
                        </select>
                        <div id="transaction_code_id-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
                    </div>

                    <div class="form-group">
                        <p>{{ __('Sub Kode Transaksi') }}</p>
                        <select class="form-control form-control-sm" name="transaction_code_sub_id" id="transaction_code_sub_id">
                            <option value="">- Pilih - </option>
                        </select>
                        <div id="transaction_code_sub_id-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
                    </div>

                    <div class="form-group">
                        <p>{{ __('Peruntukan') }}</p>
                        <input type="text" class="form-control form-control-sm" name="desc" id="import_desc">
                        <div id="desc-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
                    </div>

                    <div class="form-group">
                        <label for="income_import_file">{{ __('File Import') }} <span class="required" style="color: #dd4b39;">*</span></label>
                        <input type="file" class="form-control form-control-sm" name="file" id="income_import_file" accept=".xlsx,.xls" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Tutup</button>
                    <a href="{{ asset('storage/format_import/Format Import Transaksi.xlsx')}}" class="btn btn-warning">Download Format</a>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </div>
        </div>
    </div>
</form>
<script src="{{ asset('backend/assets/js/jquery-3.4.1.min.js')}}"></script>
<script>
    
    function getTransactionCode() {
        var account_id = $("#account_id").val();

        // Reset kode transaksi dan subkode
        $("#transaction_code_id").html(
            '<option value="">- Pilih -</option>'
        );

        $("#transaction_code_sub_id").html(
            '<option value="">- Pilih -</option>'
        );

        if (!account_id) {
            return false;
        }

        var url = "{{ url('/transaction_code/get') }}";

        $.ajax({
            url: url + "/" + account_id,
            type: "GET",

            success: function(html) {
                $("#transaction_code_id").html(html);
            },

            error: function(xhr) {
                console.error(xhr.responseText);
            }
        });

        return false;
    }


    function getTransactionCodeSub() {
        var transaction_code_id = $("#transaction_code_id").val();

        $("#transaction_code_sub_id").html(
            '<option value="">- Pilih -</option>'
        );

        if (!transaction_code_id) {
            return false;
        }

        var url = "{{ url('/transaction_code_sub/get') }}";

        $.ajax({
            url: url + "/" + transaction_code_id,
            type: "GET",

            success: function(html) {
                $("#transaction_code_sub_id").html(html);
            },

            error: function(xhr) {
                console.error(xhr.responseText);
            }
        });

        return false;
    }

</script>
