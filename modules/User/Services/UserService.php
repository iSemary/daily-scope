<?php

namespace Modules\User\Services;

use Modules\User\Entities\User;
use Modules\User\Interfaces\UserInterface;

class UserService
{
    private UserInterface $userRepository;

    public function __construct(UserInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getProfile(User $user): array
    {
        return $this->userRepository->getUserProfile($user);
    }

    public function updateProfile(User $user, array $validatedData): bool
    {
        return $this->userRepository->updateUserProfile($user, $validatedData);
    }

    public function findById(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }

    public function findByUsername(string $username): ?User
    {
        return $this->userRepository->findByUsername($username);
    }

    public function getAllUsers(int $limit = 10, int $offset = 0): array
    {
        return $this->userRepository->getAllUsers($limit, $offset);
    }

    public function searchUsers(string $query, int $limit = 10): array
    {
        return $this->userRepository->searchUsers($query, $limit);
    }

    public function deleteUser(User $user): bool
    {
        return $this->userRepository->delete($user);
    }
}
