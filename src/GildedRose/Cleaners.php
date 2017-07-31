<?php
/**
 * @file
 * Contains Cleaners.php
 *
 * PHP Version 7
 */

namespace GildedRose;

/**
 * Provides representation of clening gnomes and their schedule.
 */
class Cleaners extends HotelObject
{
    public function initSchema(): void
    {
        $sql = 'CREATE TABLE IF NOT EXISTS cleaners
          (id INTEGER, name VARCHAR(32) UNIQUE, PRIMARY KEY(id ASC))';
        $this->dbo->exec($sql);
        $sql = 'CREATE TABLE IF NOT EXISTS schedule
          (id INTEGER, room INTEGER, crew INTEGER, start TIMESTAMP, finish TIMESTAMP, PRIMARY KEY(id ASC))';
        $this->dbo->exec($sql);
    }

    /**
     * Adds a cleaning team.
     * @param string $name
     * @return bool
     */
    public function addCleaningTeam(string $name): bool
    {
        $statement = $this->dbo->prepare('INSERT INTO cleaners (name) VALUES(?)');
        return $statement->execute([$name]);
    }

    /**
     * Gets room cleaning schedule from schedule table.
     *
     * @param string $localDate
     * @return array
     */
    public function getSchedule(string $localDate): array
    {
        $date = new \DateTime($localDate);
        $start = $date->getTimestamp();
        $date->add(new \DateInterval('P1D'));
        $finish = $date->getTimestamp();
        $sql = 'SELECT room, start, finish FROM schedule
          WHERE start BETWEEN ? AND ? AND finish BETWEEN ? and ? ORDER BY start';
        $statement = $this->dbo->prepare($sql);
        $statement->execute([$start, $finish, $start, $finish]);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     *
     * return true if there is a cleaning before $checkin and there are no $checkouts between
     * the cleaning and the $checkin.
     *
     * @param int $room The room number/id.
     * @param int $checkin Timestamp of the checkin.
     * @return bool
     */
    public function hasBeenCleaned(int $room, int $checkin): bool
    {
        $sql = 'SELECT MAX(finish) FROM schedule WHERE room = ? AND finish < ?';
        $statement = $this->dbo->prepare($sql);
        if ($statement->execute([$room, $checkin])) {
            $cleaning = $statement->fetchColumn();
        } else {
            $cleaning = 0;
        }

        $sql = 'SELECT id FROM booking WHERE room = ? AND checkout BETWEEN ? AND ? LIMIT 1';
        $statement = $this->dbo->prepare($sql);
        if ($statement->execute([$room, $cleaning, $checkin])) {
            if ($statement->fetchColumn()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Rebuild cleaning schedule based on current bookings
     *
     * Not yet clear if complete rebuild will be required or if there is a practical
     * way to only rebuild a subset based on the reservation that is triggering the update.
     *
     * For each day, assume cleaning shift runs more or less normal business hours - in
     * particular, require that no shift runs overnight. Some trick points:
     *
     *  - There should be a reasonable rest period for the gnomes between shifts. Currently the law
     *    allows them to work 16 continuous hours from 4 pm one day to 8 am the next. But we don't
     *    want that even if the law currently allows it.
     *  - Although the query is by day, we need to ensure we catch any rooms that were vacated
     *    the previous day but not cleaned because the checkout time was out of shift.
     *
     * @todo Handle case where two customer are booked into room with potentially different checkout times.
     *
     * @param int $booking The reservation that triggered this rebuild.
     */
    public function rebuildSchedule(int $booking): void
    {
        $gracePeriod = 0; // potentially allow a grace period before cleaners arrive
        $reservation = (new Booking($this->dbo))->getReservation($booking);
        $timezone = (new Config())->offsetGet('time_zone');
        $start = new \DateTime();
        $start->setTimezone(new \DateTimeZone($timezone));
        $start->setTimestamp($reservation->checkout);
        $start->setTime(0, 0);
        $end = clone $start;
        $end->add(new \DateInterval('P1D'));

        $this->resetDailyCleaning($start->getTimestamp(), $end->getTimestamp());

        // Get all reservations leaving this day
        $sql = 'SELECT * FROM booking WHERE checkout BETWEEN ? AND ? ORDER BY checkout';
        $statement = $this->dbo->prepare($sql);
        $statement->execute([$start->getTimestamp(), $end->getTimestamp()]);

        $cleaningStart = 0;
        $end = 0;
        while ($booking = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $reservation = new Types\Reservation($booking);

            // Allow some time for late checkouts.
            if ($cleaningStart === 0) {
                $cleaningStart = $reservation->checkout + $gracePeriod;
            }

            // Ensure the crew has completed their last room.
            if ($end > $cleaningStart) {
                $cleaningStart = $end;
            }

            // Ensure nobody else is still booked into the room.
            if ($this->hasOtherBookings($reservation->room, $cleaningStart)) {
                continue;
            }
            $end = $this->scheduleCleaning($reservation->room, $cleaningStart);
        }
    }

    /**
     * @todo this query needs to protect against times near midnight
     *
     * @param int $startOfDay
     * @param int $endOfDay
     */
    private function resetDailyCleaning(int $startOfDay, int $endOfDay)
    {
        $sql = 'DELETE FROM schedule WHERE start > ? and finish < ?';
        $this->dbo->prepare($sql)->execute([$startOfDay, $endOfDay]);
    }

    /**
     * @todo Fix DST issue in time calculation
     * @todo Use real occupancy instead of worst-case to schedule
     *
     * @param int $room
     * @param int $start
     * @return int Timestamp for end of this cleaning shift
     */
    private function scheduleCleaning(int $room, int $start): int
    {
        $end = $start + 3600 * (new Room($this->dbo))->cleaningTime($room);
        $crew = $this->getCrew($start, $end);
        $statement = $this->dbo->prepare('INSERT INTO schedule (room, crew, start, finish) VALUES (?, ?, ?, ?)');
        $statement->execute([$room, $crew, $start, $end]);
        return $end;
    }

    /**
     * @param int $room
     * @param int $time
     * @return bool
     */
    private function hasOtherBookings(int $room, int $time): bool
    {
        $sql = 'SELECT room FROM booking WHERE room <> ? AND ? BETWEEN checkin AND checkout LIMIT 1';
        $statement = $this->dbo->prepare($sql);
        $statement->execute([$room, $time]);
        if ($statement->fetch()) {
            return true;
        }
        return false;
    }
    /**
     * Get next available cleaning crew.
     *
     * @todo return 0 if no crew is available;
     *
     * @param int $start
     * @param int $finish
     * @return int
     */
    private function getCrew(int $start, int $finish): int
    {
        // SELECT crew FROM cleaning WHERE <today>

        // SELECT MIN(start) from cleanin

        // ensure less than 8 hours.

        return 1;
    }
}
