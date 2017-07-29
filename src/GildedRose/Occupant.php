<?php
/**
 * @file
 * Contains Occupant.php
 *
 * PHP Version 5
 */

namespace GildedRose;

/**
 * Provides methods for defining and querying customers/occupants.
 */
class Occupant
{
    /** @var \PDO Uniform data storage */
    private $dbo;

    /**
     * Occupant constructor injects uniform data storage.
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
        $this->dbo->exec('CREATE TABLE customer (id INTEGER, name VARCHAR (64), PRIMARY KEY(id ASC ))');
    }

    public function newCustomer()
    {

    }
}
