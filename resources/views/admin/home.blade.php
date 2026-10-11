@extends('admin.layout')
@section('content')

<!--  BEGIN CONTENT AREA  -->
<div id="content" class="main-content">
	<div class="layout-px-spacing">

		<div class="row layout-top-spacing">

			@if(in_array(Auth::user()->group->name, ['Admin Finance']))
			<div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
				<a href="#">
					<div class="widget widget-one_hybrid widget-followers" style="background:#8bc34a;">
						<div class="widget-heading">
							<div class="row" style="color: #ffffff;">
								<div class="col-md-8">
									<p class="w-value" style="color: #ffffff;font-size: 35px;">{{ \App\Helpers\Helpers::format_number($income) }}</p>
									<h6 style="color: #ffffff;">Penerimaan Bulan Ini</h6>
								</div>
								<div class="col-md-4">
									<center>
										<img src="{{ asset('storage/menu/icons8-sign-in-100.png') }}" width="80" height="80" style="margin-right: 20px">
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
			<div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
				<a href="#">
					<div class="widget widget-one_hybrid widget-followers" style="background: #e71717e0;">
						<div class="widget-heading">
							<div class="row" style="color: #ffffff;">
								<div class="col-md-8">
									<p class="w-value" style="color: #ffffff;font-size: 35px;">{{ \App\Helpers\Helpers::format_number($expense) }}</p>
									<h6 style="color: #ffffff;">Pengeluaran Bulan Ini</h6>
								</div>
								<div class="col-md-4">
									<center>
										<img src="{{ asset('storage/menu/icons8-login-rounded-up-100.png') }}" width="80" height="80" style="margin-right: 20px">
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
			@endif
            
		</div>
	</div>
	<div class="footer-wrapper">
		<div class="footer-section f-section-1">
		<p class="">Copyright © {{ date('Y') }}</p>
		</div>
	</div>
</div>
@endsection