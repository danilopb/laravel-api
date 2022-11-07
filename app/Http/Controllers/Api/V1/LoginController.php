<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ExceptionHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\UserServiceImpl;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Remove the specified resource from storage.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $codeResponse = 200;
        $message = '';
        $hasError = false;
        try {
            $request->authenticate();
            $userService = new UserServiceImpl();
            $data['token'] = $userService->createToken(auth()->user());
        } catch (ValidationException $e) {
            $message = $e->getMessage();
        } catch (\Exception $e) {
            \Log::error(
                'Problem to login in LoginController function login',
                ExceptionHelper::getValuesErrorLog($e)
            );
            report($e);
            $message = trans('auth.login_error');
            $hasError = true;
            $codeResponse = 500;
        }
        $data['message'] = $message;
        $data['hasError'] = $hasError;
        return response()->json($data, $codeResponse);
    }
}
