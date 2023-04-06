<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;

class IpAddressAccessControl
{
    private $allowedIps;

    public function __construct($allowedIps)
    {
        $this->allowedIps = $allowedIps;
    }

    public function checkIpAddress($ipAddress)
    {
        if (!in_array($ipAddress, $this->allowedIps)) {
            return false;
        }

        return true;
    }
}