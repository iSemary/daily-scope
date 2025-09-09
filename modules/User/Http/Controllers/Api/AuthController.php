<?php

namespace Modules\User\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Modules\User\Http\Requests\LoginRequest;
use Modules\User\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use Modules\User\Services\AuthService;
use Modules\User\Entities\User;

class AuthController extends ApiController {
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle user registration.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $registerRequest): JsonResponse {
        $userRequest = $registerRequest->validated();
        $user = $this->authService->register($userRequest);
        $response = $this->authService->collectUserDetails($user);
        
        return $this->return(200, 'User Registered Successfully', ['user' => $response]);
    }

    /**
     * Handle user login.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $loginRequest): JsonResponse {
        $user = $this->authService->login($loginRequest->email, $loginRequest->password);
        
        if ($user) {
            $response = $this->authService->collectUserDetails($user);
            return $this->return(200, 'User Logged in Successfully', ['user' => $response]);
        }
        
        return $this->return(400, 'Invalid email or password');
    }


    /**
     * The function logs out a user by deleting their access tokens either for a specific request or for
     * all tokens associated with the user.
     * 
     * @param Request request The  parameter is an instance of the Request class, which represents
     * an HTTP request. It contains information about the request such as the request method, headers, and
     * input data. In this case, it is used to determine the type of logout action to perform.
     * 
     * @return JsonResponse a JsonResponse.
     */
    public function logout(Request $request): JsonResponse {
        $user = $this->authService->getAuthenticatedUser();
        
        if ($this->authService->logout($user, $request->type ?? 0)) {
            return $this->return(200, 'Logged out successfully');
        }
        
        return $this->return(400, 'Couldn\'t logout using this token');
    }

    /**
     * The function checks if the user is authenticated and returns a JSON response indicating the
     * authentication status.
     * 
     * @return JsonResponse A JsonResponse object is being returned.
     */
    public function checkAuthentication(): JsonResponse {
        if ($this->authService->isAuthenticated()) {
            return $this->return(200, "Authenticated successfully");
        }
        return $this->return(400, "Session expired");
    }

    /**
     * The function retrieves the authenticated user details and returns a JSON response with the user
     * information.
     * 
     * @return JsonResponse a JsonResponse object.
     */
    public function getUser(): JsonResponse {
        $user = $this->authService->getAuthenticatedUser();
        $userData = $this->authService->collectUserDetails($user, false);
        return $this->return(200, "User fetched successfully", ['user' => $userData]);
    }
}
