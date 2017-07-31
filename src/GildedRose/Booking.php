<?php
/**
 * @file
 * Contains Booking.php
 *
 * PHP Version 7
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
        $sql = 'CREATE TABLE IF NOT EXISTS booking
          (id INTEGER, room INTEGER, customer INTEGER, luggage INTEGER, checkin TIMESTAMP, checkout TIMESTAMP, PRIMARY KEY(id ASC))';
        $this->dbo->exec($sql);
    }

    /**
     * Reserve a room.
     *
     * @param int $room
     * @param int $customer
     * @param int $luggage
     * @param int $checkin
     * @param int $checkout
     * @return array
     */
    public function reserve(int $room, int $customer, int $luggage, int $checkin, int $checkout): array
    {
        $roomReservations = $this->getRoomReservations($room, $checkin, $checkout);
        $thisRoom = (new Room($this->dbo))->find($room);
        foreach ($roomReservations as $reservation) {
            $thisRoom['occupants']--;
            $thisRoom['storage'] -= $reservation['luggage'];
        }
        if ($thisRoom['occupants'] <1 || $thisRoom['storage'] < $luggage) {
            return ['status' => 409, 'message' => "Room $room is not available"];
        }
        $statement = $this->dbo->prepare('INSERT INTO booking (room, customer, luggage, checkin, checkout) VALUES (?, ?, ?, ?, ?)');
        if ($statement->execute([$room, $customer, $luggage, $checkin, $checkout])) {
            return ['room' => $room, 'customer' => $customer, 'luggage' => $luggage, 'checkin' => $checkin, 'checkout' => $checkout];
        }
        return ['status' => 500, 'message' => 'Database error'];
    }

    /**
     * Get reservations for a given room that overlap the indicated time period.
     *
     * @param int $room
     * @param int $checkin
     * @param int $checkout
     * @return array
     */
    public function getRoomReservations(int $room, int $checkin, int $checkout): array
    {
        $statement = $this->dbo->prepare('SELECT customer, luggage FROM booking WHERE room = ? and ? BETWEEN checkin AND checkout OR ? BETWEEN checkin AND checkout');
        $statement->execute([$room, $checkin, $checkout]);
        if ($statement->execute([$room, $checkin, $checkout])) {
            return $statement->fetchAll(\PDO::FETCH_ASSOC);
        }
        return [];
    }

    /**
     * Get a reservation by its ID.
     * @param int $reservationId
     * @return Types\Reservation|array
     */
    public function getReservation(int $reservationId)
    {
        $statement = $this->dbo->prepare('SELECT room, customer, luggage, checkin, checkout FROM booking WHERE id = ?');
        $statement->execute([$reservationId]);
        $record = $statement->fetch(\PDO::FETCH_ASSOC);
        if ($record) {
            return new Types\Reservation($record);
        }
        return ['status' => 404, 'message' => "No reservation with ID $reservationId"];
    }

    /**
     * @param int $luggage
     * @param int $checkin
     * @param int $checkout
     * @return array[]
     */
    public function findAvailableRooms(int $luggage, int $checkin, int $checkout): array
    {
        $rooms = (new Room($this->dbo))->listAllRooms();
        $sql = 'SELECT room.id, booking.luggage FROM room JOIN booking on booking.room=room.id WHERE ? BETWEEN booking.checkin AND booking.checkout OR ? BETWEEN booking.checkin AND booking.checkout';
        $statement = $this->dbo->prepare($sql);
        $statement->execute([$checkin, $checkout]);
        while ($booking = $statement->fetch()) {
            $roomId = $booking['id'];
            $rooms[$roomId]['storage'] -= $booking['luggage'];
            $rooms[$roomId]['capacity'] --;
        }
        $availableRooms = [];
        $rules = new Rules($this->dbo);
        foreach ($rooms as $id => $room) {
            $room['id'] = $id;
            if ($rules->isAvailable($room, $luggage, $checkin)) {
                $availableRooms[] = $room;
            }
        }
        return $availableRooms;
    }

    /**
     * Find the best available room (to maximize occupancy).
     *
     * We can put guests together in a room, but they must be with their luggage. Therefore, fully
     * using the available luggage capacity provides a general estimate of the best occupancy.
     * For example, if a customer has no luggage and we put them in a room thta has storage for two
     * pieces, we may not be able to accommodate a later booking that does have luggage.
     *
     * More advanced optimization will be considered for v2 product.
     *
     * @param int $luggage
     * @param int $checkin
     * @param int $checkout
     * @return array
     */
    public function findBestAvailableRoom(int $luggage, int $checkin, int $checkout): array
    {
        $room = [];
        $availableRooms = $this->findAvailableRooms($luggage, $checkin, $checkout);
        foreach ($availableRooms as $room) {
            if ($room['luggage'] == $luggage) {
                return $room;
            }
        }
        return $room;
    }

    /**
     * @param int $roomId
     * @param int $checkin
     * @param int $checkout
     * @return int
     */
    public function cleaningTime(int $roomId, int $checkin, int $checkout): int
    {
        // are there any reservations
        $st1 = $this->dbo->prepare('SELECT id, room, luggage FROM booking WHERE ? BETWEEN checkin AND checkout OR ? BETWEEN checkin AND checkout LIMIT 1');

        $statement = $this->dbo->prepare('SELECT room, luggage, checkin, checkout');
    }

    /**
     * @param int $timestamp
     * @return string A string formatted version of a timestamp.
     */
    private function date(int $timestamp): string
    {
        return date_create()->setTimestamp($timestamp)->format('c');
    }
}
