<?php
/**
 * @file
 * Contains Occupant.php
 *
 * PHP Version 7
 */

namespace GildedRose;

/**
 * Provides methods for defining and querying customers/occupants.
 */
class Occupant extends HotelObject
{
    /**
     * Initialize data storage.
     *
     * {@internal this data definition assumes auto-incrementing primary key from Sqlite}
     */
    public function initSchema(): void
    {
        $sql = 'CREATE TABLE IF NOT EXISTS customer
          (id INTEGER, name VARCHAR(64), email VARCHAR(256), PRIMARY KEY(id ASC ))';
        $this->dbo->exec($sql);
    }

    /**
     * Adds a new customer.
     *
     * @param string $name
     * @param string $email
     */
    public function newCustomer(string $name, string $email)
    {
        $statement = $this->dbo->prepare('INSERT INTO customer (name, email) VALUES (?, ?)');
        $statement->execute([$name, $email]);
    }

    /**
     * Gets a customer based on their ID.
     * @param int $customerId
     * @return mixed
     */
    public function getCustomer(int $customerId)
    {
        $statement = $this->dbo->prepare('SELECT name, email FROM customer WHERE id = ?');
        $statement->execute([$customerId]);
        return $statement->fetch(\PDO::FETCH_ASSOC);
    }
}
