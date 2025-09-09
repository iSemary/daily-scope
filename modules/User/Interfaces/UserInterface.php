<?php

namespace modules\User\Interfaces;

use modules\User\Entities\User;

interface UserInterface
{
    public function findById(int $id): ?User;
    public function findByEmail(string $email): ?User;
    public function create(array $attributes): User;
    public function update(User $user, array $attributes): bool;
    public function syncInterests(User $user, array $interests, int $itemType): void;
    public function getUserInterests(User $user): array;
}
