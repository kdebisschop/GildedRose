<?php
/**
 * @file
 * Contains Booking.php
 *
 * PHP Version 5
 */

namespace GildedRose;


class Booking extends HotelObject
{
    /**
     * Initialize data storage.
     *
     * {@internal this data definition assumes auto-incrementing primary key from Sqlite}
     */
    public function initSchema(): void
    {
        $sql = 'CREATE TABLE booking
          (id INTEGER, customer INTEGER, room INTEGER, checkin TIMESTAMP, checkout TIMESTAMP, PRIMARY KEY(id ASC))';
        $this->dbo->exec($sql);
    }

    public function request(int $occupants, int $storage, int $checkin, int $checkout)
    {

    }
}
