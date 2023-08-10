var map = document.getElementById("map");
var map = L.map("map", {
  center: [38.246318, 21.735255],
  zoom: 15,
});

var osm = L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
  attribution:
    '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
});
osm.addTo(map);

var marker = new L.marker([38.246318, 21.735255], {}).addTo(map);
// var popup = marker.bindPopup("SKATA" + marker.getLangLng()).openPopup();

marker.bindPopup("Skata");

fetch('sample_locations.json')
  .then(response => response.json())
  .then(data => {
    // Loop through the JSON data and create markers
    data.forEach(markerData => {
      const marker = L.marker([markerData.latitude, markerData.longitude]).addTo(map);
      
      // Add a pop-up with additional information
      marker.bindPopup(`<b>${markerData.name}</b><br>${markerData.description}`);
    });
  })
  .catch(error => console.error('Error fetching or processing JSON data:', error));