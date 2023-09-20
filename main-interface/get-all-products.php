<?php

use MongoDB\Client;

require '../vendor/autoload.php';

$mongoUrl = 'mongodb://localhost:27017';
$dbName = 'webproject2023';

$userInput = isset($_GET['search']) ? $_GET['search'] : '';

// Function to remove diacritics (tone marks) from Greek characters
function removeDiacritics($text) {
    $transliterator = Transliterator::createFromRules(':: NFD; :: [:Nonspacing Mark:] Remove; :: NFC;', Transliterator::FORWARD);
    return $transliterator->transliterate($text);
}

// Remove diacritics from the user input
$userInputWithoutDiacritics = removeDiacritics($userInput);

$client = new MongoDB\Client($mongoUrl);
$productsCollection = $client->$dbName->products;

// Create a regular expression pattern to match names starting with or containing the user input
$regexPattern = '^' . preg_quote($userInputWithoutDiacritics);

// Query products collection using $regex to filter products by name, ignoring diacritics
$products = $productsCollection->find([
    'name' => [
        '$regex' => $regexPattern,
        '$options' => 'i',
    ],
], [
    'projection' => ['_id' => 0, 'id' => 1, 'name' => 1],
]);

$allProductData = iterator_to_array($products);

header('Content-Type: application/json');
echo json_encode($allProductData);
?>
