const searchInput = document.getElementById('searchInput');
const searchResults = document.getElementById('searchResults');

searchInput.addEventListener('input', function () {
  const searchTerm = searchInput.value.toLowerCase();

  // Clear previous search results
  searchResults.innerHTML = '';

  // Filter markers based on search term
  const filteredMarkers = markers.filter(marker =>
    marker._popup.getContent().toLowerCase().includes(searchTerm)
  );

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
    nameElement.classList.add('result-name');

    // Append elements to the result item
    resultItem.appendChild(nameElement);

    // Strip HTML tags from popup content and replace with spaces
    const popupContent = marker._popup.getContent();

    // Set text content for name and description elements with a <br> tag
    nameElement.innerHTML = popupContent;

    // Add event listener when clicking the resultItem to focus on map
    resultItem.addEventListener('click', () => {
      map.setView(marker.getLatLng(), 25);
      showStoreDetails(marker);
    });
    searchResults.appendChild(resultItem);

    //console.log('Name:', nameElement.textContent);
  });
  function showStoreDetails(marker) {
    // Extract store name from marker popup content
    const popupContent = marker._popup.getContent();
    const storeName = popupContent.replace(/<[^>]*>/g, ''); // Remove HTML tags
  
    // Update the store details panel with the store name
    const storeNameElement = document.getElementById('storeName');
    storeNameElement.textContent = storeName;
  
    // Fetch and display products for the selected store
    fetchProductsForStore(storeName);
  }
  
  function fetchProductsForStore(storeName) {
    // Send an AJAX request to your PHP script to fetch products for the selected store
    fetch('get-products.php?store=' + encodeURIComponent(storeName))
      .then(response => response.json())
      .then(data => {
        const productList = document.getElementById('productList');
        productList.innerHTML = ''; // Clear existing product list
  
        data.forEach(product => {
          const productItem = document.createElement('li');
          productItem.textContent = product.name;
          productList.appendChild(productItem);
        });

      })
      .catch(error => console.error('Error loading product data:', error));
     //console.log('product list:', productList.textContent);
  }
});
