document.addEventListener('DOMContentLoaded', function() {
    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            // Store in sessionStorage to use later in forms
            sessionStorage.setItem('user_lat', lat);
            sessionStorage.setItem('user_lng', lng);

            // If user is logged in, we might want to update their location in DB
            // We can check if a certain element exists or just try to send it
            fetch('../public/update_location.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `lat=${lat}&lng=${lng}`
            });

            // Update any hidden fields in forms
            document.querySelectorAll('input[name="latitude"]').forEach(el => el.value = lat);
            document.querySelectorAll('input[name="longitude"]').forEach(el => el.value = lng);
        });
    }
});
