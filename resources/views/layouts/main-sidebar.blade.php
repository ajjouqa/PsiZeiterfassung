<!-- main-sidebar -->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar sidebar-scroll">
	<div class="main-sidebar-header active">
		<a class="desktop-logo logo-light active" href="#"><img src="{{URL::asset('assets/img/brand/favicon.png')}}"
				class="main-logo" alt="logo"></a>
		<a class="desktop-logo logo-dark active" href="#"><img src="{{URL::asset('assets/img/brand/favicon.png')}}"
				class="main-logo dark-theme" alt="logo"></a>
		<a class="logo-icon mobile-logo icon-light active" href="#"><img
				src="{{URL::asset('assets/img/brand/favicon.png')}}" class="logo-icon" alt="logo"></a>
		<a class="logo-icon mobile-logo icon-dark active" href="#"><img
				src="{{URL::asset('assets/img/brand/favicon.png')}}" class="logo-icon dark-theme" alt="logo"></a>
	</div>

	<div class="main-sidemenu">

		<div class="app-sidebar__user clearfix">
			<div class="dropdown user-pro-body">
				<div class="">
					<img alt="user-img" class="avatar avatar-xl brround"
						src="{{URL::asset('assets/img/faces/6.jpg')}}"><span
						class="avatar-status profile-status bg-green"></span>
				</div>
				<div class="user-info">
					<h4 class="font-weight-semibold mt-3 mb-0">{{ Auth::user()->name }}</h4>
					<span class="mb-0 text-muted">{{ Auth::user()->email }}</span>
				</div>
			</div>
		</div>
		<!-----------------------------------------------Start menu---------------------------------------------------------->

		<!-----------------------------------------------Admin---------------------------------------------------------->

		@if (Auth::guard('admin')->check())
					@php
						$activeRoutes = ['admin.admin', 'admin.mitarbeiter', 'admin.azubi', 'admin.create.user', 'xmpp.presence.logs', 'xmpp.presence.daily'];
					@endphp
					<ul class="side-menu">
						<li class="slide">
							<a class="side-menu__item" href="{{route('admin.dashboard')}}"><svg xmlns="http://www.w3.org/2000/svg"
									class="side-menu__icon" viewBox="0 0 24 24">
									<path d="M0 0h24v24H0V0z" fill="none" />
									<path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3" />
									<path
										d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z" />
								</svg><span class="side-menu__label">Index</span>
							</a>
						</li>
						<li class="slide {{ in_array(Route::currentRouteName(), $activeRoutes) ? 'active is-expanded' : '' }}">
							<a class="side-menu__item  {{ in_array(Route::currentRouteName(), $activeRoutes) ? 'active' : '' }}" data-toggle="slide" href="{{ url('/' . $page = '#') }}">
								<span class="side-menu__icon  mb-3"><i class="mdi mdi-account "></i></span><span
									class="side-menu__label">Users</span>
								<i class="angle fe fe-chevron-down"></i>
							</a>
							<ul class="slide-menu">
								<li><a class="slide-item" href="{{ route('admin.admin') }}">Admins</a></li>
								<li><a class="slide-item" href="{{ route('admin.mitarbeiter') }}">Mitarbeiters</a></li>
								<li><a class="slide-item" href="{{ route('admin.azubi') }}">Azubis</a></li>
								<li><a class="slide-item" href="{{ route('admin.create.user') }}">Add User</a></li>
							</ul>
						</li>
					</ul>

					<!-----------------------------------------------Azubi---------------------------------------------------------->

		@elseif (Auth::guard('azubi')->check())
			<ul class="side-menu">
				<li class="slide">
					<a class="side-menu__item" href="{{ route('azubi.dashboard') }}"><svg xmlns="http://www.w3.org/2000/svg"
							class="side-menu__icon" viewBox="0 0 24 24">
							<path d="M0 0h24v24H0V0z" fill="none" />
							<path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3" />
							<path
								d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z" />
						</svg><span class="side-menu__label">Index</span>
					</a>
				</li>
				<li class="slide">
					<a class="side-menu__item" href="{{ route('xmpp.presence.daily.azubi',['azubi',encrypt(auth()->user()->id)]) }}">
						<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
							<path d="M0 0h24v24H0V0z" fill="none" />
							<path
								d="M12 4c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm3.5 4c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5-1.5-.67-1.5-1.5.67-1.5 1.5-1.5zm-7 0c.83 0 1.5.67 1.5 1.5S9.33 11 8.5 11 7 10.33 7 9.5 7.67 8 8.5 8zm3.5 9.5c-2.33 0-4.32-1.45-5.12-3.5h1.67c.7 1.19 1.97 2 3.45 2s2.76-.81 3.45-2h1.67c-.8 2.05-2.79 3.5-5.12 3.5z"
								opacity=".3" />
							<circle cx="15.5" cy="9.5" r="1.5" />
							<circle cx="8.5" cy="9.5" r="1.5" />
							<path
								d="M12 16c-1.48 0-2.75-.81-3.45-2H6.88c.8 2.05 2.79 3.5 5.12 3.5s4.32-1.45 5.12-3.5h-1.67c-.69 1.19-1.97 2-3.45 2zm-.01-14C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z" />
						</svg><span class="side-menu__label">Daily presence</span>
					</a>
				</li>

			</ul>

			<!-----------------------------------------------Mitarbeiter---------------------------------------------------------->

		@elseif(Auth::guard(name: 'web')->check())
			<ul class="side-menu">
				<li class="slide">
					<a class="side-menu__item" href="{{ route('azubi.dashboard') }}"><svg xmlns="http://www.w3.org/2000/svg"
							class="side-menu__icon" viewBox="0 0 24 24">
							<path d="M0 0h24v24H0V0z" fill="none" />
							<path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3" />
							<path
								d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z" />
						</svg><span class="side-menu__label">Index</span><span
							class="badge badge-success side-badge">1</span></a>
				</li>
				<li class="slide">
					<a class="side-menu__item" href="{{ url('/' . $page = 'icons') }}"><svg
							xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
							<path d="M0 0h24v24H0V0z" fill="none" />
							<path
								d="M12 4c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm3.5 4c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5-1.5-.67-1.5-1.5.67-1.5 1.5-1.5zm-7 0c.83 0 1.5.67 1.5 1.5S9.33 11 8.5 11 7 10.33 7 9.5 7.67 8 8.5 8zm3.5 9.5c-2.33 0-4.32-1.45-5.12-3.5h1.67c.7 1.19 1.97 2 3.45 2s2.76-.81 3.45-2h1.67c-.8 2.05-2.79 3.5-5.12 3.5z"
								opacity=".3" />
							<circle cx="15.5" cy="9.5" r="1.5" />
							<circle cx="8.5" cy="9.5" r="1.5" />
							<path
								d="M12 16c-1.48 0-2.75-.81-3.45-2H6.88c.8 2.05 2.79 3.5 5.12 3.5s4.32-1.45 5.12-3.5h-1.67c-.69 1.19-1.97 2-3.45 2zm-.01-14C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z" />
						</svg><span class="side-menu__label">Icons</span><span
							class="badge badge-danger side-badge">New</span></a>
				</li>
				<li class="slide">
					<a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page = '#') }}"><svg
							xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
							<path d="M0 0h24v24H0V0z" fill="none" />
							<path d="M19 5H5v14h14V5zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z" opacity=".3" />
							<path
								d="M3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2zm2 0h14v14H5V5zm2 5h2v7H7zm4-3h2v10h-2zm4 6h2v4h-2z" />
						</svg><span class="side-menu__label">Charts</span><i class="angle fe fe-chevron-down"></i></a>
					<ul class="slide-menu">
						<li><a class="slide-item" href="{{ url('/' . $page = 'chart-morris') }}">Morris Charts</a></li>
						<li><a class="slide-item" href="{{ url('/' . $page = 'chart-flot') }}">Flot Charts</a></li>
						<li><a class="slide-item" href="{{ url('/' . $page = 'chart-chartjs') }}">ChartJS</a></li>
						<li><a class="slide-item" href="{{ url('/' . $page = 'chart-echart') }}">Echart</a></li>
						<li><a class="slide-item" href="{{ url('/' . $page = 'chart-sparkline') }}">Sparkline</a></li>
						<li><a class="slide-item" href="{{ url('/' . $page = 'chart-peity') }}">Chart-peity</a></li>
					</ul>
				</li>

			</ul>

		@endif
		<!-----------------------------------------------End Menu---------------------------------------------------------->
	</div>

</aside>
<!-- main-sidebar -->