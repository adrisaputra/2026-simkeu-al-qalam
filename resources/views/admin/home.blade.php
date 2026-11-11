@extends('admin.layout')
@section('content')

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/drilldown.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>


<!--  BEGIN CONTENT AREA  -->
<div id="content" class="main-content">
	<div class="layout-px-spacing">

		<div class="row layout-top-spacing">

			@if(in_array(Auth::user()->group->name, ['Admin Finance']))
			<div class="col-xl-3 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
				<a href="#">
					<div class="widget widget-one_hybrid widget-followers" style="background:#8bc34a;padding: 20px 20px 0px 20px;">
						<div class="widget-heading">
							<div class="row" style="color: #ffffff;">
								<div class="col-md-8">
									<p class="w-value" style="color: #ffffff;font-size: 28px;">{{ \App\Helpers\Helpers::format_number($income) }}</p>
									<h6 style="color: #ffffff;">Penerimaan</h6>
								</div>
								<div class="col-md-4">
									<center>
										<img src="{{ asset('storage/menu/icons8-sign-in-100.png') }}" width="60px" style="padding-top: 40px;">
									</center>
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
			<div class="col-xl-3 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
				<a href="#">
					<div class="widget widget-one_hybrid widget-followers" style="background: #e71717e0;padding: 20px 20px 0px 20px;">
						<div class="widget-heading">
							<div class="row" style="color: #ffffff;">
								<div class="col-md-8">
									<p class="w-value" style="color: #ffffff;font-size: 28px;">{{ \App\Helpers\Helpers::format_number($expense) }}</p>
									<h6 style="color: #ffffff;">Pengeluaran</h6>
								</div>
								<div class="col-md-4">
									<center>
										<img src="{{ asset('storage/menu/icons8-login-rounded-up-100.png') }}" width="60px" style="padding-top: 40px;">
									</center>
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
			<div class="col-xl-3 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
				<a href="#">
					<div class="widget widget-one_hybrid widget-followers" style="background: #2196f3;padding: 20px 20px 0px 20px;">
						<div class="widget-heading">
							<div class="row" style="color: #ffffff;">
								<div class="col-md-8">
									<p class="w-value" style="color: #ffffff;font-size: 28px;">{{ \App\Helpers\Helpers::format_number($cash_balance) }}</p>
									<h6 style="color: #ffffff;">Saldo Kas</h6>
								</div>
								<div class="col-md-4">
									<center>
										<img src="{{ asset('storage/menu/icons8-wallet-100.png') }}" width="60px" style="padding-top: 40px;">
									</center>
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
			<div class="col-xl-3 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
				<a href="#">
					<div class="widget widget-one_hybrid widget-followers" style="background: #ff9800;padding: 20px 20px 0px 20px;">
						<div class="widget-heading">
							<div class="row" style="color: #ffffff;">
								<div class="col-md-8">
									<p class="w-value" style="color: #ffffff;font-size: 28px;">{{ \App\Helpers\Helpers::format_number($total_aset) }}</p>
									<h6 style="color: #ffffff;">Total Aset</h6>
								</div>
								<div class="col-md-4">
									<center>
										<img src="{{ asset('storage/menu/icons8-asset-100.png') }}" width="60px" style="padding-top: 40px;">
									</center>
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
			<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
				<div id="containers"></div>
			</div>
			<div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
				<div id="income_by_category"></div>
			</div>
			<div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
				<div id="expense_by_category"></div>
			</div>
			@endif
            
		</div>
	</div>
	<div class="footer-wrapper">
		<div class="footer-section f-section-1">
		<p class="">Copyright © {{ date('Y') }}</p>
		</div>
	</div>
</div>

<script>
	Highcharts.chart('containers', {
    chart: {
        type: 'column'
    },

    title: {
        text: 'Grafik Pemasukan dan Pengeluaran Tahun {{ $year }}'
    },

    subtitle: {
        text: 'Perbandingan pemasukan dan pengeluaran setiap bulan'
    },

    xAxis: {
        categories: [
            'Januari', 'Februari', 'Maret',
            'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September',
            'Oktober', 'November', 'Desember'
        ],
        crosshair: true
    },

    yAxis: {
        min: 0,
        title: {
            text: 'Jumlah (Rp)'
        },
        labels: {
            formatter: function () {
                return 'Rp ' + Highcharts.numberFormat(this.value, 0, ',', '.');
            }
        }
    },

    tooltip: {
        shared: true,
        formatter: function () {
            let s = '<b>' + this.x + '</b>';

            this.points.forEach(function (point) {
                s += '<br/>' + point.series.name + ': <b>Rp ' +
                    Highcharts.numberFormat(point.y, 0, ',', '.') +
                    '</b>';
            });

            return s;
        }
    },

    exporting: {
        enabled: false
    },

    credits: {
        enabled: false
    },

    legend: {
        enabled: true
    },

    plotOptions: {
        column: {
            borderWidth: 0,
            dataLabels: {
                enabled: false
            },
            groupPadding: 0.15
        }
    },

    series: [
        {
            name: 'Pemasukan',
            color: '#4CAF50',
            data: @json($incomes)
        },
        {
            name: 'Pengeluaran',
            color: '#E57373',
            data: @json($expenses)
        }
    ]
});
</script>
<script>
	Highcharts.chart('income_by_category', {
    chart: {
        type: 'pie'
    },

    title: {
        text: 'Sumber Pemasukan Tahun {{ $year }}'
    },

    subtitle: {
        text: 'Total pemasukan berdasarkan kategori transaksi'
    },

    xAxis: {
        type: 'category',
        labels: {
            rotation: -35
        }
    },

    yAxis: {
        min: 0,
        title: {
            text: 'Jumlah Pemasukan (Rp)'
        },
        labels: {
            formatter: function () {
                return 'Rp ' + Highcharts.numberFormat(this.value, 0, ',', '.');
            }
        }
    },

    tooltip: {
        pointFormatter: function () {
            return '<br><b>Rp ' +
                Highcharts.numberFormat(this.y, 0, ',', '.') +
                '</b>';
        }
    },

    exporting: {
        enabled: false
    },

    credits: {
        enabled: false
    },

    // legend: {
    //     enabled: false
    // },

    legend: {
        enabled: true,
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'middle'
    },

    plotOptions: {
        series: {
            borderWidth: 0,
            color: '#4CAF50',
            dataLabels: {
                enabled: true,
                formatter: function () {
                    return Highcharts.numberFormat(this.y / 1000000, 1, ',', '.') + ' Jt';
                }
            }
        }
    },

    series: [{
        name: 'Pemasukan',
        data: @json($income_by_category)
    }]
});
</script>
<script>
Highcharts.chart('expense_by_category', {
    chart: {
        type: 'pie'
    },

    title: {
        text: 'Penggunaan Dana Tahun {{ $year }}'
    },

    subtitle: {
        text: 'Persentase pengeluaran berdasarkan kategori'
    },

    tooltip: {
        pointFormat: '<b>Rp {point.y:,.0f}</b> ({point.percentage:.1f}%)'
    },

    exporting: {
        enabled: false
    },

    credits: {
        enabled: false
    },

    legend: {
        enabled: true,
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'middle'
    },

    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            showInLegend: true,

            dataLabels: {
                enabled: true,
                format: '{point.percentage:.1f}%'
            }
        }
    },

    series: [{
        name: 'Pengeluaran',
        colorByPoint: true,
        data: @json($expense_by_category)
    }]
});
</script>
@endsection