<?php

namespace Bpa\Notifications\Handler;

use Bpa\Notifications\Notification\RoomInterface;

/**
 * Room for Stride
 */
class StrideRoom implements RoomInterface
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * @var string
     */
    private $name;

    /**
     * StrideRoom constructor.
     *
     * @param string $identifier
     * @param string $name
     */
    public function __construct($identifier, $name)
    {
        $this->identifier = $identifier;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
