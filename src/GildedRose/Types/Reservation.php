<?php
/**
 * @file
 * Contains Reservation.php
 *
 * PHP Version 5
 */

namespace GildedRose\Types;

use GildedRose\Config;

class Reservation
{
    public $room;
    public $customer;
    public $luggage;
    public $checkin;
    public $checkout;
    public $arrives;
    public $departs;

    public function __construct(array $reservation)
    {
        $this->room = $reservation['room'];
        $this->customer = $reservation['customer'];
        $this->luggage = $reservation['luggage'];
        $this->checkin = $reservation['checkin'];
        $this->checkout = $reservation['checkout'];

        $timezone = new \DateTimeZone((new Config())->offsetGet('time_zone'));
        $this->arrives = date_create($this->checkin)->setTimezone($timezone)->format('c');
        $this->departs = date_create($this->checkout)->setTimezone($timezone)->format('c');
    }
}
