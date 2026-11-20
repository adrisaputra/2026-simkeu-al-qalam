@extends('admin.layout')
@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

        <!--  BEGIN CONTENT AREA  -->
        <div id="content" class="main-content">
            <div class="layout-px-spacing">

                <div class="row layout-top-spacing">
                    <div id="tableHover" class="col-lg-12 col-12 layout-spacing">
                        <div class="statbox widget box box-shadow">
                            <div class="widget-header">
                                <div class="row">
                                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
							 		<h4>Data {{ __($title) }}</h4>
                                    </div>                 
                                </div>
                            </div>

							<div class="widget-content widget-content-area">
								<div class="row">
									<div class="col-xl-8 col-md-12 col-sm-12 col-12">
										<a href="{{ url(Request::segment(1)) }}" class="btn mb-2 mr-1 btn-warning" data-toggle="tooltip" data-placement="top" title="Refresh"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-ccw"><polyline points="1 4 1 10 7 10"></polyline><polyline points="23 20 23 14 17 14"></polyline><path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"></path></svg></a>
									</div>
								</div>
							</div>
						
                            <div class="widget-content widget-content-area" style="padding-top: 0px;">
                                <div class="row layout-top-spacing">

                                        <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                                            <a href="#">
                                                <div class="widget widget-one_hybrid widget-followers" style="background:#2196f3;padding: 20px 20px 0px 20px;">
                                                    <div class="widget-heading">
                                                        <div class="row" style="color: #ffffff;">
                                                            <div class="col-md-12">
                                                                <p class="w-value" style="color: #ffffff;font-size: 28px;">{{ $income }}</p>
                                                                <h6 style="color: #ffffff;">Total Pemasukan {{ $transaction_code->name}}</h6>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="widget-content">    
                                                        <div class="w-chart">
                                                            <div id="hybrid_followers"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                                            <a href="#">
                                                <div class="widget widget-one_hybrid widget-followers" style="background: #e71717e0;padding: 20px 20px 0px 20px;">
                                                    <div class="widget-heading">
                                                        <div class="row" style="color: #ffffff;">
                                                            <div class="col-md-12">
                                                                <p class="w-value" style="color: #ffffff;font-size: 28px;">{{ $used }}</p>
                                                                <h6 style="color: #ffffff;">Penggunaan Dana</h6>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="widget-content">    
                                                        <div class="w-chart">
                                                            <div id="hybrid_followers"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                                            <a href="#">
                                                <div class="widget widget-one_hybrid widget-followers" style="background: #8bc34a;padding: 20px 20px 0px 20px;">
                                                    <div class="widget-heading">
                                                        <div class="row" style="color: #ffffff;">
                                                            <div class="col-md-12">
                                                                <p class="w-value" style="color: #ffffff;font-size: 28px;">{{ $remaining }}</p>
                                                                <h6 style="color: #ffffff;">Sisa dana</h6>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="widget-content">    
                                                        <div class="w-chart">
                                                            <div id="hybrid_followers"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                                            <a href="#">
                                                <div class="widget widget-one_hybrid widget-followers" style="background: #ff9800;padding: 20px 20px 0px 20px;">
                                                    <div class="widget-heading">
                                                        <div class="row" style="color: #ffffff;">
                                                            <div class="col-md-12">
                                                                <p class="w-value" style="color: #ffffff;font-size: 28px;">{{ $percentage }}</p>
                                                                <h6 style="color: #ffffff;">Persentase penggunaan</h6>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="widget-content">    
                                                        <div class="w-chart">
                                                            <div id="hybrid_followers"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        
                                    </div>
							<div class="table-responsive">
								<table class="table table-bordered table-hover mb-12" id="transaction_allocation-table">
									<thead>
										<tr>
											<th style="width: 2%">Number</th>
											<th style="width: 2%">No</th>
											<th>Tanggal</th>
											<th>Keterangan</th>
											<th>Nominal</th>
										</tr>
									</thead>
								</table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    var table;

    $(document).ready(function () {
        table = $('#transaction_allocation-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('transaction_allocation_detail.list', ['transaction_code' => Crypt::encrypt($transaction_code->id)]) }}",
            columns: [
				{data: 'id', name: 'id', visible: false},
				{data: 'number', name: 'number'}, // Kolom nomor urut
                {data: 'display_date', name: 'date'},
                {data: 'display_desc', name: 'desc'},
                {data: 'display_amount', name: 'amount'},
            ],
			order: [
				[0, 'asc'] // Mengatur pengurutan kolom pertama (id) secara descending
			],
            paging: false,
			drawCallback: function () {
                var api = this.api();
                var startIndex = api.context[0]._iDisplayStart; // Indeks baris pertama di halaman
                api.column(1, {page: 'current'}).nodes().each(function (cell, i) {
                    cell.innerHTML = startIndex + i + 1; // Menghitung nomor urut berdasarkan indeks baris dan nomor halaman
                });
            }
        });

    });

</script>
@endsection