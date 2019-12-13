<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class RepositoryModelAttributeNotFoundException extends Exception
{
    protected $message = 'Tried to search for invalid record attribute.';

    protected $code = Response::HTTP_UNPROCESSABLE_ENTITY;
}

