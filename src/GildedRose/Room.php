<?php
/**
 * @file
 * Contains Room.php
 *
 * PHP Version 5
 */

namespace GildedRose;

/**
 * @property \PDO dbo
 */
class Room extends HotelObject
{
    /**
     * Initialize data storage.
     */
    public function initSchema(): void
    {
        $this->dbo->exec('CREATE TABLE room (id INTEGER, name VARCHAR(32) UNIQUE, occupants INTEGER, storage INTEGER, PRIMARY KEY(id ASC))');
    }

    /**
     * @param int $number The room number
     * @param string $name
     * @param int $capacity
     * @param int $storage
     *
     * @return bool
     */
    public function buildNewRoom(int $number, string $name, int $capacity, int $storage): bool
    {
        $statement = $this->dbo->prepare('INSERT INTO room (id, name, occupants, storage) VALUES(?, ?, ?, ?)');
        return $statement->execute([$number, $name, $capacity, $storage]);
    }

    /**
     * List all rooms in the hotel, regardless of occupancy.
     * @return array
     */
    public function listAllRooms(): array
    {
        $result = [];
        $rooms = $this->dbo->query('SELECT id, name , occupants, storage FROM room', \PDO::FETCH_ASSOC);
        foreach ($rooms as $room) {
            $result[$room['id']] = [
                'name' => $room['name'],
                'capacity' => $room['occupants'],
                'storage' => $room['storage'],
            ];
        }
        return $result;
    }

    /**
     * List rooms in the hotel that can accommodate customers.
     * @param int $checkin
     * @param int $checkout
     * @return array
     */
    public function listVacancies(int $checkin, int $checkout): array
    {
        $result = [];
        $rooms = $this->dbo->query('SELECT id, name , occupants, storage FROM room', \PDO::FETCH_ASSOC);
        foreach ($rooms as $room) {
            $result[$room['id']] = [
                'name' => $room['name'],
                'capacity' => $room['occupants'],
                'storage' => $room['storage'],
            ];
        }
        return $result;
    }

}
