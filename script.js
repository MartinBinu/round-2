 let map;

    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 12,
            center: { lat: 19.0760, lng: 72.8777 } // Default to Mumbai
        });
    }

    document.getElementById('searchForm').addEventListener('submit', function (event) {
        event.preventDefault();
        const location = document.getElementById('location').value;

        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(location)}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    let lat = parseFloat(data[0].lat);
                    let lng = parseFloat(data[0].lon);

                    let searchedLocation = { lat: lat, lng: lng };
                    map.setCenter(searchedLocation);
                    new google.maps.Marker({
                        position: searchedLocation,
                        map: map,
                        title: `Location: ${location}`
                    });
                } else {
                    alert("Location not found. Try a different search.");
                }
            })
            .catch(error => console.error("Error fetching location:", error));
    });
