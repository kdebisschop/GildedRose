<?php
/**
 * @file
 * Contains router.php
 *
 * PHP Version 5
 */

// Set up namespace autoloader
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/vendor/autoload.php';

$request = Symfony\Component\HttpFoundation\Request::createFromGlobals();
$requestUri = $request->getScriptName();

if (preg_match('/\.(?:png|jpg|jpeg|gif)$/', $requestUri)) {
    return false;
}

// Initialize uniform storage
$dbo = new GildedRose\SqlStorage();

// Route request
switch ($requestUri) {
    case '/rooms/available':
        $luggage = $request->query->get('luggage');
        $checkin = date_create($request->query->get('checkin'))->getTimestamp();
        $checkout = date_create($request->query->get('checkout'))->getTimestamp();
        return json_encode((new GildedRose\Booking($dbo))->findAvailableRooms($luggage, $checkin, $checkout));
        break;

    case '/rooms/best-available':
        $luggage = $request->query->get('luggage');
        $checkin = date_create($request->query->get('checkin'))->getTimestamp();
        $checkout = date_create($request->query->get('checkout'))->getTimestamp();
        return json_encode((new GildedRose\Booking($dbo))->findBestAvailableRoom($luggage, $checkin, $checkout));
        break;

    // @todo use request->request after v1
    case '/rooms/reserve':
        $room = $request->get('room');
        $customer = $request->get('customer');
        $luggage = $request->get('luggage');
        $checkin = date_create($request->get('checkin'))->getTimestamp();
        $checkout = date_create($request->get('checkout'))->getTimestamp();
        echo json_encode((new GildedRose\Booking($dbo))->reserve($room, $customer, $luggage, $checkin, $checkout));
        break;

    case '/cleaners/schedule':
        $date = date_create($request->query->get('date'));
        echo json_encode((new GildedRose\Cleaners($dbo))->getSchedule($date));
        break;

    // @todo use request->request after v1
    case '/customer/create':
        $name = $request->get('name');
        $email = $request->get('email');
        echo json_encode((new GildedRose\Occupant($dbo))->newCustomer($name, $email));
        break;

    case '/rooms/listAll':
        echo json_encode((new GildedRose\Room($dbo))->listAllRooms());
        break;

    case '/customer/find':
        $id = $request->query->get('id');
        echo json_encode((new GildedRose\Occupant($dbo))->getCustomer($id));
        break;

    case '/reservation/find':
        $id = $request->query->get('id');
        echo json_encode((new GildedRose\Booking($dbo))->getReservation($id));
        break;

    case '/admin/initialize':
        $hotel = new GildedRose\Hotel($dbo);
        $hotel->buildHotel();
        echo json_encode(['status' => 200, 'message' => 'OK']);
        break;

    default:
        echo json_encode(['status' => 404, 'message' => "No route for '$requestUri'"]);
}
