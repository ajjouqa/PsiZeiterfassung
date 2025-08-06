@extends('layouts.master')
@section('css')
	<!--  Owl-carousel css-->
	<link href="{{URL::asset('assets/plugins/owl-carousel/owl.carousel.css')}}" rel="stylesheet" />
	<!-- Maps css -->
	<link href="{{URL::asset('assets/plugins/jqvmap/jqvmap.min.css')}}" rel="stylesheet">
@endsection
@section('page-header')
	<!-- breadcrumb -->
	<div class="breadcrumb-header justify-content-between"></div>
	<!-- /breadcrumb -->
@endsection
@section('content')
	<!-- row -->
	<div class="row row-sm main-content-mail">
		<div class="col-lg-10 col-xl-10 col-md-10 mx-auto">
			<div class="card">
				<div class="main-content-body main-content-body-mail card-body">
					<div class="main-mail-header">
						<div class="d-flex justify-content-between">
							<div>
								<h4 class="">Notifications</h4>
							</div>
							<div>
								<p>You have {{ count($requests) }} unread messages</p>

							</div>
						</div>
					</div><!-- main-mail-list-header -->
					<div class="main-mail-list">

						@foreach ($requests as $request)
							<a href="{{ route('update.daily.status.request',encrypt($request->id)) }}" class="text-dark">
								<div class="main-mail-item {{ $request->status == 'pending'  ? '' : 'unread' }}">
									<div
										class="main-img-user avatar-md {{ Arr::random(['bg-primary','bg-dark','bg-danger','bg-warning']) }} text-light d-flex align-items-center justify-content-center me-5">
										<span>
											@if ($request->requester_type == 'azubi')
												{{ strtoupper($request->azubi->name[0]) }}
											@elseif ($request->requester_type == 'mitarbeiter')
												{{ strtoupper($request->mitarbeiter->name[0]) }}
											@endif
										</span>
									</div>
									<div class="main-mail-body">
										<div class="main-mail-from">
											@if ($request->requester_type == 'azubi')
												{{ $request->azubi->name }}
											@elseif ($request->requester_type == 'mitarbeiter')
												{{ $request->mitarbeiter->name }}
											@endif
										</div>
										<div class="main-mail-subject">
											{{Str::limit($request->reason ?? 'No reason provided', 50) }}
										</div>
									</div>
									<div class="main-mail-date">
										{{ $request->created_at->diffForHumans() }}
									</div>
								</div>
							</a>
						@endforeach
					</div>
					<div class="mg-lg-b-30"></div>
				</div>
			</div>
		</div>
	</div>


	<!-- /row -->
	</div>
	<!-- /Container -->
	</div>
	<!-- /main-content -->
@endsection












@section('js')
	<!--Internal  Chart.bundle js -->
	<script src="{{URL::asset('assets/plugins/chart.js/Chart.bundle.min.js')}}"></script>
	<!-- Moment js -->
	<script src="{{URL::asset('assets/plugins/raphael/raphael.min.js')}}"></script>
	<!--Internal  Flot js-->
	<script src="{{URL::asset('assets/plugins/jquery.flot/jquery.flot.js')}}"></script>
	<script src="{{URL::asset('assets/plugins/jquery.flot/jquery.flot.pie.js')}}"></script>
	<script src="{{URL::asset('assets/plugins/jquery.flot/jquery.flot.resize.js')}}"></script>
	<script src="{{URL::asset('assets/plugins/jquery.flot/jquery.flot.categories.js')}}"></script>
	<script src="{{URL::asset('assets/js/dashboard.sampledata.js')}}"></script>
	<script src="{{URL::asset('assets/js/chart.flot.sampledata.js')}}"></script>
	<!--Internal Apexchart js-->
	<script src="{{URL::asset('assets/js/apexcharts.js')}}"></script>
	<!-- Internal Map -->
	<script src="{{URL::asset('assets/plugins/jqvmap/jquery.vmap.min.js')}}"></script>
	<script src="{{URL::asset('assets/plugins/jqvmap/maps/jquery.vmap.usa.js')}}"></script>
	<script src="{{URL::asset('assets/js/modal-popup.js')}}"></script>
	<!--Internal  index js -->
	<script src="{{URL::asset('assets/js/index.js')}}"></script>
	<script src="{{URL::asset('assets/js/jquery.vmap.sampledata.js')}}"></script>
@endsection