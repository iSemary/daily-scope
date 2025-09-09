<?php

namespace Modules\User\Services;

use Modules\User\Entities\User;
use Modules\User\Interfaces\AuthInterface;
use Modules\User\Interfaces\UserInterestTypes;
use Illuminate\Http\Request;

class AuthService
{
    private AuthInterface $authRepository;

    public function __construct(AuthInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function register(array $userData): User
    {
        $user = $this->authRepository->register($userData);
        
        if (isset($userData['categories'])) {
            $user->syncInterests($userData['categories'], UserInterestTypes::CATEGORY);
        }
        
        return $user;
    }

    public function login(string $email, string $password): ?User
    {
        return $this->authRepository->login($email, $password);
    }

    public function logout(User $user, int $type = 0): bool
    {
        return $this->authRepository->logout($user, $type);
    }

    public function isAuthenticated(): bool
    {
        return $this->authRepository->isAuthenticated();
    }

    public function getAuthenticatedUser(): ?User
    {
        return $this->authRepository->getAuthenticatedUser();
    }

    public function collectUserDetails(User $user, bool $generateToken = true): array
    {
        return $this->authRepository->collectUserDetails($user, $generateToken);
    }
}
