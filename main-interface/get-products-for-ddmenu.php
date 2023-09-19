<?php

use MongoDB\Client;

require '../vendor/autoload.php';

$mongoUrl = 'mongodb://localhost:27017';
$dbName = 'webproject2023';

$subcategoryId= isset($_GET['selectedSubcategoryId']) ? $_GET['selectedSubcategoryId'] : ''; // Subcategory ID parameter
//echo $subcategoryId;
//$subcategoryId='08f280dee57c4b679be0102a8ba1343b';

$client = new MongoDB\Client($mongoUrl);
$productsCollection = $client->$dbName->products;

// Find all products that match the specified subcategory ID
$products = $productsCollection->find(['subcategory' => (string)$subcategoryId], ['projection' => ['_id' => 0, 'id' => 1, 'name' => 1]]);


$productData = iterator_to_array($products);

header('Content-Type: application/json');
echo json_encode($productData);
?>
