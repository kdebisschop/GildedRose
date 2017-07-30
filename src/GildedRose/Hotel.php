<?php
/**
 * @file
 * Contains Hotel.php
 *
 * PHP Version 7
 */

namespace GildedRose;

/**
 * Provides initialization for the inn specified in the prompt.
 */
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

        // Build 4 initial rooms
        $roomBuilder = new Room($this->dbo);
        $roomBuilder->initSchema();
        $roomBuilder->buildNewRoom(1, 'Lantan', 2, 1);
        $roomBuilder->buildNewRoom(2, 'Elturgard', 2, 0);
        $roomBuilder->buildNewRoom(3, 'Blingdenstone', 1, 2);
        $roomBuilder->buildNewRoom(4, 'Luruar', 1, 0);

        // Hire one cleaning team
        $cleaners = new Cleaners($this->dbo);
        $cleaners->initSchema();
        $cleaners->addCleaningTeam('Scheppen');
    }
}
