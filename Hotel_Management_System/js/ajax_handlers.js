$(document).ready(function() {
    // Room availability checker
    $('#check-availability-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '/hotel_management_system/ajax/check_room_availability.php',
            method: 'POST',
            data: $(this).serialize(),
            beforeSend: function() {
                $('#availability-result').html('<div class="loading">Checking availability...</div>');
            },
            success: function(response) {
                $('#availability-result').html(response);
            },
            error: function() {
                $('#availability-result').html('<div class="error">Error checking availability. Please try again.</div>');
            }
        });
    });

    // Real-time room filter
    $('.room-filter input, .room-filter select').on('change', function() {
        let filterData = $('.room-filter').serialize();
        
        $.ajax({
            url: '/hotel_management_system/ajax/filter_rooms.php',
            method: 'POST',
            data: filterData,
            beforeSend: function() {
                $('.room-grid').addClass('loading');
            },
            success: function(response) {
                $('.room-grid').html(response);
            },
            complete: function() {
                $('.room-grid').removeClass('loading');
            }
        });
    });

    // Room booking process
    $('#booking-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '/hotel_management_system/ajax/process_booking.php',
            method: 'POST',
            data: $(this).serialize(),
            beforeSend: function() {
                $('#booking-status').html('<div class="loading">Processing your booking...</div>');
            },
            success: function(response) {
                let result = JSON.parse(response);
                if(result.success) {
                    $('#booking-status').html('<div class="success">' + result.message + '</div>');
                    setTimeout(function() {
                        window.location.href = '/hotel_management_system/booking_details.php?id=' + result.booking_id;
                    }, 2000);
                } else {
                    $('#booking-status').html('<div class="error">' + result.message + '</div>');
                }
            }
        });
    });
});