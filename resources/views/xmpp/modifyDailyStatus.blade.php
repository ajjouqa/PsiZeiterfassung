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
     @if (session('success') == 'Request processed')
        <div class="alert alert-success">
            Request Approved Successfully
        </div>
    @elseif (session('success') == 'Request rejected')
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body">
                    <div>
                        @php 
                            if($request->requester_type == 'azubi') {
                                $requester_type = 'azubi';
                            } elseif ($request->requester_type == 'mitarbeiter') {
                                $requester_type = 'mitarbeiter';
                            } else {
                                $requesterName = 'Unknown';
                            }

                        @endphp
                        <h6 class="card-title mb-1">Requester Name : {{ $request->{$requester_type}->name ?? 'Unknown' }}
                        </h6>
                    </div>
                    <hr>
                    <div class="w-50 mx-auto">
                        <div class="d-flex justify-content-between align-items-center mt-5 ">
                            <p>Requester Email :</p>
                            <p class="text-muted">
                                {{ $request->{$requester_type}->email ?? 'No email provided' }}
                            </p>
                        </div>  
                        <hr>
                        <div class="d-flex justify-content-between align-items-center mt-3 ">
                            <p>Requester Phone :</p>
                            <p class="text-muted">
                                {{ $request->{$requester_type}->phone ?? 'No email provided' }}
                            </p>
                        </div> 
                        <hr>
                        <div class="d-flex justify-content-between align-items-center mt-3 ">
                            <p>Requester Address :</p>
                            <p class="text-muted">
                                {{ $request->{$requester_type}->address ?? 'No email provided' }}
                            </p>
                        </div> 
                        <hr>
                        <div class="d-flex justify-content-between align-items-center mt-3 ">
                            <p>For date :</p>
                            <p class="text-muted">
                                {{ $request->date }}
                            </p>
                        </div> 
                        <hr>
                        <div class="d-flex justify-content-between align-items-center mt-3 ">
                            <p>Requester Status :</p>
                            <p class="text-muted">
                                {{ $request->{$requester_type}->xmppUserMapping->current_presence ?? 'No email provided' }}
                            </p>
                        </div> 
                        <hr>
                        <div class="d-flex justify-content-between align-items-center mt-3 ">
                            <p>Reason for Request :</p>
                            <p class="text-muted">
                                {{ $request->reason ?? 'No reason provided' }}
                            </p>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center mt-3 ">
                            <p>Requested Status :</p>
                            <p class="text-muted">
                                {{ $request->requested_status ?? 'No status provided' }}
                            </p>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center mt-3 ">
                            <p>Request Status :</p>
                            <p class="text-muted">
                                {{ $request->status ?? 'No status provided' }}
                            </p>
                        </div>
                    </div>

                </div>
                @if ($request->status == 'pending')
                    <div class="card-footer w-50 mx-auto">
                        <form action="{{ route('update.daily.status.request.update', $request->id) }}" method="POST">
                            @csrf
                            @method('POST')
                            <div class="form-group">
                                <label for="status">Response</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="approved" {{ $request->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ $request->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    <option value="pending" {{ $request->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                </select>
                            </div>
                            <input type="hidden" name="requester_type" value="{{ $request->requester_type }}">
                            <input type="hidden" name="requester_id" value="{{ $request->{$request->requester_type}->id ?? '' }}">
                            <input type="hidden" name="date" value="{{ $request->date }}">
                            <input type="hidden" name="requested_status" value="{{ $request->requested_status ?? '' }}">
                            <input type="hidden" name="xmpp_username" value="{{ $request->{$requester_type}->xmppUserMapping->xmpp_username ?? '' }}">
                            <input type="hidden" name="reason" value="{{ $request->reason ?? '' }}">
                            <input type="hidden" name="id" value="{{ $request->id }}">
                            <button type="submit" class="btn btn-primary waves-effect waves-light mx-auto d-block">Send</button>
                        </form>
                    </div>
                @endif
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