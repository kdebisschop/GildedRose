<?php
/**
 * @file
 * Contains Config.php
 *
 * PHP Version 7
 */

namespace GildedRose;

/**
 * Provides configuration information.
 *
 * Currently hard-coding values in this class. We wil ultimately want
 * to consult with Allison and our customer service team to decide
 * on a better storage mechanism. Database seems best, but could be
 * prone to hackers if there is an exposed URL. YML files are another
 * option.
 *
 * @todo Decide on and implement configuration storage.
 */
class Config implements \ArrayAccess
{
    /** @var array Interim storage for settings variables */
    private $settings = [
        'time_zone' => 'America/New_York',
    ];

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->settings);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->settings[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @throws \RuntimeException
     */
    public function offsetSet($offset, $value): void
    {
        throw new \RuntimeException('Saving configuration entries is not yet supported');
    }

    /**
     * @param mixed $offset
     * @throws \RuntimeException
     */
    public function offsetUnset($offset): void
    {
        throw new \RuntimeException("Configuration entity '$offset' cannot be removed");
    }
}
