<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\JWTAuth;

class AuthController extends ApiController
{
    protected $userRepository;

    protected $auth;

    public function __construct(UserRepository $userRepository, JWTAuth $auth)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->auth = $auth;
    }

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register",
     *     description="Register a user",
     *     tags={"USER"},
     *     @OA\RequestBody(
     *         description="JSON Payload",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="name",
     *                 description="User fullname",
     *                 type="string",
     *                 example="Api Dev",
     *             ),
     *             @OA\Property(
     *                 property="email",
     *                 description="Unique email address",
     *                 type="string",
     *                 example="todo@list.com",
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 description="User password",
     *                 type="string",
     *                 example="",
     *             ),
     *             @OA\Property(
     *                 property="password_confirmation",
     *                 description="Confirm user password",
     *                 type="string",
     *                 example="",
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=Symfony\Component\HttpFoundation\Response::HTTP_OK,
     *         description="User creation successful.",
     *         @OA\JsonContent(
     *             example={
     *                 "data": {
     *                      "id": 1,
     *                      "name": "Api Dev",
     *                      "email": "todo@list.com",
     *                      "created_at": "2019-12-13 16:33:18"
     *                  }
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
     *         description="Invalid request parameters.",
     *         @OA\JsonContent(
     *             example={
     *                  "application/json": {
     *                      "error": {
     *                          "code": 422,
     *                          "message": "The given data was invalid."
     *                      },
     *                      "errors": {
     *                          "email": {"The email field is required."}
     *                      }
     *                  }
     *             }
     *         )
     *     ),
     * )
     *
     * Register post user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        return $this->runWithExceptionHandling(function () use ($request) {
            $this->validate($request, [
                'name'      => 'required|string',
                'email'     => 'required|email|min:4|unique:users',
                'password'  => 'required|confirmed',
            ]);

            $user = $this->userRepository->create([
                'name'      =>  $request->post('name'),
                'email'     =>  $request->post('email'),
                'password'  =>  Hash::make($request->post('password'))
            ]);

            $this->response->setData(['data' => new UserResource($user)]);
        });
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login",
     *     description="Login user",
     *     tags={"USER"},
     *     @OA\RequestBody(
     *         description="JSON Payload",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="email",
     *                 description="User email address",
     *                 type="string",
     *                 example="todo@list.com",
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 description="User password",
     *                 type="string",
     *                 example="",
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=Symfony\Component\HttpFoundation\Response::HTTP_OK,
     *         description="User creation successful.",
     *         @OA\JsonContent(
     *             example={
     *                   "data": {
     *                      "success": true,
     *                      "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9hcGkudG9kby50ZXN0XC9hcGlcL2xvZ2luIiwiaWF0IjoxNTc2MjI1NjM1LCJleHAiOjE2MDc3NjE2MzUsIm5iZiI6MTU3NjIyNTYzNSwianRpIjoiWTRHSlYxSEpYa21UaTdYbCIsInN1YiI6MTksInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.-LUG9Yqtat3ZR0EJ0PIs6ExObFdLBfGi3pQJ_lckw3Q",
     *                      "user": {
     *                              "id": 19,
     *                              "name": "Api Dev",
     *                              "email": "ace@list.com",
     *                              "created_at": "2019-12-13 08:19:59",
     *                              "updated_at": "2019-12-13 08:19:59",
     *                              "deleted_at": null
     *                          }
     *                      }
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
     *         description="Invalid request parameters.",
     *         @OA\JsonContent(
     *             example={
     *                  "application/json": {
     *                      "error": {
     *                          "code": 422,
     *                          "message": "The given data was invalid."
     *                      },
     *                      "errors": {
     *                          "email": {"The email field is required."}
     *                      }
     *                  }
     *             }
     *         )
     *     ),
     * )
     *
     * Register post user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        return $this->runWithExceptionHandling(function () use ($request) {
            $this->validate($request, [
                'email'     =>  'required',
                'password'  =>  'required'
            ]);
            $token = $this->auth->attempt($request->only('email', 'password'));

            $this->response->setData(['data' => [
                'success'   =>  $token ? true : false,
                'token'     =>  $token,
                'user'      =>  $this->auth->user()
            ]]);
        });
    }
}
