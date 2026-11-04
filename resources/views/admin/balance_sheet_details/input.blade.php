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
									<div class="col-xl-10 col-md-12 col-sm-12 col-12">
										</div>
                                    <div class="col-xl-2 col-md-12 col-sm-12 col-12">
										<select id="year" name="year" class="form-control form-control-sm" style="height: 38px;padding: 5px;" onchange="this.form.submit()">
                                            @for($i=2024;$i<=date('Y');$i++)
                                                <option value="{{ $i }}" @if($year==$i) selected @endif>{{ $i }}</option>
                                            @endfor
                                        </select>
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
                                                <th>Kategori</th>
                                                <th style="width: 30%">Nominal Saldo (Rp)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($balance_sheet_accounts as $index => $account)
                                                @php
                                                    $existingDetail = $account->balance_sheet_details->first();
                                                    $amount = old('amounts.'.$account->id, $existingDetail?->amount ?? '0');
                                                @endphp
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $account->name }}</td>
                                                    <td>                                   
                                                        @if($account->category=='asset')
                                                            <span class="badge badge-primary">Aset</span>
                                                        @elseif($account->category=='liability')
                                                            <span class="badge badge-warning">Kewajiban</span>
                                                        @elseif($account->category=='net_asset')
                                                            <span class="badge badge-success">Modal</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm @error('amounts.'.$account->id) is-invalid @enderror"
                                                            name="amounts[{{ $account->id }}]" value="{{ $amount }}" onkeyup="formatRupiah(this, '.')" required>
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
                                <a href="{{ route('balance_sheet_details.index') }}"  class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Kembali"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left-circle"><circle cx="12" cy="12" r="10"></circle><polyline points="12 8 8 12 12 16"></polyline><line x1="16" y1="12" x2="8" y2="12"></line></svg></a>
                            </form>
                        @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
