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
                                <h4>Detail Tagihan Siswa</h4>
                            </div>
                            <div class="col-md-4 text-md-right mb-3 mb-md-0">
                                <a href="{{ route('student_bill.index') }}" class="btn mb-2 mr-1 btn-danger" data-toggle="tooltip" data-placement="top" title="Kembali"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left-circle">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 8 8 12 12 16"></polyline>
                                        <line x1="16" y1="12" x2="8" y2="12"></line>
                                    </svg></a>
                            </div>
                        </div>
                    </div>

                    <div class="widget-content widget-content-area">


                        <p style="font-size:16px;margin-top:-20px;">
                        <div class="row">
                            <div class="col-md-2">Nama Siswa</div>
                            <div class="col-md-10">: <b>{{ $student->name }}</b></div>
                            <div class="col-md-2">Jenjang</div>
                            <div class="col-md-10">: {{ $student->classes?->work_unit?->name ?? '-' }}</div>
                            <div class="col-md-2">Kelas</div>
                            <div class="col-md-10">: {{ $student->classes?->name ?? '-' }}</div>
                        </div>
                        </p>

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
                                                    <h6 style="color: #ffffff;">Tunggakan</h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>

                        {{--<div class="row mb-3">
                            <div class="col-md-4 mb-2">
                                <div class="card h-100"><div class="card-body">
                                    <div class="text-muted">Total Tagihan</div>
                                    <strong>Rp{{ \App\Helpers\Helpers::format_number($summary->total_amount) }}</strong>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted">Terbayar</div>
                        <strong class="text-success">Rp{{ \App\Helpers\Helpers::format_number($summary->paid_amount) }}</strong>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted">Tunggakan</div>
                        <strong class="text-danger">Rp{{ \App\Helpers\Helpers::format_number($summary->remaining_amount) }}</strong>
                    </div>
                </div>
            </div>
        </div>--}}

        <form method="GET" action="{{ route('student_bill.show', $student->id) }}" class="form-inline mb-3">
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                    <select id="academic_year_id" name="academic_year_id" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                        <option value="">Semua Tahun Ajaran</option>
                        @foreach($academicYears as $academicYear)
                        <option value="{{ $academicYear->id }}" @selected(($selectedYear?->id ?? '') == $academicYear->id)>
                            {{ $academicYear->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Jenis Tagihan</th>
                        <th>Periode</th>
                        <th>Jatuh Tempo</th>
                        <th>Nominal</th>
                        <th>Terbayar</th>
                        <th>Sisa</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bills as $bill)
                    @php
                    $billTypeName = $bill->student_bill_type?->transaction_code?->name
                    ?? $bill->student_bill_type?->name
                    ?? 'Jenis Tagihan';
                    $isPaid = (float) $bill->remaining_amount <= 0;
                        @endphp
                        <tr>
                        <td>{{ $billTypeName }}</td>
                        <td>{{ \Carbon\Carbon::parse($bill->period_start)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($bill->period_end)->format('d/m/Y') }}</td>
                        <td>{{ $bill->due_date ? \Carbon\Carbon::parse($bill->due_date)->format('d/m/Y') : '-' }}</td>
                        <td>Rp{{ \App\Helpers\Helpers::format_number($bill->amount) }}</td>
                        <td class="text-success">Rp{{ \App\Helpers\Helpers::format_number($bill->paid_amount) }}</td>
                        <td class="{{ $bill->remaining_amount > 0 ? 'text-danger' : 'text-success' }}">
                            Rp{{ \App\Helpers\Helpers::format_number($bill->remaining_amount) }}
                        </td>
                        <td>
                            @if($isPaid)
                            <span class="badge badge-success">Lunas</span>
                            @elseif((float) $bill->paid_amount > 0)
                            <span class="badge badge-warning">Terbayar Sebagian</span>
                            @else
                            <span class="badge badge-danger">Belum Lunas</span>
                            @endif
                        </td>
                        <td class="text-nowrap">
                            @if((float) $bill->paid_amount <= 0)
                                <a href="{{ route('student_bill.edit', $bill->id) }}" class="btn btn-sm btn-outline-primary">Ubah</a>
                                <form method="POST" action="{{ route('student_bill.destroy', $bill->id) }}" class="d-inline" onsubmit="return confirm('Hapus tagihan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                                @else
                                <span class="text-muted">Ada pembayaran</span>
                                @endif
                        </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Belum ada tagihan untuk tahun ajaran ini.</td>
                        </tr>
                        @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
</div>
</div>
</div>
@endsection