<?php
/**
 * @file
 * Contains HotelTest.php
 *
 * PHP Version 5
 */

namespace Tests\GildedRose;

use GildedRose\Hotel;
use GildedRose\Room;
use GildedRose\SqlStorage;
use PHPUnit\Framework\TestCase;

class HotelTest extends TestCase {

  public function testBuildHotel() {
    $hotel = new Hotel();
    $hotel->buildHotel();
    $rooms = new Room(new SqlStorage());
    $this->assertEquals(4, count($rooms->listAllRooms()));
  }
}
