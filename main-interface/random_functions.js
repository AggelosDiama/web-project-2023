// Get the avatar icon and the dropdown menu
const avatarIcon = document.getElementById('avatar-icon');
const dropdownMenu = document.querySelector('.dropdown-content');

// Add a click event listener to the avatar icon
avatarIcon.addEventListener('click', function () {
  // Toggle the visibility of the dropdown menu
  if (dropdownMenu.style.display === 'block') {
    dropdownMenu.style.display = 'none';
  } else {
    dropdownMenu.style.display = 'block';
  }
});

// Close the dropdown menu if the user clicks outside of it
window.addEventListener('click', function (event) {
  if (!event.target.matches('#avatar-icon')) {
    if (dropdownMenu.style.display === 'block') {
      dropdownMenu.style.display = 'none';
    }
  }
});

const testButton = document.getElementById('test');
testButton.addEventListener('click', () => {
  const successCallback = (position) => {
    console.log(position);
  };
  
  const errorCallback = (error) => {
    console.log(error);
  };
  
  navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
})


