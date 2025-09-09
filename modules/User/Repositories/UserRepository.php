<?php

namespace Modules\User\Repositories;

use Modules\User\Entities\User;
use Modules\User\Interfaces\UserInterface;
use Modules\User\Interfaces\UserInterestTypes;

class UserRepository implements UserInterface
{
    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function create(array $attributes): User
    {
        return User::create($attributes);
    }

    public function update(User $user, array $attributes): bool
    {
        return $user->update($attributes);
    }

    public function syncInterests(User $user, array $interests, int $itemType): void
    {
        $user->syncInterests($interests, $itemType);
    }

    public function getUserInterests(User $user): array
    {
        $formattedUserInterests = [
            "categories" => [],
            "authors" => [],
            "sources" => [],
        ];

        $userInterests = $user->userInterests()->get();

        foreach ($userInterests as $interest) {
            switch ($interest->item_type_id) {
                case UserInterestTypes::CATEGORY:
                    $formattedUserInterests['categories'][] = $interest->item_id;
                    break;
                case UserInterestTypes::AUTHOR:
                    $formattedUserInterests['authors'][] = $interest->item_id;
                    break;
                case UserInterestTypes::SOURCE:
                    $formattedUserInterests['sources'][] = $interest->item_id;
                    break;
            }
        }

        return $formattedUserInterests;
    }
}
