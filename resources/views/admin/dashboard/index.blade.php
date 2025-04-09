@extends('layouts.master')
@section('css')
	<!--  Owl-carousel css-->
	<link href="{{URL::asset('assets/plugins/owl-carousel/owl.carousel.css')}}" rel="stylesheet" />
	<!-- Maps css -->
	<link href="{{URL::asset('assets/plugins/jqvmap/jqvmap.min.css')}}" rel="stylesheet">
@endsection
@section('page-header')
	<!-- breadcrumb -->
	<div class="breadcrumb-header justify-content-between">
	</div>
	<!-- /breadcrumb -->
@endsection
@section('content')
	<!-- row -->
	<div class="row row-sm">
		<div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
			<div class="card">
				<div class="card-body iconfont text-left">
					<div class="d-flex justify-content-between">
						<h4 class="card-title mb-3">Online Users</h4>
						<i class="mdi mdi-dots-vertical"></i>
					</div>
					<div class="d-flex mb-0">
						<div class="">
							<h4 class="mb-1 font-weight-bold">{{ $onlineUsers }}</h4>
							<p class="mb-2 tx-12 text-muted">Users</p>
						</div>
						<div class="card-chart bg-primary-transparent brround ml-auto mt-0">
							<i class="typcn typcn-group-outline text-primary tx-24"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
			<div class="card">
				<div class="card-body iconfont text-left">
					<div class="d-flex justify-content-between">
						<h4 class="card-title mb-3">Total Users</h4>
						<i class="mdi mdi-dots-vertical"></i>
					</div>
					<div class="d-flex mb-0">
						<div class="">
							<h4 class="mb-1 font-weight-bold">{{ $totalUsers }}</h4>
							<p class="mb-2 tx-12 text-muted">User</p>
						</div>
						
						<div class="card-chart bg-teal-transparent brround ml-auto mt-0">
							<i class="typcn typcn-chart-bar-outline text-teal tx-20"></i>

						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
			<div class="card">
				<div class="card-body iconfont text-left">
					<div class="d-flex justify-content-between">
						<h4 class="card-title mb-3">Total Azubis</h4>
						<i class="mdi mdi-dots-vertical"></i>
					</div>
					<div class="d-flex mb-0">
						<div class="">
							<h4 class="mb-1   font-weight-bold">{{ $azubis }}</span>
							</h4>
							<p class="mb-2 tx-12 text-muted">Azubi</p>
						</div>
						<div class="card-chart bg-teal-transparent brround ml-auto mt-0">
							<i class="typcn typcn-chart-bar-outline text-teal tx-20"></i>
						</div>
					</div>

				</div>
			</div>
		</div>
		<div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
			<div class="card">
				<div class="card-body iconfont text-left">
					<div class="d-flex justify-content-between">
						<h4 class="card-title mb-3">Total Mitarbeiters</h4>
						<i class="mdi mdi-dots-vertical"></i>
					</div>
					<div class="d-flex mb-0">
						<div class="">
							<h4 class="mb-1 font-weight-bold">{{ $mitarbeiters }}</span>
							</h4>
							<p class="mb-2 tx-12 text-muted">Mitarbeiter</p>
						</div>
						<div class="card-chart bg-teal-transparent brround ml-auto mt-0">
							<i class="typcn typcn-chart-bar-outline text-teal tx-20"></i>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row row-sm">
		<div class="col-sm-12 col-md-4">
			<div class="card mg-b-md-20 overflow-hidden">
				<div class="card-body">
					<div class="">
						<canvas id="userPieChart"></canvas>
					</div>
				</div>
			</div>
		</div><!-- col-6 -->
		<div class="col-sm-12 col-md-8">
			<div class="card">
				<div class="card-header pb-0">
					<div class="d-flex justify-content-between">
						<h4 class="card-title mg-b-0">LasT Logs</h4>
						<div>
							<a href="">Check All</a>

						</div>
					</div>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table mg-b-0 text-md-nowrap">
							<thead>
								<tr>
									<th>Name</th>
									<th>Xmpp user name</th>
									<th>Role</th>
									<th>Event type</th>
									<th>Event time</th>
									<th>Ip address</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($logs as $log)

									<tr>
										<td>{{ $log->xmppMapping->user->name}}</td>
										<td>{{ $log->xmppMapping->xmpp_username}}</td>
										<td>{{ $log->user_type}}</td>
										<td>{{ $log->event_type}}</td>
										<td>{{ $log->timestamp->diffForHumans()}}</td>
										<td>{{ $log->ip_address}}</td>

									</tr>

								@endforeach
							</tbody>
						</table>
					</div>
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



@section('logout')
	@include('admin.dashboard.logout')
@endsection










@section('js')
	<!-- Moment js -->
	<script src="{{URL::asset('assets/plugins/raphael/raphael.min.js')}}"></script>
	<!--Internal  Flot js-->
	<script src="{{URL::asset('assets/plugins/jquery.flot/jquery.flot.js')}}"></script>
	<script src="{{URL::asset('assets/plugins/jquery.flot/jquery.flot.pie.js')}}"></script>
	<script src="{{URL::asset('assets/plugins/jquery.flot/jquery.flot.resize.js')}}"></script>
	<script src="{{URL::asset('assets/plugins/jquery.flot/jquery.flot.categories.js')}}"></script>
	<script src="{{URL::asset('assets/js/dashboard.sampledata.js')}}"></script>

	<!-- Internal Map -->
	<script src="{{URL::asset('assets/plugins/jqvmap/jquery.vmap.min.js')}}"></script>
	<script src="{{URL::asset('assets/plugins/jqvmap/maps/jquery.vmap.usa.js')}}"></script>
	<script src="{{URL::asset('assets/js/modal-popup.js')}}"></script>
	<!--Internal  index js -->
	<script src="{{URL::asset('assets/js/index.js')}}"></script>
	<script src="{{URL::asset('assets/js/jquery.vmap.sampledata.js')}}"></script>

	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			const ctx = document.getElementById('userPieChart').getContext('2d');

			const chart = new Chart(ctx, {
				type: 'doughnut',
				data: {
					labels: ['Admins', 'Azubis', 'Mitarbeiter'],
					datasets: [{
						label: 'User Types',
						data: [
										{{ $admins }},
										{{ $azubis }},
							{{ $mitarbeiters }}
						],
						backgroundColor: [
							'rgba(255, 99, 132, 0.6)',
							'rgba(54, 162, 235, 0.6)',
							'rgba(75, 192, 192, 0.6)'
						],
						borderColor: [
							'rgba(255, 99, 132, 1)',
							'rgba(54, 162, 235, 1)',
							'rgba(75, 192, 192, 1)'
						],
						borderWidth: 1
					}]
				},
				options: {
					responsive: true,
					plugins: {
						legend: {
							position: 'bottom'
						},
						title: {
							display: true,
							text: 'Verteilung der Benutzer nach Typ'
						}
					}
				}
			});
		});
	</script>
@endsection