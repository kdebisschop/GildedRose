<?php
/**
 * @file
 * Contains Hotel.php
 *
 * PHP Version 5
 */

namespace GildedRose;

class Hotel extends HotelObject
{
    /**
     * Builds a representation of the Gilded Rose in sqlite.
     *
     * @todo ensure this is protected against being run multiple times.
     */
    public function buildHotel()
    {
        // Define tables.
        (new Booking($this->dbo))->initSchema();
        (new Occupant($this->dbo))->initSchema();
        $roomBuilder = new Room($this->dbo);

        // Build 4 initial rooms
        $roomBuilder->initSchema();
        $roomBuilder->buildNewRoom(1, 'Beren', 2, 1);
        $roomBuilder->buildNewRoom(2, 'Garrick', 2, 0);
        $roomBuilder->buildNewRoom(3, 'Ningel', 1, 2);
        $roomBuilder->buildNewRoom(4, 'Turen', 1, 0);
    }
}
