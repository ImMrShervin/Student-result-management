<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class EnrollmentException extends Exception
{
    public function render($request)
    {
        return response()->json([
            'message' => $this->getMessage(),
            'error' => 'enrollment_error',
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
