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
                    <div class="form-group">
                        <label for="income_import_desc">{{ __('Uraian') }} <span class="required" style="color: #dd4b39;">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="desc" id="income_import_desc" maxlength="1000" required>
                    </div>

                    <div class="form-group">
                        <label for="income_import_desc_category_id">{{ __('Kategori Uraian') }} <span class="required" style="color: #dd4b39;">*</span></label>
                        <select class="form-control form-control-sm" name="desc_category_id" id="income_import_desc_category_id" required>
                            <option value="">- Pilih -</option>
                            @foreach($desc_category as $v)
                                <option value="{{ $v->id }}">{{ $v->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="income_import_file">{{ __('File Import') }} <span class="required" style="color: #dd4b39;">*</span></label>
                        <input type="file" class="form-control form-control-sm" name="file" id="income_import_file" accept=".xlsx,.xls" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Tutup</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </div>
        </div>
    </div>
</form>
