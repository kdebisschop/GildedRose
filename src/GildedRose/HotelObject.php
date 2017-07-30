<?php
/**
 * @file
 * Contains HotelObject.php
 *
 * PHP Version 7
 */

namespace GildedRose;

/**
 * Provides common features of HotelObjects.
 */
class HotelObject
{
    /** @var \PDO  */
    protected $dbo;

    /**
     * HotelObject constructor injects data storage.
     *
     * @param \PDO $dbo
     */
    public function __construct(\PDO $dbo)
    {
        $this->dbo = $dbo;
    }
}
