<?php

namespace Modules\User\Services;

use Modules\User\Entities\User;
use Modules\User\Interfaces\UserInterface;
use Modules\User\Interfaces\UserInterestTypes;

class UserService
{
    private UserInterface $userRepository;

    public function __construct(UserInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getProfile(User $user): array
    {
        return [
            'user' => $user,
            'interests' => $this->userRepository->getUserInterests($user)
        ];
    }

    public function updateProfile(User $user, array $validatedData): bool
    {
        // Update user info
        $this->userRepository->update($user, $validatedData);
        
        // Update user preferences
        $this->userRepository->syncInterests($user, $validatedData['categories'], UserInterestTypes::CATEGORY);
        $this->userRepository->syncInterests($user, $validatedData['authors'], UserInterestTypes::AUTHOR);
        $this->userRepository->syncInterests($user, $validatedData['sources'], UserInterestTypes::SOURCE);

        return true;
    }
}
