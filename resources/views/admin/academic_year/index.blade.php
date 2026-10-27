@extends('admin.layout')
@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

<div id="content" class="main-content">
    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div class="col-lg-12 col-12 layout-spacing">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header">
                        <div class="row">
                            <div class="col-12">
                                <h4>Manajemen {{ $title }}</h4>
                            </div>
                        </div>
                    </div>

                    <div class="widget-content widget-content-area">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-4">
                            <a href="#" class="btn btn-success" data-toggle="modal" data-target="#academicYearModal">
                                <span class="mr-1">+</span> Tambah Tahun Ajaran
                            </a>
                            <a href="{{ route('student_promotions.index') }}" class="btn btn-info">Kenaikan Kelas Siswa</a>
                        </div>

                        @include('admin.academic_year.create')

                        @foreach ($academicYears as $academicYear)
                            <form id="academic-year-update-{{ $academicYear->id }}" action="{{ route('academic_years.update', $academicYear) }}" method="POST">
                                @csrf
                                @method('PUT')
                            </form>
                        @endforeach

                        <div class="table-responsive">
                            <table id="academic-years-table" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Tahun Ajaran</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Selesai</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($academicYears as $academicYear)
                                        @php
                                            $updateFormId = 'academic-year-update-'.$academicYear->id;
                                        @endphp
                                        <tr>
                                            <td data-search="{{ $academicYear->name }}">
                                                <input
                                                    id="name-{{ $academicYear->id }}"
                                                    name="name"
                                                    type="text"
                                                    class="form-control"
                                                    value="{{ $academicYear->name }}"
                                                    form="{{ $updateFormId }}"
                                                    required
                                                >
                                            </td>
                                            <td data-order="{{ $academicYear->start_date }}" data-search="{{ $academicYear->start_date }}">
                                                <input
                                                    id="start-{{ $academicYear->id }}"
                                                    name="start_date"
                                                    type="date"
                                                    class="form-control"
                                                    value="{{ $academicYear->start_date }}"
                                                    form="{{ $updateFormId }}"
                                                >
                                            </td>
                                            <td data-order="{{ $academicYear->end_date }}" data-search="{{ $academicYear->end_date }}">
                                                <input
                                                    id="end-{{ $academicYear->id }}"
                                                    name="end_date"
                                                    type="date"
                                                    class="form-control"
                                                    value="{{ $academicYear->end_date }}"
                                                    form="{{ $updateFormId }}"
                                                >
                                            </td>
                                            <td data-search="{{ $academicYear->is_active ? 'Aktif' : 'Tidak Aktif' }}">
                                                @if ($academicYear->is_active)
                                                    <span class="badge badge-success">Aktif</span>
                                                @else
                                                    <span class="badge badge-secondary">Tidak Aktif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="submit" form="{{ $updateFormId }}" class="btn btn-sm btn-primary mb-2">Perbarui</button>
                                                @if (! $academicYear->is_active)
                                                    <form action="{{ route('academic_years.activate', $academicYear) }}" method="POST" class="mb-2">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success">Jadikan Aktif</button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('academic_years.destroy', $academicYear) }}" method="POST" class="mb-2" onsubmit="return confirm('Hapus tahun ajaran ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function () {
        $('#academic-years-table').DataTable({
            order: [[1, 'desc'], [0, 'desc']],
            pageLength: 50,
            columnDefs: [
                {targets: 4, orderable: false, searchable: false}
            ],
            language: {
                emptyTable: 'Belum ada tahun ajaran.',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ tahun ajaran',
                infoEmpty: 'Menampilkan 0 sampai 0 dari 0 tahun ajaran',
                infoFiltered: '(disaring dari _MAX_ total tahun ajaran)',
                lengthMenu: 'Tampilkan _MENU_ tahun ajaran',
                loadingRecords: 'Memuat...',
                processing: 'Memproses...',
                search: 'Cari:',
                zeroRecords: 'Tahun ajaran tidak ditemukan',
                paginate: {
                    first: 'Pertama',
                    last: 'Terakhir',
                    next: 'Berikutnya',
                    previous: 'Sebelumnya'
                }
            }
        });

        @if (old('_form_context') === 'create')
            $('#academicYearModal').modal('show');
        @endif
    });
</script>
@endsection
