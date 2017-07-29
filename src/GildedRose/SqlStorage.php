<?php
/**
 * @file
 * Contains Sqlite.php
 *
 * PHP Version 5
 */

namespace GildedRose;

class SqlStorage extends \PDO
{
    public function __construct()
    {
        $dsn = 'sqlite:' . __DIR__ . 'GildedRose.sq3';
        $username = '';
        $passwd = '';
        $options = [];
        parent::__construct($dsn, $username, $passwd, $options);
    }
}
