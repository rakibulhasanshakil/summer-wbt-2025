document.addEventListener('DOMContentLoaded', function() {
    const filterRooms = () => {
        const roomType = document.getElementById('roomType').value.toLowerCase();
        const priceRange = document.getElementById('priceRange').value;
        const availability = document.getElementById('availability').value;

        // Get all room cards
        const rooms = document.querySelectorAll('.room-card');

        rooms.forEach(room => {
            let showRoom = true;

            // Filter by room type
            if (roomType && room.dataset.type.toLowerCase() !== roomType) {
                showRoom = false;
            }

            // Filter by price range
            if (priceRange) {
                const price = parseFloat(room.dataset.price);
                const [min, max] = priceRange.split('-').map(num => num.replace('+', ''));
                
                if (max) {
                    if (price < parseFloat(min) || price > parseFloat(max)) {
                        showRoom = false;
                    }
                } else {
                    if (price < parseFloat(min)) {
                        showRoom = false;
                    }
                }
            }

            // Filter by availability
            if (availability) {
                const status = room.querySelector('.room-tag').classList.contains('available') ? 'available' : 'booked';
                if (status !== availability) {
                    showRoom = false;
                }
            }

            // Show/hide room
            room.style.display = showRoom ? 'block' : 'none';
        });

        // Show "No rooms found" message if all rooms are hidden
        const visibleRooms = document.querySelectorAll('.room-card[style="display: block"]');
        const noRoomsMessage = document.querySelector('.no-rooms');
        
        if (visibleRooms.length === 0) {
            if (!noRoomsMessage) {
                const message = document.createElement('div');
                message.className = 'no-rooms';
                message.innerHTML = `
                    <i class="fas fa-bed"></i>
                    <h3>No Rooms Found</h3>
                    <p>Please try different filter criteria</p>
                `;
                document.querySelector('.room-grid').appendChild(message);
            }
        } else if (noRoomsMessage) {
            noRoomsMessage.remove();
        }
    };

    // Add event listeners to filters
    document.getElementById('roomType').addEventListener('change', filterRooms);
    document.getElementById('priceRange').addEventListener('change', filterRooms);
    document.getElementById('availability').addEventListener('change', filterRooms);
});