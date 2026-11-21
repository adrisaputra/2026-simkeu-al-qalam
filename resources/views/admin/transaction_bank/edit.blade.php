@extends('admin.layout')

@section('content')
<div id="content" class="main-content">
    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div class="col-lg-8 col-12">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header"><h4>Ubah Tagihan Siswa</h4></div>
                    <div class="widget-content widget-content-area">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <p>
                            <strong>{{ $bill->student?->name }}</strong><br>
                            {{ $bill->student_bill_type?->transaction_code?->name ?? $bill->student_bill_type?->name ?? 'Jenis Tagihan' }}
                        </p>
                        <p class="text-muted">
                            Periode {{ \Carbon\Carbon::parse($bill->period_start)->format('d/m/Y') }}
                            - {{ \Carbon\Carbon::parse($bill->period_end)->format('d/m/Y') }}
                        </p>

                        <form method="POST" action="{{ route('student_bill.update', $bill->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="amount">Nominal (Rp)</label>
                                <input id="amount" name="amount" type="number" class="form-control" min="0.01" max="9999999999999.99" step="0.01" value="{{ old('amount', $bill->amount) }}" required>
                            </div>
                            <div class="form-group">
                                <label for="due_date">Jatuh Tempo</label>
                                <input id="due_date" name="due_date" type="date" class="form-control" value="{{ old('due_date', $bill->due_date) }}">
                            </div>
                            <div class="form-group">
                                <label for="notes">Catatan</label>
                                <textarea id="notes" name="notes" class="form-control" rows="3" maxlength="1000">{{ old('notes', $bill->notes) }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                            <a href="{{ route('student_bill.show', ['student' => $bill->student_id, 'academic_year_id' => $bill->academic_year_id]) }}" class="btn btn-light">Batal</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
