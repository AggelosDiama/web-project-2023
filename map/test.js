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

// Define an empty array to store markers
const markers = [];

// Load JSON data and create markers
fetch('sample_locations.json')
  .then(response => response.json())
  .then(data => {
    // Loop through JSON data and create markers
    data.forEach(location => {
      const marker = L.marker([location.latitude, location.longitude])
        .bindPopup(`<b>${location.name}</b><br>${location.description}`)
        .addTo(map);

      markers.push(marker); // Add the marker to the markers array
    });


const searchInput = document.getElementById('searchInput');
const searchResults = document.getElementById('searchResults');

searchInput.addEventListener('input', function () {
  const searchTerm = searchInput.value.toLowerCase();

  // Clear previous search results
  searchResults.innerHTML = '';

  // Filter markers based on search term
  const filteredMarkers = markers.filter(marker => marker._popup.getContent().toLowerCase().includes(searchTerm));

  // Show only the filtered markers on the map
  markers.forEach(marker => {
    if (filteredMarkers.includes(marker)) {
      map.addLayer(marker);
    } else {
      map.removeLayer(marker);
    }
  });

  // Display search results
  filteredMarkers.forEach(marker => {
    const resultItem = document.createElement('div');
    resultItem.classList.add('result-item');
    
    // Create elements for the name and description
    const nameElement = document.createElement('div');
    const descriptionElement = document.createElement('div');
    nameElement.classList.add('result-name');
    descriptionElement.classList.add('result-description');
    

    // Assign name and description to elements
    nameElement.textContent = markers.name;
    descriptionElement.textContent = "text";

    
    // Strip HTML tags from popup content
    const popupContent = marker._popup.getContent();
    const plainTextContent = popupContent.replace(/<\/?[^>]+(>|$)/g, " ");

    
    // Split the plain text content into lines
    const lines = plainTextContent.split('\n');
  
    // Set text content for name and description elements with a <br> tag
    nameElement.innerHTML = lines[0] + '<br>';
    descriptionElement.textContent = lines[1];
  
    // Append elements to the result item
    resultItem.appendChild(nameElement);
    resultItem.appendChild(descriptionElement);
    
    resultItem.addEventListener('click', () => {
      map.setView(marker.getLatLng(), 15);
    });
    searchResults.appendChild(resultItem);

    console.log('Name:', nameElement.textContent);
    console.log('Description:', descriptionElement.textContent);
  });
});
})
.catch(error => console.error('Error loading JSON data:', error));



