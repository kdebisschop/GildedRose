<?php
/**
 * @file
 * Contains HotelTest.php
 *
 * PHP Version 5
 */

namespace GildedRose;

use PHPUnit\Framework\TestCase;

/**
 * Provides unit tests for initializing the Hotel.
 */
class HotelTest extends TestCase
{
    /**
     * Hotel is built with 4 initial rooms.
     */
    public function testBuildHotel()
    {
        $dbo = new SqlStorage();
        $hotel = new Hotel($dbo);
        $hotel->buildHotel();
        $rooms = new Room($dbo);
        $this->assertCount(4, $rooms->listAllRooms());
    }
}
