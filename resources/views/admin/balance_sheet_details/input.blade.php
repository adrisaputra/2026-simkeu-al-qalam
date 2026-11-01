@extends('admin.layout')
@section('content')
<div id="content" class="main-content">
    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div class="col-lg-12 col-12 layout-spacing">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header">
                        <div class="row">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <h4>{{ __($title) }}</h4>
                            </div>
                        </div>
                    </div>

					   	<form method="GET" action="{{ route('balance_sheet_details.input') }}">
                            <div class="widget-content widget-content-area">
								<div class="row">
									<div class="col-xl-8 col-md-12 col-sm-12 col-12">
										</div>
                                    <div class="col-xl-4 col-md-12 col-sm-12 col-12">
										<select id="year" name="year" class="form-control form-control-sm" style="height: 38px;padding: 5px;">
                                            @for($i=2026;$i<=date('Y');$i++)
                                                <option value="{{ $i }}" @if(date('Y')==$i) selected @endif>{{ $i }}</option>
                                            @endfor
                                        </select>
                                        <button type="submit" class="btn btn-info btn-sm">Tampilkan Akun</button>
                                    </div>
								</div>
							</div>
						</form>

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if($balance_sheet_accounts->isEmpty())
                            <div class="alert alert-warning">Belum ada akun neraca aktif. Tambahkan akun terlebih dahulu di Master Akun Neraca.</div>
                        @else
                            <form method="POST" action="{{ route('balance_sheet_details.store_all') }}">
                                @csrf
                                <input type="hidden" name="year" value="{{ $year }}">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%">No</th>
                                                <th>Nama Akun Neraca</th>
                                                <th style="width: 30%">Nominal Saldo (Rp)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($balance_sheet_accounts as $index => $account)
                                                @php
                                                    $existingDetail = $account->balance_sheet_details->first();
                                                    $amount = old('amounts.'.$account->id, $existingDetail?->amount ?? '0.00');
                                                @endphp
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $account->name }}</td>
                                                    <td>
                                                        <input type="number" class="form-control form-control-sm @error('amounts.'.$account->id) is-invalid @enderror"
                                                            name="amounts[{{ $account->id }}]" value="{{ $amount }}" step="0.01" required>
                                                        @error('amounts.'.$account->id)
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <button type="submit" class="btn btn-primary">Simpan Semua Saldo</button>
                                <a href="{{ route('balance_sheet_details.index') }}" class="btn btn-secondary">Kembali</a>
                            </form>
                        @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
