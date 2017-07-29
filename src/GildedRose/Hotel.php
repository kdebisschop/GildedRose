<?php
/**
 * @file
 * Contains Hotel.php
 *
 * PHP Version 5
 */

namespace GildedRose;

class Hotel
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
     * Builds a representation of the Gilded Rose in sqlite */
    public function buildHotel()
    {
        // Define tables.
        (new Booking($this->dbo))->initSchema();
        (new Occupant($this->dbo))->initSchema();
        $this->dbo->exec('CREATE TABLE customer (id INTEGER, name VARCHAR (64), PRIMARY KEY(id ASC ))');


        // Build 4 initial rooms
        $roomBuilder = new Room($this->dbo);
        $roomBuilder->initSchema();
        $roomBuilder->buildNewRoom(1, 'Beren', 2, 1);
        $roomBuilder->buildNewRoom(2, 'Garrick', 2, 0);
        $roomBuilder->buildNewRoom(3, 'Ningel', 1, 2);
        $roomBuilder->buildNewRoom(4, 'Turen', 1, 0);
        $roomBuilder->listAllRooms();
    }
}
