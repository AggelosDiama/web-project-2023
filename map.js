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
