<?php
/**
 * @file
 * Contains Room.php
 *
 * PHP Version 7
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
        $sql = 'CREATE TABLE  IF NOT EXISTS room
          (id INTEGER, name VARCHAR(32) UNIQUE, occupants INTEGER, storage INTEGER, PRIMARY KEY(id ASC))';
        $this->dbo->exec($sql);
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
        $rooms = $this->dbo->query('SELECT id, name, occupants, storage FROM room', \PDO::FETCH_ASSOC);
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
     * Get characteristics of a room.
     *
     * @param int $roomId Room number.
     * @return array Room description (id, name, maximum # of occupants, and luggage capacity).
     */
    public function find(int $roomId): array
    {
        $statement = $this->dbo->prepare('SELECT id, name, occupants, storage FROM room WHERE id = ?');
        if ($statement->execute([$roomId])) {
            $record = $statement->fetch(\PDO::FETCH_ASSOC);
            if ($record) {
                return $record;
            }
        }
        return ['occupants' => 0, 'storage' => 0];
    }

    /**
     * Returns worst-case cleaning time for a room, based on full occupancy.
     *
     * @param int $roomNumber
     * @return float
     */
    public function cleaningTime(int $roomNumber): float
    {
        $room = $this->find($roomNumber);
        return 1.0 + 0.5 * $room['occupants'];
    }
}
