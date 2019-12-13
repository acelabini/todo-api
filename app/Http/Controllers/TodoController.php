<?php

namespace App\Http\Controllers;

use App\Http\Resources\TodoCollection;
use App\Http\Resources\TodoResource;
use App\Repositories\TodoRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoController extends ApiController
{
    protected $userRepository;

    protected $todoRepository;

    public function __construct(UserRepository $userRepository, TodoRepository $todoRepository)
    {
        parent::__construct();
        $this->middleware('auth');
        $this->userRepository = $userRepository;
        $this->todoRepository = $todoRepository;
    }


    /**
     * @OA\Get(
     *     path="/api/todo",
     *     summary="Get all todo of a user",
     *     description="Fetch all available todo list.",
     *     tags={"TODO"},
     *     security={
     *         { "bearer": {}}
     *     },
     *     @OA\Response(
     *         response=Symfony\Component\HttpFoundation\Response::HTTP_OK,
     *         description="",
     *         @OA\JsonContent(
     *             example={
     *                     "data": {
     *                          {
     *                              {
     *                                  "id": 1,
     *                                  "title": "test",
     *                                  "description": "asdads",
     *                                  "is_done": "asdads",
     *                                  "created_at": null
     *                              },
     *                              {
     *                                  "id": 2,
     *                                  "title": "qwe",
     *                                  "description": "eewqeqe",
     *                                  "is_done": "eewqeqe",
     *                                  "created_at": null
     *                              }
     *                          }
     *                      }
     *              }
     *         )
     *     ),
     *     @OA\Response(
     *         response=Symfony\Component\HttpFoundation\Response::HTTP_UNAUTHORIZED,
     *         description="Unauthenticated.",
     *         @OA\JsonContent(
     *              example={
     *                 "error": {
     *                     "code": 401,
     *                     "message": "Unauthenticated."
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
     *         description="Unauthorized.",
     *         @OA\JsonContent(
     *              example={
     *                 "error": {
     *                     "code": 403,
     *                     "message": "Unauthorized."
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
     *         description="User not found.",
     *         @OA\JsonContent(
     *              example={
     *                 "error": {
     *                     "code": 404,
     *                     "message": "Record not found."
     *                 }
     *             }
     *         )
     *     )
     * )
     *
     * Get list of todo list of user
     * @param Request $request
     * @param $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        return $this->runWithExceptionHandling(function () use ($request) {
            $todos = $this->todoRepository->search([
                ['user_id', Auth::guard()->user()->id]
            ]);
            $this->response->setData(new TodoCollection($todos));
        });
    }

    /**
     * @OA\Post(
     *     path="/api/todo",
     *     summary="Create todo",
     *     description="Create todo",
     *     tags={"TODO"},
     *     security={
     *         { "bearer": {}}
     *     },
     *     @OA\RequestBody(
     *         description="JSON Payload",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="title",
     *                 description="Title of the todo",
     *                 type="string",
     *                 example="",
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 description="Description of the todo",
     *                 type="string",
     *                 example="",
     *             ),
     *             @OA\Property(
     *                 property="is_done",
     *                 description="",
     *                 type="bolean",
     *                 example=false,
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=Symfony\Component\HttpFoundation\Response::HTTP_OK,
     *         description="User creation successful.",
     *         @OA\JsonContent(
     *             example={
     *                   "data": {
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
     *                          "email": {"The title field is required."}
     *                      }
     *                  }
     *             }
     *         )
     *     ),
     * )
     *
     * Create a todo
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        return $this->runWithExceptionHandling(function () use ($request) {
            $this->validate($request, [
                'title'         =>  'required|max:50',
                'is_done'       =>  'boolean'
            ]);

            $todo = $this->todoRepository->create([
                'user_id'       =>  Auth::guard()->user()->id,
                'title'         =>  $request->post("title"),
                'description'   =>  $request->post("description"),
                'is_done'       =>  $request->post("is_done")
            ]);

            $this->response->setData(['data' => new TodoResource($todo)]);
        });
    }
}
