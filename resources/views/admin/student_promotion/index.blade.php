@extends('admin.layout')
@section('content')
<div id="content" class="main-content">
    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div class="col-lg-12 col-12 layout-spacing">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header">
                        <div class="row">
                            <div class="col-12">
                                <h4>{{ $title }}</h4>
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

                        <div class="mb-3">
                            <a href="{{ route('academic_years.index') }}" class="btn btn-danger">Kembali ke Tahun Ajaran</a>
                        </div>

                        @if (!$activeYear)
                            <div class="alert alert-warning">Belum ada tahun ajaran aktif. Aktifkan tahun ajaran sumber terlebih dahulu.</div>
                        @elseif ($academicYears->isEmpty())
                            <div class="alert alert-warning">Tambahkan tahun ajaran tujuan sebelum memproses kenaikan kelas.</div>
                        @elseif ($sourceClasses->isEmpty())
                            <div class="alert alert-warning">Belum ada siswa yang terdaftar pada kelas mana pun.</div>
                        @else
                            <div class="alert alert-info">
                                Tahun ajaran sumber: <strong>{{ $activeYear->name }}</strong>.
                                Siswa yang kelas tujuannya dikosongkan tidak diubah. Proses ini tidak otomatis mengaktifkan tahun ajaran tujuan.
                            </div>

                            <form action="{{ route('student_promotions.store') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="target_academic_year_id">Tahun Ajaran Tujuan</label>
                                    <select id="target_academic_year_id" name="target_academic_year_id" class="form-control" required>
                                        <option value="">- Pilih Tahun Ajaran -</option>
                                        @foreach ($academicYears as $academicYear)
                                            <option value="{{ $academicYear->id }}" @selected(old('target_academic_year_id') == $academicYear->id)>
                                                {{ $academicYear->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Jenjang</th>
                                                <th>Kelas Asal</th>
                                                <th>Jumlah Siswa</th>
                                                <th>Kelas Tujuan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($sourceClasses as $sourceClass)
                                                <tr>
                                                    <td>{{ $sourceClass->work_unit->name ?? '-' }}</td>
                                                    <td>{{ $sourceClass->name }}</td>
                                                    <td>{{ $sourceClass->students_count }}</td>
                                                    <td>
                                                        <select name="mappings[{{ $sourceClass->id }}]" class="form-control">
                                                            <option value="">Tidak dipindahkan</option>
                                                            @foreach ($destinationClasses->where('work_unit_id', $sourceClass->work_unit_id) as $destinationClass)
                                                                <option value="{{ $destinationClass->id }}" @selected(old('mappings.'.$sourceClass->id) == $destinationClass->id)>
                                                                    {{ $destinationClass->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <button type="submit" class="btn btn-primary" onclick="return confirm('Simpan pemetaan dan pindahkan siswa ke tahun ajaran tujuan?')">
                                    Proses Kenaikan Kelas
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
