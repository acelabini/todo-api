<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use Exception;
use Illuminate\Http\Response;
use InvalidArgumentException;
use App\Exceptions\ApiException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class ApiController extends Controller
{
    protected $response;

    public function __construct()
    {
        $this->response = new ApiResponse();
    }

    public function runWithExceptionHandling($callback)
    {
        try {
            $callback();
            return response()->json($this->response->getData());
        } catch (ApiException $e) {
            throw $e;
        } catch(ValidationException | InvalidArgumentException $e) {
            throw new ApiException(
                $e->getMessage(),
                $e->getResponse() ? $e->getResponse()->getStatusCode() : Response::HTTP_UNPROCESSABLE_ENTITY,
                $e->errors()
            );
        } catch(ModelNotFoundException $e) {
            throw new ApiException('Record not found.', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            throw new ApiException($e->getMessage(), $e->getCode());
        }
    }
}
