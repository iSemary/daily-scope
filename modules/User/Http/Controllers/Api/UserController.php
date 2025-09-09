<?php

namespace Modules\User\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\User\Http\Requests\ProfileRequest;
use Modules\User\Services\UserService;
use Modules\User\Entities\User;

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

    /**
     * Get user by ID
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function getUserById(int $id): JsonResponse
    {
        $user = $this->userService->findById($id);
        
        if (!$user) {
            return $this->return(404, 'User not found');
        }
        
        return $this->return(200, 'User fetched successfully', ['user' => $user]);
    }

    /**
     * Get user by email
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserByEmail(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);
        
        $user = $this->userService->findByEmail($request->email);
        
        if (!$user) {
            return $this->return(404, 'User not found');
        }
        
        return $this->return(200, 'User fetched successfully', ['user' => $user]);
    }

    /**
     * Get user by username
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserByUsername(Request $request): JsonResponse
    {
        $request->validate(['username' => 'required|string']);
        
        $user = $this->userService->findByUsername($request->username);
        
        if (!$user) {
            return $this->return(404, 'User not found');
        }
        
        return $this->return(200, 'User fetched successfully', ['user' => $user]);
    }

    /**
     * Get all users with pagination
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllUsers(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 10);
        $offset = $request->get('offset', 0);
        
        $users = $this->userService->getAllUsers($limit, $offset);
        
        return $this->return(200, 'Users fetched successfully', ['users' => $users]);
    }

    /**
     * Search users
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function searchUsers(Request $request): JsonResponse
    {
        $request->validate(['query' => 'required|string|min:2']);
        
        $query = $request->get('query');
        $limit = $request->get('limit', 10);
        
        $users = $this->userService->searchUsers($query, $limit);
        
        return $this->return(200, 'Search results fetched successfully', ['users' => $users]);
    }

    /**
     * Delete user account
     * 
     * @return JsonResponse
     */
    public function deleteAccount(): JsonResponse
    {
        $user = $this->getAuthenticatedUser();
        
        if ($this->userService->deleteUser($user)) {
            return $this->return(200, 'Account deleted successfully');
        }
        
        return $this->return(400, 'Failed to delete account');
    }
}
