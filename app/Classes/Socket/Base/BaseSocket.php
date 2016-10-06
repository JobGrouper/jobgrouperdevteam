<?php

namespace App\Classes\Socket\Base;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class BaseSocket implements MessageComponentInterface
{
    public function onOpen(ConnectionInterface $conn)
    {
        // TODO: Implement onOpen() method.
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        // TODO: Implement onMessage() method.
    }

    public function onClose(ConnectionInterface $conn)
    {
        // TODO: Implement onClose() method.
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        // TODO: Implement onError() method.
    }
}