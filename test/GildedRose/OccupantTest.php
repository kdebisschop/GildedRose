<?php
/**
 * @file
 * Contains OccupantTest.php
 *
 * PHP Version 5
 */

namespace GildedRose;

use PHPUnit\Framework\TestCase;

/**
 * Provides unit tests for Occupants.
 */
class OccupantTest extends TestCase
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

    public function testNewCustomer()
    {
        $customer = new Occupant($this->dbo);
        $customer->newCustomer('Gimli', 'gimli@example.com');
        $gimli = $customer->getCustomer(1);
        $this->assertNotEmpty($gimli);
        $this->assertEquals('Gimli', $gimli['name']);
    }
}
