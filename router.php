<?php
/**
 * @file
 * Contains router.php
 *
 * PHP Version 5
 */

$request = $_SERVER['REQUEST_URI'];

if (preg_match('/\.(?:png|jpg|jpeg|gif)$/', $request)) {
  return false;
}

// Set up namespace autoloader
require_once __DIR__ . '/bootstrap.php';

// Initialize uniform storage
$dbo = new GildedRose\SqlStorage();

// Route request
switch ($request) {
  case '/initialize':
    $hotel = new GildedRose\Hotel($dbo);
    $hotel->buildHotel();
    echo json_encode(['status' => 200, 'message' => 'OK']);
    break;

  case '/rooms/listAll':
    echo json_encode((new GildedRose\Room($dbo))->listAllRooms());
    break;

  case '/customer/create':
    echo json_encode((new GildedRose\Occupant($dbo))->newCustomer());

  default:
    echo json_encode(['status' => 404, 'message' => "No route for '$request'"]);
}
