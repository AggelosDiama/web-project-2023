var userMarker;
var map;
var osm;

// var userIcon = L.icon({
//   iconUrl: "/map/icons8-circle-48.png",
//   iconAnchor: [25, 20],
// });

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

function createMap(lat, long) {
  map = L.map("map", {
    center: [lat, long],
    zoom: 15,
  });

  osm = L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution:
      '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
  });
  osm.addTo(map);
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

      createMap(lat, long);
      createMarker(lat, long);
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}

function update_products() {
  var formData = new FormData();
  fetch("/main-interface/check-offer-date.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log(data);
      if (data == 200) {
        console.log("Products have been updated");
      } else console.log("There was an error updating the products");
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}

get_user_initial_location();
