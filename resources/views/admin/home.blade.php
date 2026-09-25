@extends('admin.layout')
@section('content')

<!--  BEGIN CONTENT AREA  -->
<div id="content" class="main-content">
	<div class="layout-px-spacing">

		<div class="row layout-top-spacing">

			@if(in_array(Auth::user()->group->name, ['Admin Simkeu']))
			<div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
				<a href="#">
					<div class="widget widget-one_hybrid widget-followers" style="background:rgba(231, 23, 23, 0.88);">
						<div class="widget-heading">
							<div class="row" style="color: #ffffff;">
								<div class="col-md-8">
									<p class="w-value" style="color: #ffffff;font-size: 35px;">{{ \App\Helpers\Helpers::format_number($employee) }}</p>
									<h6 style="color: #ffffff;">Jumlah Pegawai</h6>
								</div>
								<div class="col-md-4">
									<center>
										<img src="{{ asset('storage/menu/icons8-employee-100.png') }}" width="80" height="80" style="margin-right: 20px">
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
			<div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
				<a href="#">
					<div class="widget widget-one_hybrid widget-followers" style="background: #03A9F4;">
						<div class="widget-heading">
							<div class="row" style="color: #ffffff;">
								<div class="col-md-8">
									<p class="w-value" style="color: #ffffff;font-size: 35px;">{{ \App\Helpers\Helpers::format_number($employee_l) }}</p>
									<h6 style="color: #ffffff;">Pegawai Laki-laki</h6>
								</div>
								<div class="col-md-4">
									<center>
										<img src="{{ asset('storage/menu/icons8-customer-100.png') }}" width="80" height="80" style="margin-right: 20px">
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
			<div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
				<a href="#">
					<div class="widget widget-one_hybrid widget-followers" style="background:rgb(244, 54, 133);">
						<div class="widget-heading">
							<div class="row" style="color: #ffffff;">
								<div class="col-md-8">
									<p class="w-value" style="color: #ffffff;font-size: 35px;">{{ \App\Helpers\Helpers::format_number($employee_p) }}</p>
									<h6 style="color: #ffffff;">Pegawai Perempuan</h6>
								</div>
								<div class="col-md-4">
									<center>
										<img src="{{ asset('storage/menu/icons8-customer-100.png') }}" width="80" height="80" style="margin-right: 20px">
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
			@endif
            
		</div>
	</div>
	<div class="footer-wrapper">
		<div class="footer-section f-section-1">
		<p class="">Copyright © 2026</p>
		</div>
	</div>
</div>
@endsection