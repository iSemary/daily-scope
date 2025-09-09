<?php

namespace Modules\User\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Modules\User\Http\Requests\ProfileRequest;
use Modules\User\Services\UserService;

class UserController extends ApiController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * The function `getProfile` retrieves the profile data of an authenticated user and returns it as a
     * JSON response.
     * 
     * @return JsonResponse A JsonResponse object is being returned.
     */
    public function getProfile(): JsonResponse
    {
        $user = $this->getAuthenticatedUser();
        $profile = $this->userService->getProfile($user);
        return $this->return(200, "Profile fetched successfully", ['profile' => $profile]);
    }

    /**
     * The function updates the user's profile information and preferences based on the validated data from
     * the profile request.
     * 
     * @param ProfileRequest profileRequest The `ProfileRequest` is a request object that contains the data
     * submitted by the user for updating their profile. It is typically used to validate and sanitize the
     * input data before updating the user's profile.
     * 
     * @return JsonResponse a JsonResponse with a status code of 200 and a message of 'Profile updated
     * successfully'.
     */
    public function updateProfile(ProfileRequest $profileRequest): JsonResponse
    {
        $user = $this->getAuthenticatedUser();
        $validatedData = $profileRequest->validated();
        
        $this->userService->updateProfile($user, $validatedData);

        return $this->return(200, 'Profile updated successfully');
    }
}
