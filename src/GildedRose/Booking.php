<?php
/**
 * @file
 * Contains Booking.php
 *
 * PHP Version 5
 */

namespace GildedRose;

/**
 * Provides a mechanism for booking a customer into a room for a predetermined time period.
 */
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
          (id INTEGER, room INTEGER, customer INTEGER, luggage INTEGER, checkin TIMESTAMP, checkout TIMESTAMP, PRIMARY KEY(id ASC))';
        $this->dbo->exec($sql);
    }

    public function request(int $occupants, int $storage, int $checkin, int $checkout)
    {

    }

    public function reserve(int $room, int $customer, int $luggage, int $checkin, int $checkout)
    {
        $statement = $this->dbo->prepare('INSERT INTO booking (room, customer, luggage, checkin, checkout) VALUES (?, ?, ?, ?, ?)');
        return $statement->execute([$room, $customer, $luggage, $checkin, $checkout]);
    }

    public function getReservation(int $id) {
        $statement = $this->dbo->prepare('SELECT room, customer, luggage, checkin, checkout FROM booking WHERE id = ?');
        $statement->execute([$id]);
        return $statement->fetch(\PDO::FETCH_ASSOC);
    }
}
