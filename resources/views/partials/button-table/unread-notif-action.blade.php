<button id="read" data-id="{{ $id }}" data-original-title="read" class="read btn btn btn-primary">
    <i class="fa-regular fa-eye"></i>
    <span class="badge badge-success">Unread</span>
</button>

<script>
    $(document).ready(function() {
        $('.read').click(function(){
            var notificationButton = $(this);
            var notificationId = notificationButton.data('id');
            console.log(notificationId);
    
            $.ajax({
                type:'POST',
                url: '{{ route("notification.markAsRead", ["id"=>":id"]) }}'.replace(':id', notificationId),
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function () {
                    notificationButton.find('.badge').removeClass('badge-danger').addClass('badge-success');
                    notificationButton.prop('disabled', true);
                },
                error: function (error) {
                    console.log(error);
                }
            });
        });
    });
</script>