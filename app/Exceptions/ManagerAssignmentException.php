<?php

namespace App\Exceptions;

class ManagerAssignmentException extends BusinessException
{
    public static function conflict(string $message = 'Manager assignment conflict'): self
    {
        return new self($message);
    }
}
