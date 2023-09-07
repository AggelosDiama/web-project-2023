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

// Define an empty array to store markers
const markers = [];

fetch('get-markets.php') // Replace with the correct path to your PHP script
  .then(response => response.json())
  .then(data => {
    // Loop through the data and create markers
    data.forEach(markerData => {
      const coordinates = markerData.coordinates;
      const name = markerData.name;

      const marker = L.marker([coordinates[1], coordinates[0]])
        .bindPopup(`<b>${name}</b>`)
        .addTo(map);

      markers.push(marker);
    });
  })
  .catch(error => console.error('Error loading market data:', error));
  
  
