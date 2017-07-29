<?php
/**
 * @file
 * Contains Booking.php
 *
 * PHP Version 5
 */

namespace GildedRose;


class Booking
{

    private $dbo;

    /**
     * Hotel constructor injects data storage.
     *
     * @param \PDO $dbo
     */
    public function __construct(\PDO $dbo)
    {
        $this->dbo = $dbo;
    }

    /**
     * Initialize data storage.
     */
    public function initSchema(): void
    {
        $this->dbo->exec('CREATE TABLE booking (id INTEGER, integer customer, checkin TIMESTAMP, checkout TIMESTAMP, PRIMARY KEY(id ASC))');
    }

    public function request(int $occupants, int $storage, int $checkin, int $checkout)
    {

    }
}
