<li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#">
        <span class="badge badge-danger navbar-badge" id="critical-notification-count">{{ auth()->user()->unreadNotifications->where('data.type', 'critical')->count() }}</span>
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" id="critical-notification-list">
        <span class="dropdown-header" id="critical-count-notification">{{ auth()->user()->unreadNotifications->where('data.type', 'warning')->count() }} Notifications</span>
        <div class="dropdown-divider"></div>
        @foreach(auth()->user()->unreadNotifications->where('data.type', 'critical')->take(5) as $notification)
        <a href="#" class="dropdown-item">
            <i class="fas fa-exclamation-triangle mr-2 text-danger"></i>
            {{ Illuminate\Support\Str::limit($notification->data['name'], 19) }}
            <span class="float-right text-muted text-sm">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</span>
        </a>
        <div class="dropdown-divider"></div>
        @endforeach
        <a href="#" class="dropdown-item dropdown-footer">See All Products</a>
    </div>
</li>
    

<li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="far fa-bell"></i>
        <span class="badge badge-warning navbar-badge" id="warning-notification-count">{{ auth()->user()->unreadNotifications->where('data.type', 'warning')->count() }}</span>
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" id="warning-notification-list">
        <span class="dropdown-header" id="warning-count-notification">{{ auth()->user()->unreadNotifications->where('data.type', 'warning')->count() }} Notifications</span>
        <div class="dropdown-divider"></div>
            @foreach(auth()->user()->unreadNotifications->where('data.type', 'warning') as $notification)
                <a href="#" class="dropdown-item">
                    <i class="fas fa-exclamation-triangle mr-2 text-warning"></i>
                    {{ Illuminate\Support\Str::limit($notification->data['name'], 19) }}
                    <span class="float-right text-muted text-sm">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</span>
                </a>
                <div class="dropdown-divider"></div>
            @endforeach

        <a href="{{ route('notification.index') }}" class="dropdown-item dropdown-footer">See All Notifications</a>
    </div>
</li>
<script>
    var pusher = new Pusher('50e5010495e7225dbb70', {
        cluster: 'ap1'
    });
    
    var productNotif = pusher.subscribe('public.product.notification.1')
    .bind("product.notification", (data) => {
        console.log('Data Message : ', data.message);
        console.log('Data Type : ', data.type);
        if (data.type === 'warning' || data.type === 'critical') {
            handleNotification(data.data, data.type);
        }
    });

    function handleNotification(data, type) {
                
                // Update the notification count
                const countElement = document.getElementById(`${type}-notification-count`);
                countElement.textContent = parseInt(countElement.textContent) + 1;
                
                // Add the new notification above the "See All Products" link
                const listElement = document.getElementById(`${type}-notification-list`);
                const seeAllProductsLink = listElement.querySelector('.dropdown-footer'); // Reference to "See All Products" link
                const notificationItem = document.createElement('div');
                const iconClass = type === 'warning' ? 'fas fa-exclamation-triangle text-warning' : 'fas fa-exclamation-circle text-danger';
                
                // Format the timestamp using moment.js
                const formattedTime = moment(data.created_at).fromNow(); // Adjust the format as per your preference
                
                notificationItem.innerHTML = `
                <a href="#" class="dropdown-item">
                    <i class="${iconClass} mr-2"></i>
                    ${data.name}
                    <span class="float-right text-muted text-sm">${formattedTime}</span>
                </a>
                <div class="dropdown-divider"></div>
                `;
                // Insert the new notification before the "See All Products" link
                listElement.insertBefore(notificationItem, seeAllProductsLink);
            }
</script>