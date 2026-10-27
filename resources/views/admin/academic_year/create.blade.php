<form action="{{ route('academic_years.store') }}" method="POST" class="form-horizontal">
    @csrf
    <input type="hidden" name="_form_context" value="create">

    <div class="modal fade" id="academicYearModal" tabindex="-1" role="dialog" aria-labelledby="academicYearModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="academicYearModalLabel">Tambah Tahun Ajaran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label for="academic-year-name">Nama Tahun Ajaran</label>
                        <input id="academic-year-name" name="name" type="text" class="form-control" value="{{ old('name') }}" placeholder="2026/2027" required>
                    </div>
                    <div class="form-group">
                        <label for="academic-year-start-date">Tanggal Mulai</label>
                        <input id="academic-year-start-date" name="start_date" type="date" class="form-control" value="{{ old('start_date') }}">
                    </div>
                    <div class="form-group mb-0">
                        <label for="academic-year-end-date">Tanggal Selesai</label>
                        <input id="academic-year-end-date" name="end_date" type="date" class="form-control" value="{{ old('end_date') }}">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</form>
