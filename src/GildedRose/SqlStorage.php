<?php
/**
 * @file
 * Contains Sqlite.php
 *
 * PHP Version 7
 */

namespace GildedRose;

/**
 * Provides a single point of access for database storage.
 */
class SqlStorage extends \PDO
{
    /**
     * SqlStorage constructor creates a PDO object.
     */
    public function __construct()
    {
        $dbFile = dirname(__DIR__, 2) . '/var/GildedRose.sq3';
        $dsn = 'sqlite:' . $dbFile;
        $username = '';
        $passwd = '';
        $options = [];
        parent::__construct($dsn, $username, $passwd, $options);
    }
}
