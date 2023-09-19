var userMarker;
var map = L.map("map", {
  center: [38.246318, 21.735255],
  zoom: 15,
});

var osm = L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
  attribution:
    '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
});
osm.addTo(map);

var userIcon = L.icon({
  iconUrl: "/map/icons8-circle-48.png",
  iconAnchor: [25, 20],
});

// Listen for the "dragend" event on the marker
function updatePopup() {
  var newLat = userMarker.getLatLng().lat;
  var newLng = userMarker.getLatLng().lng;

  // Update the popup content with the new coordinates
  var popupContent = "Latitude: " + newLat + "<br>Longitude: " + newLng;
  userMarker.getPopup().setContent(popupContent);

  // Send an AJAX POST request to store the new coordinates in MongoDB
  var formData = new FormData();
  formData.append("latitude", newLat);
  formData.append("longitude", newLng);
  formData.append("functionality", "user_location");

  fetch("/map/map_functions.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.text())
    .then((data) => {
      console.log("Coordinates updated:", data);
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}

// Function to create the userMarker
function createMarker(lat, long) {
  userMarker = L.marker([lat, long], {
    draggable: true,
    icon: userIcon,
  }).addTo(map);

  // Bind the initial popup
  var popupContent = "Latitude: " + lat + "<br>Longitude: " + long;
  userMarker.bindPopup(popupContent);

  // Listen for the "dragend" event on the marker
  userMarker.on("dragend", function (event) {
    // When the marker is dragged to a new position, update the coordinates in the popup
    updatePopup();
  });
}

// Fetch user coordinates and create the marker
function get_user_initial_location() {
  var formData = new FormData();
  formData.append("functionality", "current_marker_location");

  fetch("/map/map_functions.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      var lat = data.user_latitude;
      var long = data.user_longitude;

      console.log(lat);
      console.log(long);
      // Create the userMarker and add it to the map
      createMarker(lat, long);
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}

// Call get_user_initial_location after map is initialized
// map.on("load", get_user_initial_location);
get_user_initial_location();
