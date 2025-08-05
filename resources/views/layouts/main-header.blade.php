<!-- main-header -->
<div class="main-header sticky side-header nav nav-item">
	<div class="container-fluid">
		<div class="main-header-left ">
			<div class="responsive-logo">
				<a href="{{ url('/' . $page = 'index') }}"><img src="{{URL::asset('assets/img/brand/logo.png')}}"
						class="logo-1" alt="logo"></a>
				<a href="{{ url('/' . $page = 'index') }}"><img src="{{URL::asset('assets/img/brand/logo-white.png')}}"
						class="dark-logo-1" alt="logo"></a>
				<a href="{{ url('/' . $page = 'index') }}"><img src="{{URL::asset('assets/img/brand/favicon.png')}}"
						class="logo-2" alt="logo"></a>
				<a href="{{ url('/' . $page = 'index') }}"><img src="{{URL::asset('assets/img/brand/favicon.png')}}"
						class="dark-logo-2" alt="logo"></a>
			</div>
			<div class="app-sidebar__toggle" data-toggle="sidebar">
				<a class="open-toggle" href="#"><i class="header-icon fe fe-align-left"></i></a>
				<a class="close-toggle" href="#"><i class="header-icons fe fe-x"></i></a>
			</div>
			<div class="main-header-center ml-3 d-sm-none d-md-none d-lg-block">
				<input class="form-control" placeholder="Search for anything..." type="search"> <button class="btn"><i
						class="fas fa-search d-none d-md-block"></i></button>
			</div>
		</div>
		<div class="main-header-right">
			<ul class="nav">
				<li class="">
					<div class="dropdown  nav-itemd-none d-md-flex">
						<a href="#" class="d-flex  nav-item nav-link pr-0 country-flag1" data-toggle="dropdown"
							aria-expanded="false">
							<span class="avatar country-Flag mr-0 align-self-center bg-transparent"><img
									src="{{URL::asset('assets/img/flags/us_flag.jpg')}}" alt="img"></span>
							<div class="my-auto">
								<strong class="mr-2 ml-2 my-auto">English</strong>
							</div>
						</a>
						<div class="dropdown-menu dropdown-menu-left dropdown-menu-arrow" x-placement="bottom-end">
							<a href="#" class="dropdown-item d-flex ">
								<span class="avatar  mr-3 align-self-center bg-transparent"><img
										src="{{URL::asset('assets/img/flags/french_flag.jpg')}}" alt="img"></span>
								<div class="d-flex">
									<span class="mt-2">French</span>
								</div>
							</a>
							<a href="#" class="dropdown-item d-flex">
								<span class="avatar  mr-3 align-self-center bg-transparent"><img
										src="{{URL::asset('assets/img/flags/germany_flag.jpg')}}" alt="img"></span>
								<div class="d-flex">
									<span class="mt-2">Germany</span>
								</div>
							</a>
							<a href="#" class="dropdown-item d-flex">
								<span class="avatar mr-3 align-self-center bg-transparent"><img
										src="{{URL::asset('assets/img/flags/italy_flag.jpg')}}" alt="img"></span>
								<div class="d-flex">
									<span class="mt-2">Italy</span>
								</div>
							</a>
							<a href="#" class="dropdown-item d-flex">
								<span class="avatar mr-3 align-self-center bg-transparent"><img
										src="{{URL::asset('assets/img/flags/russia_flag.jpg')}}" alt="img"></span>
								<div class="d-flex">
									<span class="mt-2">Russia</span>
								</div>
							</a>
							<a href="#" class="dropdown-item d-flex">
								<span class="avatar  mr-3 align-self-center bg-transparent"><img
										src="{{URL::asset('assets/img/flags/spain_flag.jpg')}}" alt="img"></span>
								<div class="d-flex">
									<span class="mt-2">spain</span>
								</div>
							</a>
						</div>
					</div>
				</li>
			</ul>
			<div class="nav nav-item  navbar-nav-right ml-auto">
				<div class="nav-link" id="bs-example-navbar-collapse-1">
					<form class="navbar-form" role="search">
						<div class="input-group">
							<input type="text" class="form-control" placeholder="Search">
							<span class="input-group-btn">
								<button type="reset" class="btn btn-default">
									<i class="fas fa-times"></i>
								</button>
								<button type="submit" class="btn btn-default nav-link resp-btn">
									<svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" viewBox="0 0 24 24"
										fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
										stroke-linejoin="round" class="feather feather-search">
										<circle cx="11" cy="11" r="8"></circle>
										<line x1="21" y1="21" x2="16.65" y2="16.65"></line>
									</svg>
								</button>
							</span>
						</div>
					</form>
				</div>
				<div class="dropdown nav-item main-header-notification">
					<a class="new nav-link" href="#">
						<svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" viewBox="0 0 24 24" fill="none"
							stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
							class="feather feather-bell">
							<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
							<path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
						</svg>
						@if (count($notifications) > 0)
							<span class=" pulse"></span>
						@endif
					</a>
					<div class="dropdown-menu">
						<div class="menu-header-content bg-primary text-left">
							<div class="d-flex">
								<h6 class="dropdown-title mb-1 tx-15 text-white font-weight-semibold">Notifications</h6>
							</div>
							<p class="dropdown-title-text subtext mb-0 text-white op-6 pb-0 tx-12 ">You have <span class="badge badge-pill badge-danger">{{ count($notifications) }}</span> unread
								Notifications</p>
						</div>
						<div class="main-notification-list Notification-scroll">
							@if(count($notifications))
								@foreach($notifications as $notification)
									<a class="d-flex p-3 border-bottom" href="#">
										<div class="ml-3">
											@if (Auth::guard('admin')->check())
												<h5 class="notification-label mb-1">
													User {{ $notification->requester->name ?? 'System' }} has a request
												</h5>
											@elseif (Auth::guard('web')->check())
												<h5 class="notification-label mb-1">
													Admin has {{ $notification->status }}  your request
												</h5>
											@elseif (Auth::guard('azubi')->check())
												<h5 class="notification-label mb-1">
													Admin has {{ $notification->status }}  your request
												</h5>
											@endif

											<div class="notification-subtext">
												Status: {{ $notification->status }}
											</div>
											<div class="notification-subtext">
												Requested Status: {{ $notification->requested_status }}
											</div>
											<div class="notification-subtext">
												{{ \Carbon\Carbon::parse($notification->updated_at)->diffForHumans() }}
											</div>
										</div>
										<div class="ml-auto">
											<i class="las la-angle-right text-right text-muted"></i>
										</div>
									</a>
								@endforeach
							@else
								<div class="text-center p-3 text-muted">No notifications</div>
							@endif
						</div>
					<div class="dropdown-footer">
						<a href="">VIEW ALL</a>

					</div>
				</div>
			</div>
			<div class="nav-item full-screen fullscreen-button">
				<a class="new nav-link full-screen-link" href="#"><svg xmlns="http://www.w3.org/2000/svg"
						class="header-icon-svgs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
						stroke-linecap="round" stroke-linejoin="round" class="feather feather-maximize">
						<path
							d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3">
						</path>
					</svg></a>
			</div>
			<div class="dropdown main-profile-menu nav nav-item nav-link">
				<a class="profile-user d-flex" href=""><img alt="" src="{{URL::asset('assets/img/faces/6.jpg')}}"></a>
				<div class="dropdown-menu">
					<div class="main-header-profile bg-primary p-3">
						<div class="d-flex wd-100p">
							<div class="main-img-user"><img alt="" src="{{URL::asset('assets/img/faces/6.jpg')}}"
									class=""></div>
							<div class="ml-3 my-auto">
								<h6>Petey Cruiser</h6><span>Premium Member</span>
							</div>
						</div>
					</div>
					<a class="dropdown-item" href=""><i class="bx bx-user-circle"></i>Profile</a>
					<a class="dropdown-item" href=""><i class="bx bx-cog"></i> Edit Profile</a>
					<a class="dropdown-item" href=""><i class="bx bxs-inbox"></i>Inbox</a>
					<a class="dropdown-item" href=""><i class="bx bx-envelope"></i>Messages</a>
					<a class="dropdown-item" href=""><i class="bx bx-slider-alt"></i> Account Settings</a>
					@if (Auth::guard('admin')->check())
						@include('admin.dashboard.logout')
					@elseif(Auth::guard('web')->check())
						@include('mitarbeiter.dashboard.logout')
					@elseif(Auth::guard('azubi')->check())
						@include('azubi.dashboard.logout')
					@endif
				</div>
			</div>
			<div class="dropdown main-header-message right-toggle">
				<a class="nav-link pr-0" data-toggle="sidebar-right" data-target=".sidebar-right">
					<svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" viewBox="0 0 24 24" fill="none"
						stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
						class="feather feather-menu">
						<line x1="3" y1="12" x2="21" y2="12"></line>
						<line x1="3" y1="6" x2="21" y2="6"></line>
						<line x1="3" y1="18" x2="21" y2="18"></line>
					</svg>
				</a>
			</div>
		</div>
	</div>
</div>
</div>
<!-- /main-header -->