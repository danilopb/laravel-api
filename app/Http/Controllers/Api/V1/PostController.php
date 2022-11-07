<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ExceptionHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\BulkPostRequest;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\V1\PostResource;
use App\Models\Post;
use App\Services\IPostService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    private IPostService $service;

    public function __construct(IPostService $service)
    {
        $this->service =  $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $data = [];
        $codeResponse = 200;
        $message = '';
        $hasError = false;
        try {
            $data['posts'] = PostResource::collection($this->service->findAll($request));
        } catch (\Exception $e) {
            \Log::error('Problem to get Posts in PostController function index', ExceptionHelper::getValuesErrorLog($e));
            report($e);
            $message = trans('post.message.index_error');
            $hasError = true;
            $codeResponse = 500;
        }
        $data['message'] = $message;
        $data['hasError'] = $hasError;
        return response()->json($data, $codeResponse);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StorePostRequest $request
     * @return JsonResponse
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        $data = [];
        $codeResponse = 200;
        $hasError = false;
        try {
            $data['post'] = new PostResource($this->service->create($request->validationData()));
            $message = trans('post.message.store_success');
        } catch (\Exception $e) {
            \Log::error(
                'Problem to store Post in PostController function store',
                ExceptionHelper::getValuesErrorLog($e)
            );
            report($e);
            $message = trans('post.message.store_error');
            $hasError = true;
            $codeResponse = 500;
        }
        $data['message'] = $message;
        $data['hasError'] = $hasError;
        return response()->json($data, $codeResponse);
    }

    /**
     * Bulk Store posts
     *
     * @param BulkPostRequest $request
     * @return JsonResponse
     */
    public function bulkStore(BulkPostRequest $request)
    {
        $data = [];
        $codeResponse = 200;
        $hasError = false;
        try {
            $this->service->bulk($request->validationData());
            $message = trans('post.message.bulk_store_success');
        } catch (\Exception $e) {
            \Log::error(
                'Problem to bulk Posts in PostController function bulkStore',
                ExceptionHelper::getValuesErrorLog($e)
            );
            report($e);
            $message = trans('post.message.bulk_store_error');
            $hasError = true;
            $codeResponse = 500;
        }
        $data['message'] = $message;
        $data['hasError'] = $hasError;
        return response()->json($data, $codeResponse);
    }

    /**
     * Display the specified resource.
     *
     * @param Post $post
     * @return JsonResponse
     */
    public function show(Post $post): JsonResponse
    {
        $data = [];
        $codeResponse = 200;
        $message = '';
        $hasError = false;
        try {
            $data['post'] = new PostResource($post);
        } catch (\Exception $e) {
            \Log::error(
                'Problem to show Post in PostController function show',
                ExceptionHelper::getValuesErrorLog($e)
            );
            report($e);
            $message = trans('post.message.show_error');
            $hasError = true;
            $codeResponse = 500;
        }
        $data['message'] = $message;
        $data['hasError'] = $hasError;
        return response()->json($data, $codeResponse);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdatePostRequest $request
     * @param Post $post
     * @return JsonResponse
     */
    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        $data = [];
        $codeResponse = 200;
        $hasError = false;
        try {
            $data['post'] = new PostResource($this->service->update($post, $request->validationData()));
            $message = trans('post.message.update_success');
        } catch (\Exception $e) {
            \Log::error(
                'Problem to update Post in PostController function update',
                ExceptionHelper::getValuesErrorLog($e)
            );
            report($e);
            $message = trans('post.message.update_error');
            $hasError = true;
            $codeResponse = 500;
        }
        $data['message'] = $message;
        $data['hasError'] = $hasError;
        return response()->json($data, $codeResponse);
    }

    /**
     * Logically remove a record
     *
     * @param Post $post
     * @return JsonResponse
     */
    public function destroy(Post $post): JsonResponse
    {
        $data = [];
        $codeResponse = 200;
        $hasError = false;
        try {
            $this->service->delete($post);
            $message = trans('post.message.destroy_success');
        } catch (\Exception $e) {
            \Log::error(
                'Problem to destroy Post in PostController function destroy',
                ExceptionHelper::getValuesErrorLog($e)
            );
            report($e);
            $message = trans('post.message.destroy_error');
            $hasError = true;
            $codeResponse = 500;
        }
        $data['message'] = $message;
        $data['hasError'] = $hasError;
        return response()->json($data, $codeResponse);
    }

    /**
     * Remove a record physically
     *
     * @param int $id
     * @return JsonResponse
     */
    public function forceDestroy(int $id): JsonResponse
    {
        $data = [];
        $codeResponse = 200;
        $hasError = false;
        try {
            $this->service->forceDelete($id);
            $message = trans('post.message.force_destroy_success');
        } catch (ModelNotFoundException) {
            abort(404);
        } catch (\Exception $e) {
            \Log::error(
                'Problem to force destroy Post in PostController function forceDestroy',
                ExceptionHelper::getValuesErrorLog($e)
            );
            report($e);
            $message = trans('post.message.force_destroy_error');
            $hasError = true;
            $codeResponse = 500;
        }
        $data['message'] = $message;
        $data['hasError'] = $hasError;
        return response()->json($data, $codeResponse);
    }
}
