// Function to open the popup
function openPopup() {
  var popup = document.getElementById("offerPopup");
  var overlay = document.getElementById("overlay"); // Get the overlay element

  if (popup && overlay) {
    popup.style.display = "block"; // Show the popup
    overlay.style.display = "block"; // Show the overlay
  }
  fetchCategories();
}

// Function to close the popup
function closePopup() {
  var popup = document.getElementById("offerPopup");
  var overlay = document.getElementById("overlay"); // Get the overlay element

  if (popup && overlay) {
    popup.style.display = "none"; // Hide the popup
    overlay.style.display = "none"; // Hide the overlay
  }
}

// ----------- Code to submit an offer --------------

// Function to fetch categories from MongoDB and populate the category dropdown
function fetchCategories() {
  fetch(
    "http://webproject2023.ddns.net/main-interface/get-categories-for-ddmenu.php"
  )
    .then((response) => response.json())
    .then((categories) => {
      const categoryDropdown = document.getElementById("categoryDropdown");
      categories.forEach((category) => {
        const option = document.createElement("option");
        option.value = category.id; // Use the appropriate property for category ID
        option.textContent = category.name; // Use the appropriate property for category name
        categoryDropdown.appendChild(option);
      });
    })
    .catch((error) => {
      console.error("Error fetching categories:", error);
    });
}

// Function to fetch subcategories from the selected category and populate the subcategory dropdown
function fetchSubcategories(selectedCategoryId) {
  fetch(
    "http://webproject2023.ddns.net/main-interface/get-categories-for-ddmenu.php"
  ) // Fetch the categories collection
    .then((response) => response.json())
    .then((categories) => {
      const subcategoryDropdown = document.getElementById(
        "subcategoryDropdown"
      );
      subcategoryDropdown.innerHTML = ""; // Clear previous options

      // Find the selected category in the categories collection
      const selectedCategory = categories.find(
        (category) => category.id === selectedCategoryId
      );

      if (selectedCategory && selectedCategory.subcategories) {
        selectedCategory.subcategories.forEach((subcategory) => {
          const option = document.createElement("option");
          option.value = subcategory.uuid; // Use the appropriate property for subcategory ID
          option.textContent = subcategory.name; // Use the appropriate property for subcategory name
          subcategoryDropdown.appendChild(option);
        });
      }
      //console.log(selectedCategoryId);
    })

    .catch((error) => {
      console.error("Error fetching subcategories:", error);
    });
}

// Function to fetch products from the selected subcategory and populate the product dropdown
function fetchProducts(selectedSubcategoryId) {
  fetch(
    `http://webproject2023.ddns.net/main-interface/get-products-for-ddmenu.php?selectedSubcategoryId=${selectedSubcategoryId}`
  )
    .then((response) => response.json())
    .then((products) => {
      const productDropdown = document.getElementById("productDropdown");
      productDropdown.innerHTML = ""; // Clear previous options
      products.forEach((product) => {
        const option = document.createElement("option");
        option.value = product.id; // Use the appropriate property for product ID
        option.textContent = product.name; // Use the appropriate property for product name
        productDropdown.appendChild(option);
      });
    })
    .catch((error) => {
      console.error("Error fetching products:", error);
    });
}

// Initialize an empty array for suggestions
let suggestions = [];

// Get references to HTML elements
const productSearchInput = document.getElementById("searchProduct");
const suggestionList = document.getElementById("suggestionList");

/// Add event listeners for input focus and blur
productSearchInput.addEventListener("focus", () => {
  // When input is focused, show suggestions
  searchProducts(productSearchInput.value);
});

productSearchInput.addEventListener("blur", () => {
  // When input loses focus, hide suggestions
  suggestionList.style.display = "none";
});



// Function to search products and display suggestions
function searchProducts(userInput) {
  if (userInput) {
    // Fetch product data from PHP script
    fetch(
      `http://webproject2023.ddns.net/main-interface/get-all-products.php?search=${userInput}`
    )
      .then((response) => response.json())
      .then((data) => {
        if (data && data.length > 0) {
          // Update the suggestions array with product names
          suggestions = data.map((product) => product.name);
          showSuggestions();
        } else {
          suggestions = [];
          showSuggestions();
        }
      })
      .catch((error) => {
        console.error("Error fetching products:", error);
      });
  } else {
    suggestions = [];
    showSuggestions();
  }
}

// Function to display suggestions in the suggestionList
function showSuggestions() {
  suggestionList.innerHTML = ""; // Clear previous suggestions
  if (suggestions.length > 0) {
    suggestions.forEach((suggestion) => {
      const suggestionItem = document.createElement("div");
      suggestionItem.textContent = suggestion;
      suggestionList.appendChild(suggestionItem);
    });
    suggestionList.style.display = "block"; // Show the suggestion list
  } else {
    suggestionList.style.display = "none"; // Hide the suggestion list if no suggestions
  }
}

// Handle user clicks on a suggestion
suggestionList.addEventListener("click", (e) => {
  if (e.target && e.target.textContent) {
    // Fill the search input with the selected suggestion
    productSearchInput.value = e.target.textContent;
    // Hide the suggestion list
    suggestionList.style.display = "none";
    // Prevent the default click behavior (e.g., following a link)
    e.preventDefault();
  }
});




// Function to submit the offer
function submitOffer() {
  const selectedProduct = document.getElementById("productDropdown").value;
  const selectedAllProducts = document.getElementById("searchProduct").value;
  const offerPrice = document.getElementById("offerPrice").value;

  // Your logic for handling the submitted offer
  console.log("Selected Product:", selectedProduct);
  console.log("Offer Price:", offerPrice);
  console.log("Offer Price:", selectedAllProducts);

  var formData = new FormData();
  formData.append("offered_price", offerPrice);
  formData.append("product_id", selectedProduct);
  formData.append("store_id", localStorage.getItem("marketId"));

  fetch("/main-interface/product_functions.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log(data);
      if(data == 500) {
        alert("User is too far away");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
    });

  // Close the popup
  closePopup();
}

// Event listener for the category dropdown to fetch subcategories
document
  .getElementById("categoryDropdown")
  .addEventListener("change", function () {
    const selectedCategoryId = this.value;
    console.log(selectedCategoryId);
    fetchSubcategories(selectedCategoryId);
  });

// Event listener for the subcategory dropdown to fetch products
document
  .getElementById("subcategoryDropdown")
  .addEventListener("change", function () {
    const selectedSubcategoryId = this.value;
    console.log(selectedSubcategoryId);
    fetchProducts(selectedSubcategoryId);
  });
