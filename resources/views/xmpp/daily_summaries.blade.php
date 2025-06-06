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
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title mg-b-0 w-100 mr-2">Logs table for {{ $userType }} : {{ $username }}</h4>
                            <div>
                                @if ($status == 'available')
                                    <span class="badge badge-success ">Online</span>
                                @else
                                    <span class="badge badge-danger ">Offline</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <i class="typcn typcn-pdf"></i>
                            <a href="{{ route('generate.daily.presence.pdf',[$userType,encrypt($userId)]) }}" class="">download as pdf</a>
                        </div>
                    </div>

                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table text-md-nowrap" id="example1">
                            <thead>
                                <tr>
                                    <th class="wd-15p border-bottom-0" style="width:10%;">Date</th>
                                    <th class="wd-15p border-bottom-0" style="width:10%;">Total working time</th>
                                    <th class="wd-20p border-bottom-0" style="width:10%;">Total Session</th>
                                    <th class="wd-20p border-bottom-0" style="width:10%;">Day Status</th>
                                    <th class="wd-20p border-bottom-0" style="width:15%;">Comment</th>
                                    <th class="wd-15p border-bottom-0" style="width:10%;">First Login</th>
                                    <th class="wd-10p border-bottom-0" style="width:10%;">Last Logout</th>
                                    <th class="wd-10p border-bottom-0" style="width:10%;">Overtime</th>
                                    <th class="wd-25p border-bottom-0" style="width:10%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($summaries as $summarie)

                                    <tr>
                                        <td>{{ $summarie->date->format('d') }}</td>
                                        <td>{{ roundToQuarter($summarie->first_login, $summarie->last_logout) }}</td>
                                        <td>{{ $summarie->session_count }}</td>
                                        <td>
                                            @if ($summarie->status)
                                                {{ $summarie->status->status }}
                                            @else
                                                Not available
                                            @endif
                                        </td>
                                        <td>
                                            @if ($summarie->status)
                                                {{ $summarie->status->notes }}
                                            @else
                                                Not Comment
                                            @endif
                                        </td>
                                        <td>{{ $summarie->first_login }}</td>
                                        <td>{{ $summarie->last_logout }}</td>
                                        <td>{{ $summarie->over_time }} </td>
                                        <td>
                                        <div class="dropdown">
													<button aria-expanded="false" aria-haspopup="true"
														class="btn ripple btn-outline-primary btn-sm" data-toggle="dropdown"
														type="button"> <i
															class="fas fa-caret-down mr-1"></i></button>
													<div class="dropdown-menu tx-13">
														<a class="dropdown-item" href="#" data-toggle="modal"
															data-target="#update_daystatus{{$summarie->id}}">Modify Day Status</a>

														<a class="dropdown-item" href="#" data-toggle="modal"
															data-target="#update_overtime{{$summarie->id}}">Modify Over Time</a>
													</div>
												</div>
                                        </td>
                                    </tr>


                                    @include('xmpp.update_daystatus')
                                    @include('xmpp.update_overtime')



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
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
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