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

// Create a marker and add it to the map
var userMarker = L.marker([38.246318, 21.735255], {
  draggable: true,
  icon: userIcon,
}).addTo(map);

// Bind the popup initially
var popupContent =
  "Latitude: " +
  userMarker.getLatLng().lat +
  "<br>Longitude: " +
  userMarker.getLatLng().lng;
userMarker.bindPopup(popupContent);

// Function to update the popup content with the new coordinates
function updatePopup() {
  var popupContent =
    "Latitude: " +
    userMarker.getLatLng().lat +
    "<br>Longitude: " +
    userMarker.getLatLng().lng;
  userMarker.getPopup().setContent(popupContent);

  var newLat = userMarker.getLatLng().lat;
  var newLng = userMarker.getLatLng().lng;

  // Send an AJAX POST request to a PHP file to store the new coordinates in MongoDB
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
      console.log(data); // Log the response from the PHP file
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}

// Listen for the dragend event on the marker
userMarker.on("dragend", function (event) {
  // When the marker is dragged to a new position, update the coordinates in the popup
  updatePopup();
});
