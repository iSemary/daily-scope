<?php

namespace Modules\User\Interfaces;

use Modules\User\Entities\User;
use Illuminate\Http\Request;

interface AuthInterface
{
    public function register(array $userData): User;
    public function login(string $email, string $password): ?User;
    public function logout(User $user, int $type = 0): bool;
    public function generateAccessToken(User $user): string;
    public function revokeToken(User $user, ?string $tokenId): bool;
    public function revokeAllTokens(User $user, ?string $currentTokenId): bool;
    public function isAuthenticated(): bool;
    public function getAuthenticatedUser(): ?User;
    public function collectUserDetails(User $user, bool $generateToken = true): array;
}
