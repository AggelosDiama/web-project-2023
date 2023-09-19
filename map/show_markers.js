// ------------ CODE FOR DISPLAYING FETCHING LOCATIONS---------------

const markers = []; // Define an empty array to store markets

fetch("/main-interface/get-markets.php")
  .then((response) => response.json())
  .then((data) => {
    // Loop through the data and create markers
    data.forEach((marketData) => {
      const coordinates = marketData.coordinates;
      const name = marketData.name;
      const address = marketData.address;
      const market_id = marketData.id;    

      const marker = L.marker([coordinates[1], coordinates[0]], {
        id: market_id,
        name: name,
        address: address,
      }).bindPopup(`<b>${name}</b><br><span style="color: #888;">${address}</span>`);
      //console.log(marker.options.name);

      markers.push(marker);
    });
  })
  .catch((error) => console.error("Error loading market data:", error));
console.log("Markers", markers);

// ------------ CODE FOR DISPLAYING THE SEARCH RESULTS OF MARKETS ----------------

const searchInput = document.getElementById("searchInput");
const searchResults = document.getElementById("searchResults");

function toggleSidebar(show) {
  const sidebarContainer = document.querySelector(".sidebarStoreDetails");
  const xMarkButton = document.querySelector(".fa-xmark");

  // sidebarContainer.classList.toggle('show-sidebar', show);
  xMarkButton.addEventListener("click", () => {
    show = false;
    toggleSidebar(show);
  });
  if (show) {
    sidebarContainer.classList.add("show-sidebar");
  } else {
    sidebarContainer.classList.remove("show-sidebar");
  }
}

searchInput.addEventListener("input", function () {
  const searchTerm = searchInput.value.toLowerCase();

  // Clear previous search results
  searchResults.innerHTML = "";

  // Filter markers based on search term
  const filteredMarkets = markers.filter((marker) =>
    marker._popup.getContent().toLowerCase().includes(searchTerm)
  );

  // Show only the filtered markers on the map
  markers.forEach((marker) => {
    if (filteredMarkets.includes(marker)) {
      map.addLayer(marker);
    } else {
      map.removeLayer(marker);
    }
    marker.addEventListener("click", () => {
      showStoreDetails(marker);
      toggleSidebar(true);
    });
  });

  // Display search results
  filteredMarkets.forEach((marker) => {
    const resultItem = document.createElement("div"); //create result-item and add class
    resultItem.classList.add("result-item");

    const nameElement = document.createElement("div"); //create result-name and add class
    nameElement.classList.add("result-name");

    // Create a div for the address and add class
    const addressElement = document.createElement("div");
    addressElement.classList.add("result-address");

    resultItem.appendChild(nameElement); // Append elements to the result item
    resultItem.appendChild(addressElement);

    nameElement.innerHTML = marker.options.name; //initialise with the name of the market
    addressElement.innerHTML = marker.options.address;

    // Add event listener when clicking the resultItem to focus on map
    resultItem.addEventListener("click", () => {
      map.setView(marker.getLatLng(), 25);
      showStoreDetails(marker);
      toggleSidebar(true);
    });
    searchResults.appendChild(resultItem);

    //console.log('Name:', nameElement.textContent);
  });

  // ------------ CODE FOR DISPLAYING THE PRODUCTS OF SELECTED MARKET ----------------

  function showStoreDetails(marker) {
    var marketId = marker.options.id;
    var storeName = marker.options.name;
    console.log(marketId, storeName);

    localStorage.setItem("marketId", marketId);

    // Update the store details panel with the store name
    const storeNameElement = document.getElementById("storeName");
    storeNameElement.textContent = storeName;

    // Fetch and display products for the selected store
    fetchProductsForStore(marketId);

    // Show the entire sidebar
    //toggleSidebar(true);
    console.log(storeName, marketId);
  }

  function fetchProductsForStore(marketId) {
    // Make a GET request to get-product-ids.php
    fetch(
      `http://webproject2023.ddns.net/main-interface/get-products.php?marketId=${marketId}`
    )
      .then((response) => response.json())
      .then((products) => {
        console.log(products);
        // Get the productList div
        const productList = document.getElementById("productList");
        productList.innerHTML = ""; // Clear existing product list

        // Loop through the products and create a list item for each product
        products.forEach((product) => {
          const productItem = document.createElement("div");
          productItem.classList.add("product-item"); // Apply the CSS class
          productList.appendChild(productItem);

          // Create a container for the product details
          const productDetails = document.createElement("div");
          productDetails.classList.add("product-details");
          productItem.appendChild(productDetails);

          // Create a paragraph for displaying product name
          const productName = document.createElement("p");
          productName.classList.add("product-name"); // Apply the CSS class
          productName.textContent = product.name;
          productDetails.appendChild(productName);

          // Create a paragraph for displaying product category and subcategory
          const productCategorySubcategory = document.createElement("p");
          productCategorySubcategory.classList.add(
            "product-category-subcategory"
          ); // Apply the CSS class
          productCategorySubcategory.textContent = `${product.category}, ${product.subcategory}`;
          productDetails.appendChild(productCategorySubcategory);

          const productAvailable = document.createElement("p");
          productAvailable.classList.add("product-available");
          productAvailable.textContent = product.available ? 'In stock' : 'Out of stock';

          if (!product.available) {
            productAvailable.classList.add("unavailable");
          }

          productDetails.appendChild(productAvailable);

          // Create a paragraph for displaying product likes and dislikes with icons
          const productLikesDislikes = document.createElement("p");
          productLikesDislikes.classList.add("product-likes-dislikes"); // Apply the CSS class

          // Create a span for the thumbs-up (like) icon
          const thumbsUpIcon = document.createElement("span");
          thumbsUpIcon.classList.add("fas", "fa-thumbs-up", "outline"); // Add 'outline' class for outlined icon
          thumbsUpIcon.addEventListener("click", () => {
            // Data for the POST request
            var formData = new FormData();
            formData.append("functionality", "user_likes");
            formData.append("store_id", marketId);
            formData.append("product_id", product.id);

            // Toggle between outline and filled icons
            if (thumbsUpIcon.classList.contains("outline")) {
              thumbsUpIcon.classList.remove("outline");
              thumbsUpIcon.classList.add("filled");
              // Increment the number of likes and update the database here
              product.likes += 1;
              //updateDatabase(product.id, 'likes', product.likes); // Replace with your update function

              formData.append("change_type", "add");
            } else {
              thumbsUpIcon.classList.remove("filled");
              thumbsUpIcon.classList.add("outline");
              // Decrement the number of likes and update the database here (if needed)
              product.likes -= 1;
              //updateDatabase(product.id, 'likes', product.likes); // Replace with your update function
              formData.append("change_type", "remove");
            }

            // Update the like count to the database
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
            // Update the displayed number of likes
            productLikesDislikes.querySelector(".likes-count").textContent =
              product.likes;
          });

          // Create a span for the thumbs-down (dislike) icon
          const thumbsDownIcon = document.createElement("span");
          thumbsDownIcon.classList.add("fas", "fa-thumbs-down", "outline"); // Add 'outline' class for outlined icon
          thumbsDownIcon.addEventListener("click", () => {
            // Data for the POST request
            var formData = new FormData();
            formData.append("functionality", "user_dislikes");
            formData.append("store_id", marketId);
            formData.append("product_id", product.id);

            // Toggle between outline and filled icons
            if (thumbsDownIcon.classList.contains("outline")) {
              thumbsDownIcon.classList.remove("outline");
              thumbsDownIcon.classList.add("filled");
              // Increment the number of dislikes and update the database here
              product.dislikes += 1;
              //updateDatabase(product.id, 'dislikes', product.dislikes); // Replace with your update function
              formData.append("change_type", "add");
            } else {
              thumbsDownIcon.classList.remove("filled");
              thumbsDownIcon.classList.add("outline");
              // Decrement the number of dislikes and update the database here (if needed)
              product.dislikes -= 1;
              //updateDatabase(product.id, 'dislikes', product.dislikes); // Replace with your update function
              formData.append("change_type", "remove");
            }
            // Update the like count to the database
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

            // Update the displayed number of dislikes
            productLikesDislikes.querySelector(".dislikes-count").textContent =
              product.dislikes;
          });

          // Initialize the likes and dislikes counts
          const likesCount = document.createElement("span");
          likesCount.classList.add("likes-count");
          likesCount.textContent = product.likes;

          const dislikesCount = document.createElement("span");
          dislikesCount.classList.add("dislikes-count");
          dislikesCount.textContent = product.dislikes;

          // Add the thumbs-up and thumbs-down icons to the likes and dislikes paragraph
          productLikesDislikes.appendChild(thumbsUpIcon);
          productLikesDislikes.appendChild(likesCount);
          productLikesDislikes.appendChild(thumbsDownIcon);
          productLikesDislikes.appendChild(dislikesCount);

          productDetails.appendChild(productLikesDislikes);

          const miscInfo = document.createElement("p");
          miscInfo.classList.add("product-misc-info")
          miscInfo.textContent = `Submitted by ${product.madeByUser} (${product.userScore}) on ${product.dateSubmitted}`;
          productDetails.appendChild(miscInfo);
          

          // Create a paragraph for displaying product price
          const productPrice = document.createElement("p");
          productPrice.classList.add("product-price"); // Apply the CSS class
          productPrice.textContent = `${product.price}€`;
          productDetails.appendChild(productPrice);
        });
      })
      .catch((error) => {
        console.error("Error:", error);
      });
  }
});
