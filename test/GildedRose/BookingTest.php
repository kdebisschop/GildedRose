<?php
/**
 * @file
 * Contains BookingTest.php
 *
 * PHP Version 5
 */

namespace GildedRose;

use PHPUnit\Framework\TestCase;

class BookingTest extends TestCase
{
    /** @var \PDO data storage */
    private $dbo;

    /**
     * Ensures Hotel exists in SqlStorage.
     */
    public static function setUpBeforeClass()
    {
        (new Hotel(new SqlStorage()))->buildHotel();
    }

    public function setUp()
    {
        $this->dbo = new SqlStorage();
    }

    public function testReservation()
    {
        $booking = new Booking($this->dbo);
        $now = new \DateTime();
        $now->add(new \DateInterval('P1D'));
        $booking->reserve(1, 1, 1, time(), $now->getTimestamp());

    }
}
