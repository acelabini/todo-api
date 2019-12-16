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
     *                                  "created_at": "2019-12-13 10:46:39"
     *                              },
     *                              {
     *                                  "id": 2,
     *                                  "title": "qwe",
     *                                  "description": "eewqeqe",
     *                                  "is_done": "eewqeqe",
     *                                  "created_at": "2019-12-13 10:46:39"
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
            $todos = $this->todoRepository->getAllByUserId(Auth::guard()->user());

            $this->response->setData(new TodoCollection($todos));
        });
    }

    /**
     * @OA\Get(
     *     path="/api/todo/{todo_id}",
     *     summary="Get todo by id",
     *     description="Get a todo by id",
     *     tags={"TODO"},
     *     security={
     *         { "bearer": {}}
     *     },
     *     @OA\Parameter(
     *         name="todo_id",
     *         in="path",
     *         description="Todo id",
     *         required=true,
     *         schema={
     *            "type"="integer"
     *         }
     *     ),
     *     @OA\Response(
     *         response=Symfony\Component\HttpFoundation\Response::HTTP_OK,
     *         description="Fetch successful.",
     *         @OA\JsonContent(
     *             example={
     *                   "data":  {
     *                      "id": 11,
     *                      "title": "updated",
     *                      "description": "updatetete",
     *                      "is_done": true,
     *                      "created_at": "2019-12-16 05:00:07"
     *                    }
     *             }
     *         )
     *     ),
     * )
     *
     * Get a todo by id
     *
     * @param Request $request
     * @param $todoId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $todoId)
    {
        return $this->runWithExceptionHandling(function () use ($request, $todoId) {
            $todo = $this->todoRepository->find([
                ['id', $todoId],
                ['user_id', Auth::guard()->user()->id]
            ]);

            $this->response->setData(['data' => new TodoResource($todo)]);
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
     *         description="Todo creation successful.",
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

    /**
     * @OA\Patch(
     *     path="/api/todo/{todo_id}",
     *     summary="Update todo",
     *     description="Update a todo",
     *     tags={"TODO"},
     *     security={
     *         { "bearer": {}}
     *     },
     *     @OA\Parameter(
     *         name="todo_id",
     *         in="path",
     *         description="Todo id",
     *         required=true,
     *         schema={
     *            "type"="integer"
     *         }
     *     ),
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
     *         description="Update todo successful.",
     *         @OA\JsonContent(
     *             example={
     *                   "data":  {
     *                      "id": 11,
     *                      "title": "updated",
     *                      "description": "updatetete",
     *                      "is_done": true,
     *                      "created_at": "2019-12-16 05:00:07"
     *                    }
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
     * Update a todo
     *
     * @param Request $request
     * @param $todoId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $todoId)
    {
        return $this->runWithExceptionHandling(function () use ($request, $todoId) {
            $this->validate($request, [
                'title'         =>  'required|max:50'
            ]);
            $todo = $this->todoRepository->find([
                ['id', $todoId],
                ['user_id', Auth::guard()->user()->id]
            ]);

            $updatedTodo = $this->todoRepository->update($todo, [
                'title'         =>  $request->post("title"),
                'description'   =>  $request->post("description")
            ]);

            $this->response->setData(['data' => new TodoResource($updatedTodo)]);
        });
    }

    /**
     * @OA\Patch(
     *     path="/api/todo/{todo_id}/status",
     *     summary="Update todo status",
     *     description="Update a todo status",
     *     tags={"TODO"},
     *     security={
     *         { "bearer": {}}
     *     },
     *     @OA\Parameter(
     *         name="todo_id",
     *         in="path",
     *         description="Todo id",
     *         required=true,
     *         schema={
     *            "type"="integer"
     *         }
     *     ),
     *     @OA\RequestBody(
     *         description="JSON Payload",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 description="true or false",
     *                 type="boolean",
     *                 example="true",
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=Symfony\Component\HttpFoundation\Response::HTTP_OK,
     *         description="Update todo status successful.",
     *         @OA\JsonContent(
     *             example={
     *                   "data":  {
     *                      "id": 11,
     *                      "title": "updated",
     *                      "description": "updatetete",
     *                      "is_done": true,
     *                      "created_at": "2019-12-16 05:00:07"
     *                    }
     *             }
     *         )
     *     ),
     * )
     *
     * Update a todo status
     *
     * @param Request $request
     * @param $todoId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $todoId)
    {
        return $this->runWithExceptionHandling(function () use ($request, $todoId) {
            $this->validate($request, [
                'is_done'       =>  'boolean'
            ]);
            $todo = $this->todoRepository->find([
                ['id', $todoId],
                ['user_id', Auth::guard()->user()->id]
            ]);

            $updatedTodo = $this->todoRepository->update($todo, [
                'is_done'       =>  filter_var($request->get('status'), FILTER_VALIDATE_BOOLEAN)
            ]);

            $this->response->setData(['data' => new TodoResource($updatedTodo)]);
        });
    }

    /**
     * @OA\Delete(
     *     path="/api/todo/{todo_id}",
     *     summary="Delete todo",
     *     description="Delete a todo",
     *     tags={"TODO"},
     *     security={
     *         { "bearer": {}}
     *     },
     *     @OA\Parameter(
     *         name="todo_id",
     *         in="path",
     *         description="Todo id",
     *         required=true,
     *         schema={
     *            "type"="integer"
     *         }
     *     ),
     *     @OA\Response(
     *         response=Symfony\Component\HttpFoundation\Response::HTTP_OK,
     *         description="Delete successful.",
     *         @OA\JsonContent(
     *             example={
     *                   "data":  {
     *                    }
     *             }
     *         )
     *     ),
     * )
     *
     * Delete a todo
     *
     * @param Request $request
     * @param $todoId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $todoId)
    {
        return $this->runWithExceptionHandling(function () use ($request, $todoId) {
            $todo = $this->todoRepository->find([
                ['id', $todoId],
                ['user_id', Auth::guard()->user()->id]
            ]);

             $this->todoRepository->delete($todo);
        });
    }
}
