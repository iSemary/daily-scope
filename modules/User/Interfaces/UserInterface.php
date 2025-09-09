<?php

namespace Modules\User\Interfaces;

use Modules\User\Entities\User;

interface UserInterface
{
    public function findById(int $id): ?User;
    public function findByEmail(string $email): ?User;
    public function findByUsername(string $username): ?User;
    public function create(array $attributes): User;
    public function update(User $user, array $attributes): bool;
    public function delete(User $user): bool;
    public function syncInterests(User $user, array $interests, int $itemType): void;
    public function getUserInterests(User $user): array;
    public function getUserProfile(User $user): array;
    public function updateUserProfile(User $user, array $data): bool;
    public function getAllUsers(int $limit = 10, int $offset = 0): array;
    public function searchUsers(string $query, int $limit = 10): array;
}
