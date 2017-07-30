<?php
/**
 * @file
 * Contains Rules.php
 *
 * PHP Version 7
 */

namespace GildedRose;

/**
 * Provides rules for determining room availability and cleaning crew availability.
 */
class Rules extends HotelObject
{
    /**
     * Determine if a room is available for booking.
     *
     * @todo Get cleaning tine instead of relying on checkout time
     *
     * @param array $room
     * @param int $luggage
     * @param int $checkin
     * @return bool
     */
    public function isAvailable (array $room, int $luggage, int $checkin): bool
    {
        $clean = new Cleaners($this->dbo);
        if (!$clean->hasBeenCleaned($room['id'], $checkin)) {
            return false;
        }

        return $room['capacity'] > 0 && $luggage <= $room['storage'];
    }
}
