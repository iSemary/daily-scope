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

    public function findByUsername(string $username): ?User
    {
        return User::where('username', $username)->first();
    }

    public function delete(User $user): bool
    {
        return $user->delete();
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

    public function getUserProfile(User $user): array
    {
        return [
            'user' => $user,
            'interests' => $this->getUserInterests($user)
        ];
    }

    public function updateUserProfile(User $user, array $data): bool
    {
        $this->update($user, $data);
        
        if (isset($data['categories'])) {
            $this->syncInterests($user, $data['categories'], UserInterestTypes::CATEGORY);
        }
        if (isset($data['authors'])) {
            $this->syncInterests($user, $data['authors'], UserInterestTypes::AUTHOR);
        }
        if (isset($data['sources'])) {
            $this->syncInterests($user, $data['sources'], UserInterestTypes::SOURCE);
        }

        return true;
    }

    public function getAllUsers(int $limit = 10, int $offset = 0): array
    {
        return User::select('id', 'full_name', 'email', 'username', 'created_at')
            ->skip($offset)
            ->take($limit)
            ->get()
            ->toArray();
    }

    public function searchUsers(string $query, int $limit = 10): array
    {
        return User::where('full_name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('username', 'like', "%{$query}%")
            ->select('id', 'full_name', 'email', 'username', 'created_at')
            ->take($limit)
            ->get()
            ->toArray();
    }
}
