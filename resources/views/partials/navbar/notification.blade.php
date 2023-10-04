<li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="far fa-bell"></i>
        <span class="badge badge-warning navbar-badge" id="notification-count">{{ auth()->user()->unreadNotifications->count() }}</span>
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <span class="dropdown-header" id="notification-count">{{ auth()->user()->unreadNotifications->count() }} Notifications</span>
        <div class="dropdown-divider"></div>

        {{-- <div id="notification-list"> --}}
            @foreach(auth()->user()->unreadNotifications as $notification)
                <a href="#" class="dropdown-item">
                    @if($notification->data['type'] === 'warning')
                        <i class="fas fa-exclamation-triangle mr-2 text-warning"></i>
                    @elseif($notification->data['type'] === 'critical')
                        <i class="fas fa-exclamation-circle mr-2 text-danger"></i>
                    @endif
                    {{ $notification->data['message'] }}
                    <span class="float-right text-muted text-sm">{{ $notification->created_at->format('H:i') }}</span>
                </a>
                <div class="dropdown-divider"></div>
            @endforeach
        {{-- </div> --}}

        <a href="{{ route('notification.index') }}" class="dropdown-item dropdown-footer">See All Notifications</a>
    </div>
</li>



<script>
    $(document).ready(function() {
        var pusher = new Pusher('50e5010495e7225dbb70', {
            cluster: 'ap1'
        });
        
        
        var productNotif = pusher.subscribe('public.product.notification.1')
        .bind("product.notification", (data) => {
            console.log(data);
        });
    });
</script>

