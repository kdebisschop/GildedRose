<?php
/**
 * @file
 * Contains BookingTest.php
 *
 * PHP Version 5
 */

namespace GildedRose;

use PHPUnit\Framework\TestCase;

/**
 * Provides tests for room reservation.
 */
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

    /**
     * Get storage object.
     *
     * @todo Replace with mock object or dummy Sqlite instance to make true unit test.
     */
    public function setUp()
    {
        $this->dbo = new SqlStorage();
    }

    public function testReservation()
    {
        $booking = new Booking($this->dbo);
        $this->dbo->exec('DELETE FROM booking');
        $now = new \DateTime();
        $now->add(new \DateInterval('P1D'));

        // first reservation succeeds
        $result = $booking->reserve(1, 1, 1, time(), $now->getTimestamp());
        $this->assertEquals(1, $result['room'], print_r($result, 1));

        // second should fail
        $result = $booking->reserve(1, 1, 1, time(), $now->getTimestamp());
        $this->assertEquals(409, $result['status'], print_r($result, 1));

        $reservation = $booking->getReservation(1);
        $this->assertEquals(1, $reservation->room);
    }

    public function testFindAvailableRooms()
    {
        $booking = new Booking($this->dbo);
        $now = new \DateTime();
        $now->add(new \DateInterval('P1D'));
        $available = $booking->findAvailableRooms(1, time(), $now->getTimestamp());
        $this->assertCount(1, $available, print_r($available, true));
        $this->assertEquals(3, $available[0]['id']);
        $this->assertEquals(1, $available[0]['capacity']);
        $this->assertEquals(2, $available[0]['storage']);
    }
}
