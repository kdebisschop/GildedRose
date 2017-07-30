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
          (room INTEGER, name VARCHAR(32) UNIQUE, PRIMARY KEY(id ASC))';
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
        $sql = 'SELECT room, start, finish FROM schedule WHERE start BETWEEN ? AND ? AND finish BETWEEN ? and ? ORDER BY start';
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

        $sql = 'SELECT id FROM booking WHERE room = ? AND checkout BETWEEN ? AND ?';
        $statement = $this->dbo->prepare($sql);
        if ($statement->execute([$room, $cleaning, $checkin])) {
            return false;
        }

        return true;
    }

    /**
     * Rebuild cleaning schedule based on current bookings
     *
     * Not yet clear if complete rebuild will be required or if there is a practical
     * way to only rebuild a subset based on the reservation that is triggering the update.
     *
     * @todo Not Yet Implemented
     *
     * @param int $booking The reservation that triggered this rebuild.
     */
    public function rebuildSchedule(int $booking): void
    {

    }
}
