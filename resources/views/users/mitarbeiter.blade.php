@extends('layouts.master')
@section('css')
	<!-- Internal Data table css -->
	<link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" />
	<link href="{{URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css')}}" rel="stylesheet">
	<link href="{{URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css')}}" rel="stylesheet" />
	<link href="{{URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css')}}" rel="stylesheet">
	<link href="{{URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css')}}" rel="stylesheet">
	<link href="{{URL::asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
@endsection
@section('page-header')
	<!-- breadcrumb -->
	<div class="breadcrumb-header justify-content-between">
	</div>
	<!-- breadcrumb -->
@endsection
@section('content')
	<!-- row opened -->
	<div class="row row-sm">
		<div class="col-xl-12">
			<div class="card mg-b-20">
				<div class="card-header pb-0">
					<div class="d-flex justify-content-between">
						<h4 class="card-title mg-b-0">Table Admins</h4>
						<i class="mdi mdi-dots-horizontal text-gray"></i>
					</div>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table id="example" class="table key-buttons text-md-nowrap">
							<thead>
								<tr>
									<th class="border-bottom-0">Name</th>
									<th class="border-bottom-0">Phone</th>
									<th class="border-bottom-0 w-25">Adress</th>
									<th class="border-bottom-0">Current Status</th>
									<th class="border-bottom-0">Account Status</th>
									<th class="border-bottom-0">Created at</th>
									<th class="border-bottom-0">Action</th>
								</tr>
							</thead>
							<tbody>
								@if ($mitarbeiters->isEmpty())
									<tr>
										<td colspan="5" class="text-center">No admins found</td>
									</tr>
								@else
									@foreach ($mitarbeiters as $mitarbeiter)
										<tr>
											<td>{{ $mitarbeiter->name }}</td>
											<td>{{ $mitarbeiter->phone }}</td>
											<td>{{ $mitarbeiter->address }}</td>
											<td>
												@if ($mitarbeiter->xmppUserMapping->current_presence == 'available')
													<span class="badge badge-success">Online</span>
												@else
													<span class="badge badge-danger">Offline</span>
												@endif
											</td>
											<td>
												@if ($mitarbeiter->status == 'active')
													<span class="badge badge-success">Active</span>
												@else
													<span class="badge badge-danger">Inactive</span>
												@endif
											</td>
											<td>{{ $mitarbeiter->created_at }}</td>
											<td>
												<a href="{{ route('xmpp.presence.logs', ['mitarbeiter', encrypt($mitarbeiter->id)]) }}"
													class="btn btn-primary">view user logs</a>
												<a href="{{ route('xmpp.presence.daily', ['mitarbeiter', encrypt($mitarbeiter->id)]) }}"
													class="btn btn-primary">view daily summary</a>
												<form action="" method="POST" style="display:inline;">
													@csrf
													@method('DELETE')
													<button type="submit" class="btn btn-danger">Delete</button>
												</form>

											</td>
										</tr>
									@endforeach
								@endif
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<!--/div-->
	</div>
	<!-- /row -->
	</div>
	<!-- Container closed -->
	</div>
	<!-- main-content closed -->
@endsection

@section('logout')
	@include('admin.dashboard.logout')
@endsection


@section('js')
	<!-- Internal Data tables -->
	<script src="{{URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
	<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.dataTables.min.js')}}"></script>
	<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js')}}"></script>
	<script src="{{URL::asset('assets/plugins/datatable/js/responsive.dataTables.min.js')}}"></script>
	<script src="{{URL::asset('assets/plugins/datatable/js/jquery.dataTables.js')}}"></script>
	<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js')}}"></script>
	<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.buttons.min.js')}}"></script>
	<script src="{{URL::asset('assets/plugins/datatable/js/buttons.bootstrap4.min.js')}}"></script>
	<script src="{{URL::asset('assets/plugins/datatable/js/jszip.min.js')}}"></script>
	<script src="{{URL::asset('assets/plugins/datatable/js/pdfmake.min.js')}}"></script>
	<script src="{{URL::asset('assets/plugins/datatable/js/vfs_fonts.js')}}"></script>
	<script src="{{URL::asset('assets/plugins/datatable/js/buttons.html5.min.js')}}"></script>
	<script src="{{URL::asset('assets/plugins/datatable/js/buttons.print.min.js')}}"></script>
	<script src="{{URL::asset('assets/plugins/datatable/js/buttons.colVis.min.js')}}"></script>
	<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js')}}"></script>
	<script src="{{URL::asset('assets/plugins/datatable/js/responsive.bootstrap4.min.js')}}"></script>
	<!--Internal  Datatable js -->
	<script src="{{URL::asset('assets/js/table-data.js')}}"></script>
@endsection