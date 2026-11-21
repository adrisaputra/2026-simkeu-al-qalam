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
                                <h4>Tagihan Siswa</h4>
                            </div>
                            <div class="col-md-4 text-md-right mb-3 mb-md-0">
                                <a href="{{ route('student_bill.create') }}" class="btn mb-2 mr-1 btn-success" >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus-circle"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg> Buat Tagihan Siswa
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="widget-content widget-content-area">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if($errors->has('bill'))
                            <div class="alert alert-danger">{{ $errors->first('bill') }}</div>
                        @endif

                        <div class="row mb-4">
                            <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                                <a href="#">
                                    <div class="widget widget-one_hybrid widget-followers" style="background:#2196f3;padding: 20px 20px 0px 20px;">
                                        <div class="widget-heading">
                                            <div class="row" style="color: #ffffff;">
                                                <div class="col-md-12">
                                                    <p class="w-value" style="color: #ffffff;font-size: 28px;">{{ \App\Helpers\Helpers::format_number($summary->total_amount) }}</p>
                                                    <h6 style="color: #ffffff;">Total Tagihan</h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                                <a href="#">
                                    <div class="widget widget-one_hybrid widget-followers" style="background:#8bc34a;padding: 20px 20px 0px 20px;">
                                        <div class="widget-heading">
                                            <div class="row" style="color: #ffffff;">
                                                <div class="col-md-12">
                                                    <p class="w-value" style="color: #ffffff;font-size: 28px;">{{ \App\Helpers\Helpers::format_number($summary->paid_amount) }}</p>
                                                    <h6 style="color: #ffffff;">Terbayar</h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                                <a href="#">
                                    <div class="widget widget-one_hybrid widget-followers" style="background:#e71717e0;padding: 20px 20px 0px 20px;">
                                        <div class="widget-heading">
                                            <div class="row" style="color: #ffffff;">
                                                <div class="col-md-12">
                                                    <p class="w-value" style="color: #ffffff;font-size: 28px;">{{ \App\Helpers\Helpers::format_number($summary->remaining_amount) }}</p>
                                                    <h6 style="color: #ffffff;">Sisa Tagihan</h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <h5>Filter Data</h5>
                        <form method="GET" action="{{ route('student_bill.index') }}" class="mb-4">
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label for="work_unit_id">Jenjang</label>
                                    <select id="work_unit_id" name="work_unit_id" class="form-control form-control-sm" onchange="this.form.class_id.value=''; this.form.student_bill_type_id.value='';getClasses();getStudentBillType()">
                                        <option value="">Semua Jenjang</option>
                                        @foreach($workUnits as $workUnit)
                                            <option value="{{ $workUnit->id }}" @selected(($filters['work_unit_id'] ?? '') == $workUnit->id)>
                                                {{ $workUnit->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="academic_year_id">Tahun Ajaran</label>
                                    <select id="academic_year_id" name="academic_year_id" class="form-control form-control-sm">
                                        <option value="">Semua Tahun Ajaran</option>
                                        @foreach($academicYears as $academicYear)
                                            <option value="{{ $academicYear->id }}" @selected(($filters['academic_year_id'] ?? '') == $academicYear->id)>
                                                {{ $academicYear->name }}{{ $academicYear->is_active ? ' (Aktif)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="class_id">Kelas</label>
                                    <select id="class_id" name="class_id" class="form-control form-control-sm">
                                        <option value="">Semua Kelas</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" @selected(($filters['class_id'] ?? '') == $class->id)>
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="student_bill_type_id">Jenis Tagihan</label>
                                    <select id="student_bill_type_id" name="student_bill_type_id" class="form-control form-control-sm">
                                        <option value="">Semua Tagihan</option>
                                        @if(!empty($filters['work_unit_id']))
                                            @foreach($billTypes as $billType)
                                                <option value="{{ $billType->id }}" @selected(($filters['student_bill_type_id'] ?? '') == $billType->id)>
                                                    {{ $billType->transaction_code?->name ?? $billType->name ?? 'Jenis Tagihan '.$billType->id }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="status">Status</label>
                                    <select id="status" name="status" class="form-control form-control-sm">
                                        <option value="">Semua Status</option>
                                        <option value="outstanding" @selected(($filters['status'] ?? '') === 'outstanding')>Ada Tunggakan</option>
                                        <option value="partial" @selected(($filters['status'] ?? '') === 'partial')>Terbayar Sebagian</option>
                                        <option value="paid" @selected(($filters['status'] ?? '') === 'paid')>Lunas</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-9">
                                    <label for="search">Cari Siswa</label>
                                    <input id="search" name="search" type="search" class="form-control form-control-sm" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nama siswa...">
                                </div>
                                <div class="form-group col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn mb-1 mr-1 btn-info"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></button>
                                    <a href="{{ route('student_bill.index') }}" class="btn mb-1 mr-1 btn-warning" data-toggle="tooltip" data-placement="top" title="Refresh"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-ccw"><polyline points="1 4 1 10 7 10"></polyline><polyline points="23 20 23 14 17 14"></polyline><path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"></path></svg></a>
                                </div>
                            </div>
                        </form>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Daftar Siswa</h5>
                            <small class="text-muted">{{ $students->total() }} siswa</small>
                        </div>

                        @forelse($students as $student)
                            @php
                                $totalAmount = (float) $student->total_amount;
                                $paidAmount = (float) $student->paid_amount;
                                $remainingAmount = (float) $student->remaining_amount;
                                $unitName = $workUnits->firstWhere('id', $student->work_unit_id)?->name;
                            @endphp
                            <div class="card mb-3">
                                <div class="card-body"  style="color:#515365">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <b>{{ $student->name }}</b>
                                            <div class="text-muted small">
                                                {{ $unitName ?? 'Unit '.$student->work_unit_id }} - Kelas {{ $student->class_name }}
                                            </div>
                                        </div>
                                        @if($totalAmount <= 0)
                                            <span class="badge badge-secondary">Belum Ada Tagihan</span>
                                        @elseif($remainingAmount <= 0)
                                            <span class="badge badge-success">Lunas</span>
                                        @else
                                            <span class="badge badge-danger">Ada Tunggakan</span>
                                        @endif
                                    </div>
                                    <hr>
                                    <div class="row" style="font-size: 16px;">
                                        <div class="col-md-4 mb-2">
                                            <small class="text-muted d-block">Total</small>
                                            <b>Rp{{ \App\Helpers\Helpers::format_number($totalAmount) }}</b>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <small class="text-muted d-block">Terbayar</small>
                                            <b class="text-success">Rp{{ \App\Helpers\Helpers::format_number($paidAmount) }}</b>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <small class="text-muted d-block">Sisa</small>
                                            <b class="{{ $remainingAmount > 0 ? 'text-danger' : 'text-success' }}">
                                                Rp{{ \App\Helpers\Helpers::format_number($remainingAmount) }}
                                            </b>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <a href="{{ route('student_bill.show', ['student' => $student->id, 'academic_year_id' => $filters['academic_year_id'] ?? null]) }}" class="btn btn-outline-info btn-sm">
                                            Detail Tagihan
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-info mb-0">Tidak ada siswa yang sesuai dengan filter.</div>
                        @endforelse

                        <div class="mt-3">{{ $students->links() }}</div>
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
            '<option value="">- Pilih Kelas-</option>'
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
