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

fetch('map.geojson')
  .then(response => response.json())
  .then(data => {
    // Loop through GeoJSON data aSnd create markers
    data.features.forEach(feature => {
      const coordinates = feature.geometry.coordinates;
      const name = feature.properties.name;

      const marker = L.marker([coordinates[1], coordinates[0]])
        .bindPopup(`<b>${name}</b>`)
        .addTo(map);

      markers.push(marker); // Add the marker to the markers array
    });
  }).catch(error => console.error('Error loading GeoJSON data:', error));

  //console.log(markers); 

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

    //Display search results
    filteredMarkers.forEach(marker => {
      const resultItem = document.createElement('div');
      resultItem.classList.add('result-item');

      // Create elements for the name and description
      const nameElement = document.createElement('div');
      nameElement.classList.add('result-name');

      const descriptionElement = document.createElement('div');
      descriptionElement.classList.add('result-description');

      // Append elements to the result item
      resultItem.appendChild(nameElement);
      resultItem.appendChild(descriptionElement);

      // Strip HTML tags from popup content and replace with spaces
      const popupContent = marker._popup.getContent();
      //const plainTextContent = popupContent.replace(/<\/?[^>]+(>|$)/g, " ");

      // Split the plain text content into lines
       const lines = popupContent.split('<br>');
  
      // Set text content for name and description elements with a <br> tag
      nameElement.innerHTML = lines[0];
      descriptionElement.textContent = lines[1];


      //add event listener when clicking the resultItem to focus on map
      resultItem.addEventListener('click', () => {
        map.setView(marker.getLatLng(), 15);
      });
      searchResults.appendChild(resultItem);

      //console.log(markers); 
      console.log('Name:', nameElement.textContent);
      console.log('Description:', descriptionElement.textContent);
      // console.log(lines[0]);
      // console.log(lines[1]);
    })

  })

  
  
