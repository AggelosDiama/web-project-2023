// ------------ CODE FOR DISPLAYING FETCHING LOCATIONS---------------

const markers = []; // Define an empty array to store markets
var is_user_close = 0;

fetch("/main-interface/get-markets.php")
  .then((response) => response.json())
  .then((data) => {
    // Loop through the data and create markers
    data.forEach((marketData) => {
      const coordinates = marketData.coordinates;
      const name = marketData.name;
      const address = marketData.address;
      const availableProducts = marketData.products;
      const market_id = marketData.id;
      
      const icon = availableProducts.length > 0 ? blueIcon : redIcon;
      const productCount = availableProducts.length;

      const popupText = `
        <b>${name}</b><br>
        <span style="color: #888;">${address}</span><br>
        <b>Available Products:</b> ${productCount > 0 ? productCount : 'No products available'}`;

      const marker = L.marker([coordinates[1], coordinates[0]], {
        icon: icon,
        id: market_id,
        name: name,
        address: address,
      }).bindPopup(popupText);

      if (availableProducts.length>0) {
        map.addLayer(marker);
      }
      
      markers.push(marker);
    });
  })
  .catch((error) => console.error("Error loading market data:", error));
console.log("Markers", markers);

// ------------ CODE FOR DISPLAYING THE SEARCH RESULTS OF MARKETS ----------------

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

const searchInput = document.getElementById("searchInput");
const searchResults = document.getElementById("searchResults");

const searchByMarketNameButton = document.getElementById("searchByMarketName");
const searchByProductCategoryButton = document.getElementById("searchByProductCategory");
const categorySearchDropdown = document.getElementById("categorySearchDropdown");
var searchFlag = 1;

searchByMarketNameButton.addEventListener("click", () => {
  // Toggle active class for buttons
  searchByMarketNameButton.classList.add("active");
  searchByProductCategoryButton.classList.remove("active");

  // Show market name input, hide category dropdown
  searchInput.classList.remove("hidden");
  categorySearchDropdown.classList.add("hidden");
  searchFlag = 1;
});

searchByProductCategoryButton.addEventListener("click", () => {
  // Toggle active class for buttons
  searchByMarketNameButton.classList.remove("active");
  searchByProductCategoryButton.classList.add("active");

  // Show category dropdown, hide market name input
  searchInput.classList.add("hidden");
  categorySearchDropdown.classList.remove("hidden");
  searchFlag = 0;

  // Clear existing options in the categorySearchDropdown, but keep the default option
  const options = categorySearchDropdown.querySelectorAll("option");
  options.forEach((option) => {
    if (!option.disabled) {
      option.remove();
    }
  });

  fetch(
    "http://webproject2023.ddns.net/main-interface/get-categories-for-ddmenu.php"
  )
    .then((response) => response.json())
    .then((categories) => {
      categories.forEach((category) => {
        const option = document.createElement("option");
        option.value = category.id; // Use the appropriate property for category ID
        option.textContent = category.name; // Use the appropriate property for category name
        categorySearchDropdown.appendChild(option);
      });
    })
    .catch((error) => {
      console.error("Error fetching categories:", error);
    });
});

 // declare filteredmarkets here to be able to use it and not be emptied every time
searchInput.addEventListener("input", fetchResults);
categorySearchDropdown.addEventListener("change", fetchResults);


function fetchResults(event) {
  var searchTerm = ""
  
  // Clear previous search results
  searchResults.innerHTML = "";

  if(searchFlag){
    searchTerm = searchInput.value.toLowerCase();
    let filteredMarkets = [ ];

    // Filter markers based on search term
    filteredMarkets = markers.filter((marker) =>
    marker._popup.getContent().toLowerCase().includes(searchTerm)
    );

    console.log("skata3", filteredMarkets);

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
    displayResults(filteredMarkets);
  } else {
    searchTerm = categorySearchDropdown.value;
    const encodedSearchTerm = encodeURIComponent(searchTerm); //Because the search category name is in greek characters
    //console.log(encodedSearchTerm);

    fetch(`http://webproject2023.ddns.net/main-interface/get-markets-based-on-cat.php?searchInput=${encodedSearchTerm}`)
      .then((response) => response.json())
      .then((marketsFromCat) => {

        // Clear previous results
        searchResults.innerHTML = "";
        //console.log(marketsFromCat);

        let filteredMarkets = [ ];
        
       // Extract the names from marketsFromCat
       const marketNamesFromCat = marketsFromCat.map((market) => market.name.toLowerCase());

       // Filter markers based on names in marketNamesFromCat
       filteredMarkets = markers.filter((marker) =>
         marketNamesFromCat.includes(marker.options.name.toLowerCase())
       );  
      console.log('skata1', filteredMarkets);

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
        displayResults(filteredMarkets);
      }      
    )
  }

  // Display search results
  function displayResults(selectedMarkets){
    console.log('skata', selectedMarkets);
    selectedMarkets.forEach((marker) => {
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
  }

  // Delete button functionallity
  function deleteOffer(marketId){

    var marketId = marker.options.id;

    var formData = new FormData();
    formData.append("functionality", "deleteOffer"); 
    formData.append("store_id", marketId);
    fetch("/map/map_functions.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if(data == 500) alert("Could not delete offer.");
      })
      .catch((error) => {
        console.error("Error:", error);
      });
  }
  

  // ------------ CODE FOR DISPLAYING THE PRODUCTS OF SELECTED MARKET ----------------

  function showStoreDetails(marker) {
    var marketId = marker.options.id;
    var storeName = marker.options.name;

    localStorage.setItem("marketId", marketId);

    // Update the store details panel with the store name
    const storeNameElement = document.getElementById("storeName");
    storeNameElement.textContent = storeName;

    const submitButton = document.getElementById("submitOffer");

    var formData = new FormData();
    formData.append("functionality", "check_user_distance"); 
    formData.append("store_id", marketId);
    fetch("/map/map_functions.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        is_user_close = data.user_distance; 
        console.log(is_user_close);
        if(!is_user_close){
          submitButton.classList.add("hide-submit-button");
        } else submitButton.classList.remove("hide-submit-button");
      })
      .catch((error) => {
        console.error("Error:", error);
      });

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

        if (products.length > 0) {
          products.forEach((product) => {
            const productItem = document.createElement("div");
            productItem.classList.add("product-item"); 
            productList.appendChild(productItem);
  
            // Create a container for the product details
            const productDetails = document.createElement("div");
            productDetails.classList.add("product-details");
            productItem.appendChild(productDetails);
  
            // Create a paragraph for displaying product name
            const productName = document.createElement("p");
            productName.classList.add("product-name"); 
            productName.textContent = product.name;
            productDetails.appendChild(productName);

            //Create delete product button that will be visible only to admin
            const deleteButton = document.createElement("button");
            deleteButton.classList.add("delete-button");
            deleteButton.textContent = 'Delete Product';
            productDetails.appendChild(deleteButton);

            var formData = new FormData();
            formData.append("functionality", "check_if_admin");
            fetch("/map/map_functions.php", {
              method: "POST",
              body: formData,
            })
              .then((response) => response.json())
              .then((data) => {      
                if (data == 1) {
                  deleteButton.classList.add("hide-button");
                }
              })
              .catch((error) => {
                console.error("Error:", error);
              });
            
            deleteButton.addEventListener("click", () => {
              var formData = new FormData();
              formData.append("functionality", "delete_offer");
              formData.append("store_id", marketId);
              formData.append("product_id", product.id);
              fetch("/map/map_functions.php", {
                method: "POST",
                body: formData,
              })
                .then((response) => {
                  alert("Product has been deleted")
                  location.reload();
                })
                .catch((error) => {
                  console.error("Error:", error);
                });  
            });

            // Create a paragraph for displaying product category and subcategory
            const productCategorySubcategory = document.createElement("p");
            productCategorySubcategory.classList.add(
              "product-category-subcategory"
            );
            productCategorySubcategory.textContent = `${product.category}, ${product.subcategory}`;
            productDetails.appendChild(productCategorySubcategory);
  
            const productStockInfo = document.createElement("div");
            const productAvailable = document.createElement("p");
            const productChangeStock = document.createElement("a"); // link button to click it to change stock 
            
            productStockInfo.classList.add("product-stock-info");
            productAvailable.classList.add("product-available");
            productChangeStock.classList.add("product-stock");

            productAvailable.textContent = product.available ? 'In stock' : 'Out of stock';
            productChangeStock.textContent = product.available ? ' (Out of stock?)' : ' (In stock?)';

            productChangeStock.addEventListener("click", () => {
              console.log('skata');
              const confirmationMessage = product.available
                ? "Are you sure you want to mark this product as out of stock?"
                : "Are you sure you want to mark this product as in stock?";
            
              if (window.confirm(confirmationMessage)) {
                // User confirmed, toggle the product availability here
                // Example: You can set product.available to its opposite value
                product.available = !product.available;
                productChangeStock.textContent = product.available ? '(Out of stock?)' : '(In stock?)';
            
                // Update the text content of productAvailable accordingly
                productAvailable.textContent = product.available ? 'In stock' : 'Out of stock';

                var formData = new FormData();
                formData.append("functionality", "product_availability");
                formData.append("availability", product.available);
                formData.append("store_id", marketId);
                formData.append("product_id", product.id);
                
                fetch("/map/map_functions.php", {
                  method: "POST",
                  body: formData,
                })
                  .then((response) => response.text())
                  .catch((error) => {
                    console.error("Error:", error);
                  });
              }
            });
  
            productStockInfo.appendChild(productAvailable);
            productStockInfo.appendChild(productChangeStock);
            productDetails.appendChild(productStockInfo);
  
            // Create a paragraph for displaying product likes and dislikes with icons
            const productLikesDislikes = document.createElement("div");
            productLikesDislikes.classList.add("product-likes-dislikes"); 
  
  
            // Create a span for the thumbs-up (like) icon
            const thumbsUpIcon = document.createElement("span");
            const thumbsUpCircleBg = document.createElement("div");
  
            thumbsUpCircleBg.classList.add("thumbsup-circle");
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
  
                //remove the class filled if the other button is pushed
                if (thumbsDownIcon.classList.contains("filled")){
                  thumbsDownIcon.classList.remove("filled");
                  //product.dislikes -=1;
                }
                // Increment the number of likes and update the database here
                product.likes += 1;
                
  
                formData.append("change_type", "add");
              } else {
                thumbsUpIcon.classList.remove("filled");
                thumbsUpIcon.classList.add("outline");
                // Decrement the number of likes and update the database here (if needed)
                product.likes -= 1;
                
                formData.append("change_type", "remove");
              }
  
              // Update the like count to the database
              fetch("/map/map_functions.php", {
                method: "POST",
                body: formData,
              })
                .then((response) => response.text())
                .catch((error) => {
                  console.error("Error:", error);
                });
              // Update the displayed number of likes
              productLikesDislikes.querySelector(".likes-count").textContent =
                product.likes;
            });
  
            // Create a span for the thumbs-down (dislike) icon
            const thumbsDownCircleBg = document.createElement("div");
            thumbsDownCircleBg.classList.add("thumbsdown-circle");
  
            const thumbsDownIcon = document.createElement("span");
            thumbsDownIcon.classList.add("fas", "fa-thumbs-down", "outline");
  
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
  
                //remove the class filled if the other button is pushed
                if (thumbsUpIcon.classList.contains("filled")){
                  thumbsUpIcon.classList.remove("filled");
                  //product.likes -=1;
                }
  
                // Increment the number of dislikes and update the database here
                product.dislikes += 1;
                
                formData.append("change_type", "add");
              } else {
                thumbsDownIcon.classList.remove("filled");
                thumbsDownIcon.classList.add("outline");
                // Decrement the number of dislikes and update the database here (if needed)
                product.dislikes -= 1;
                
                formData.append("change_type", "remove");
              }
              // Update the like count to the database
              fetch("/map/map_functions.php", {
                method: "POST",
                body: formData,
              })
                .then((response) => response.text())
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
            thumbsUpCircleBg.appendChild(thumbsUpIcon);
            productLikesDislikes.appendChild(thumbsUpCircleBg);
            productLikesDislikes.appendChild(likesCount);
  
            thumbsDownCircleBg.appendChild(thumbsDownIcon);
            productLikesDislikes.appendChild(thumbsDownCircleBg);
            productLikesDislikes.appendChild(dislikesCount);
  
            productDetails.appendChild(productLikesDislikes);
  
            if (!product.available) {
              productAvailable.classList.add("unavailable");
              productLikesDislikes.classList.add("unavailable");
              thumbsUpIcon.classList.add("unavailable");
              thumbsDownIcon.classList.add("unavailable");
              productItem.classList.add("unavailable");
            }

            var formData = new FormData();
            formData.append("functionality", "check_user_distance"); 
            formData.append("store_id", marketId);
            fetch("/map/map_functions.php", {
              method: "POST",
              body: formData,
            })
              .then((response) => response.json())
              .then((data) => {
                is_user_close = data.user_distance; 
                console.log(is_user_close);
                if(!is_user_close){
                  productLikesDislikes.classList.add("unavailable");
                  thumbsUpIcon.classList.add("unavailable");
                  thumbsDownIcon.classList.add("unavailable");
                  productItem.classList.add("unavailable");
                } else {
                  productLikesDislikes.classList.remove("unavailable");
                  thumbsUpIcon.classList.remove("unavailable");
                  thumbsDownIcon.classList.remove("unavailable");
                  productItem.classList.remove("unavailable");
                }
              })
              .catch((error) => {
                console.error("Error:", error);
              });
  
            const miscInfo = document.createElement("p");
            miscInfo.classList.add("product-misc-info")
            miscInfo.textContent = `Submitted by ${product.madeByUser} (${product.userScore}) on ${product.dateSubmitted}`;
            productDetails.appendChild(miscInfo);
            
  
            // Create a paragraph for displaying product price
            const productPrice = document.createElement("p");
            productPrice.classList.add("product-price"); 
            productPrice.textContent = `${product.price}€`;
            productDetails.appendChild(productPrice);
          });
        } else {
          const offerError = document.createElement("p");
          offerError.classList.add("offer-error");
          productList.appendChild(offerError);
          offerError.textContent = `No offers for this store right now`
        }
        // Loop through the products and create a list item for each product
        
      })
      .catch((error) => {
        console.error("Error:", error);
      });
  }
};
