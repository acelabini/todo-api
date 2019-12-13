<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class RepositoryInvalidQueryFormatException extends Exception
{
    protected $message = 'Invalid query parameters passed.';

    protected $code = Response::HTTP_UNPROCESSABLE_ENTITY;
}

