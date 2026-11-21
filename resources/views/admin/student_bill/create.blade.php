@extends('admin.layout')

@section('content')
<div id="content" class="main-content">
    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div class="col-12">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4>Buat Tagihan Siswa</h4>
                            </div>
                            <div class="col-md-4 text-md-right mb-3 mb-md-0">
                                 <a href="{{ url(Request::segment(1)) }}" class="btn mb-2 mr-1 btn-danger" data-toggle="tooltip" data-placement="top" title="Kembali"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left-circle"><circle cx="12" cy="12" r="10"></circle><polyline points="12 8 8 12 12 16"></polyline><line x1="16" y1="12" x2="8" y2="12"></line></svg></a>	
                            </div>
                        </div>
                    </div>

                    <div class="widget-content widget-content-area">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="GET" action="{{ route('student_bill.create') }}" class="mb-4">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="work_unit_id">Unit Kerja</label>
                                    <select id="work_unit_id" name="work_unit_id" class="form-control form-control-sm" onchange="this.form.class_id.value=''; this.form.student_bill_type_id.value=''; getClasses();getStudentBillType();">
                                        <option value="">Pilih Unit Kerja</option>
                                        @foreach($workUnits as $workUnit)
                                            <option value="{{ $workUnit->id }}" @selected(($filters['work_unit_id'] ?? '') == $workUnit->id)>
                                                {{ $workUnit->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="academic_year_id">Tahun Ajaran</label>
                                    <select id="academic_year_id" name="academic_year_id" class="form-control form-control-sm" onchange="this.form.submit()">
                                        <option value="">Pilih Tahun Ajaran</option>
                                        @foreach($academicYears as $academicYear)
                                            <option value="{{ $academicYear->id }}" @selected(($selectedYear?->id ?? '') == $academicYear->id)>
                                                {{ $academicYear->name }}{{ $academicYear->is_active ? ' (Aktif)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="student_bill_type_id">Jenis Tagihan</label>
                                    <select id="student_bill_type_id" name="student_bill_type_id" class="form-control form-control-sm" onchange="this.form.submit()">
                                        <option value="">Pilih Jenis Tagihan</option>
                                        @if(!empty($filters['work_unit_id']))
                                            @foreach($billTypes as $billType)
                                                <option value="{{ $billType->id }}" @selected(($selectedBillType?->id ?? '') == $billType->id)>
                                                    {{ $billType->transaction_code?->name ?? $billType->name ?? 'Jenis Tagihan '.$billType->id }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="class_id">Kelas (Opsional)</label>
                                    <select id="class_id" name="class_id" class="form-control form-control-sm" onchange="this.form.submit()">
                                        <option value="">Semua Kelas</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" @selected(($filters['class_id'] ?? '') == $class->id)>
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @if($periods)
                                    <div class="form-group col-md-6">
                                        <label for="period_start">Periode</label>
                                        <select id="period_start" name="period_start" class="form-control form-control-sm" onchange="this.form.submit()">
                                            @foreach($periods as $period)
                                                <option value="{{ $period['period_start'] }}" @selected($selectedPeriod['period_start'] === $period['period_start'])>
                                                    {{ $period['label'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </div>
                        </form>

                        @if($selectedYear && $selectedBillType && $selectedPeriod)
                            <hr>
                            <h5>Daftar Siswa dan Nominal</h5>
                            <p class="text-muted">
                                {{ $selectedBillType->transaction_code?->name ?? $selectedBillType->name ?? 'Jenis Tagihan '.$selectedBillType->id }}
                                &middot; {{ $selectedYear->name }}
                                &middot; {{ $selectedPeriod['label'] }}
                            </p>

                            @if($students->isEmpty())
                                <div class="alert alert-info">Tidak ada siswa aktif untuk unit dan kelas yang dipilih.</div>
                            @else
                                <form method="POST" action="{{ route('student_bill.store') }}">
                                    @csrf
                                    <input type="hidden" name="work_unit_id" value="{{ $filters['work_unit_id'] }}">
                                    <input type="hidden" name="academic_year_id" value="{{ $selectedYear->id }}">
                                    <input type="hidden" name="student_bill_type_id" value="{{ $selectedBillType->id }}">
                                    <input type="hidden" name="class_id" value="{{ $filters['class_id'] ?? '' }}">
                                    <input type="hidden" name="period_start" value="{{ $selectedPeriod['period_start'] }}">

                                    <div class="form-group">
                                        <label for="due_date">Jatuh Tempo</label>
                                        <input id="due_date" name="due_date" type="date" class="form-control" value="{{ old('due_date') }}">
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Siswa</th>
                                                    <th>Kelas</th>
                                                    <th style="min-width: 220px">Nominal (Rp)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($students as $i => $student)
                                                    @php
                                                        $existingBill = $existingBills->get($student->id);
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $i + 1 }}</td>
                                                        <td>{{ $student->name }}</td>
                                                        <td>{{ $student->classes?->name ?? '-' }}</td>
                                                        <td>
                                                            @if($existingBill)
                                                                <span class="text-muted">
                                                                    Sudah diterbitkan: Rp{{ \App\Helpers\Helpers::format_number($existingBill->amount) }}
                                                                </span>
                                                            @else
                                                                <input
                                                                    type="text"
                                                                    name="amounts[{{ $student->id }}]"
                                                                    class="form-control form-control-sm @error('amounts.'.$student->id) is-invalid @enderror"
                                                                    step="0.01"
                                                                    value="{{ \App\Helpers\Helpers::format_number(old('amounts.'.$student->id, $selectedBillType->default_amount)) }}"
                                                                    required
                                                                    onkeyup="formatRupiah(this, '.')"
                                                                >
                                                                @error('amounts.'.$student->id)
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    @if($students->contains(fn ($student) => ! $existingBills->has($student->id)))
                                        <button type="submit" class="btn btn-success">Simpan Tagihan</button>
                                    @else
                                        <div class="alert alert-info mb-0">Semua siswa sudah memiliki tagihan untuk periode ini.</div>
                                    @endif
                                </form>
                            @endif
                        @else
                            <div class="alert alert-light border mb-0">
                                Pilih unit kerja, tahun ajaran, dan jenis tagihan untuk menampilkan daftar siswa.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('backend/assets/js/jquery-3.4.1.min.js')}}"></script>
<script>
    
    function getClasses(selectedClassId = null) {
        var work_unit_id = $("#work_unit_id").val();

        $("#class_id").html(
            '<option value="">- Pilih Kelas -</option>'
        );

        if (!work_unit_id) {
            return false;
        }

        var url = "{{ url('/classes/get') }}";

        $.ajax({
            url: url + "/" + work_unit_id,
            type: "GET",

            success: function (html) {
                $("#class_id").html(html);

                // Pilih kelas setelah opsi selesai dimuat
                if (selectedClassId !== null) {
                    $("#class_id").val(String(selectedClassId));
                }
            },

            error: function (xhr) {
                console.error(xhr.responseText);
            }
        });

        return false;
    }


    function getStudentBillType(selectedClassId = null) {
        var work_unit_id = $("#work_unit_id").val();

        $("#student_bill_type_id").html(
            '<option value="">- Pilih Jenis Tagihan -</option>'
        );

        if (!work_unit_id) {
            return false;
        }

        var url = "{{ url('/student_bill_type/get') }}";

        $.ajax({
            url: url + "/" + work_unit_id,
            type: "GET",

            success: function (html) {
                $("#student_bill_type_id").html(html);

                // Pilih kelas setelah opsi selesai dimuat
                if (selectedClassId !== null) {
                    $("#student_bill_type_id").val(String(selectedClassId));
                }
            },

            error: function (xhr) {
                console.error(xhr.responseText);
            }
        });

        return false;
    }

</script>
@endsection
