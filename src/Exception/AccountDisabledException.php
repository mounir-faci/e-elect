<?php

namespace App\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Throwable;

class AccountDisabledException extends AuthenticationException
{
    private const MESSAGE = "This Account has been Disabled !";

    public function __construct($message = self::MESSAGE, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getMessageKey()
    {
        return $this->getMessage();
    }
}

