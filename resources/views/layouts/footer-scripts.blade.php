<?php

use Illuminate\Support\Facades\Auth;

?>

<!-- Back-to-top -->
<a href="#top" id="back-to-top"><i class="las la-angle-double-up"></i></a>
<!-- JQuery min js -->
<script src="{{URL::asset('assets/plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap Bundle js -->
<script src="{{URL::asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- Ionicons js -->
<script src="{{URL::asset('assets/plugins/ionicons/ionicons.js')}}"></script>
<!-- Moment js -->
<script src="{{URL::asset('assets/plugins/moment/moment.js')}}"></script>

<!-- Rating js-->
<script src="{{URL::asset('assets/plugins/rating/jquery.rating-stars.js')}}"></script>
<script src="{{URL::asset('assets/plugins/rating/jquery.barrating.js')}}"></script>

<!--Internal  Perfect-scrollbar js -->
<script src="{{URL::asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/perfect-scrollbar/p-scroll.js')}}"></script>
<!--Internal Sparkline js -->
<script src="{{URL::asset('assets/plugins/jquery-sparkline/jquery.sparkline.min.js')}}"></script>
<!-- Custom Scroll bar Js-->
<script src="{{URL::asset('assets/plugins/mscrollbar/jquery.mCustomScrollbar.concat.min.js')}}"></script>
<!-- right-sidebar js -->
<script src="{{URL::asset('assets/plugins/sidebar/sidebar.js')}}"></script>
<script src="{{URL::asset('assets/plugins/sidebar/sidebar-custom.js')}}"></script>
<!-- Eva-icons js -->
<script src="{{URL::asset('assets/js/eva-icons.min.js')}}"></script>
@yield('js')
<!-- Sticky js -->
<script src="{{URL::asset('assets/js/sticky.js')}}"></script>
<!-- custom js -->
<script src="{{URL::asset('assets/js/custom.js')}}"></script><!-- Left-menu js-->
<script src="{{URL::asset('assets/plugins/side-menu/sidemenu.js')}}"></script>

@if(Auth::check())
<script>

    @php
        
        // Check if the user is logged in and the XMPP connection is established
        if (Auth::guard('admin')->check()) {
            $user = 'admin';
        } elseif (Auth::guard('azubi')->check()) {
            $user = 'azubi';
        } elseif (Auth::guard('web')->check()) {
            $user = 'mitarbeiter';
        } else {
            $user = null; // User is not logged in
        }
    @endphp

    document.addEventListener('DOMContentLoaded', function () {
        let lastActivityTime = Date.now();

        ['mousemove', 'keydown', 'scroll', 'click'].forEach(function (eventType) {
            document.addEventListener(eventType, function () {
                lastActivityTime = Date.now();
            });
        });

        setInterval(function () {
            if (Date.now() - lastActivityTime > 120000) {
                console.log('User inactive, skipping heartbeat');
                return;
            }

            fetch('/{{ $user }}/xmpp-heartbeat', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        console.error('XMPP heartbeat failed:', response.status);
                        return response.json().then(err => console.error(err));
                    }
                    return response.json();
                })
                .then(data => console.debug('Heartbeat success'))
                .catch(error => console.error('XMPP heartbeat error:', error));
        }, 30000);

        window.addEventListener('beforeunload', function () {
            navigator.sendBeacon(
                '/{{ $user }}/xmpp-disconnect',
                new Blob(
                    [JSON.stringify({})],
                    { type: 'application/json' }
                )
            );
        });
    });
</script>
@endif