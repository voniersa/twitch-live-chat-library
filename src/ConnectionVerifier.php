<?php

declare(strict_types=1);

namespace voniersa\twitch\livechat;

/** @codeCoverageIgnore */
class ConnectionVerifier
{
    /**
     * @param mixed $resource stream resource
     * @return bool indicator if connection is valid or not
     */
    public function verifyConnection(mixed $resource): bool
    {
        return is_resource($resource) && get_resource_type($resource) === "stream";
    }
}
