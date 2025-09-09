<?php

namespace Modules\User\Repositories;

use Modules\User\Entities\User;
use Modules\User\Interfaces\AuthInterface;
use Modules\User\Interfaces\UserInterface;
use Modules\Country\Entities\Country;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthRepository implements AuthInterface
{
    private UserInterface $userRepository;

    public function __construct(UserInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $userData): User
    {
        $email = $userData['email'];
        $username = strtok($email, '@');
        $userData['username'] = $username . Str::random(4);
        $userData['country_id'] = Country::getCountryIdByCode($userData['country_code']);
        
        return $this->userRepository->create($userData);
    }

    public function login(string $email, string $password): ?User
    {
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            return Auth::user();
        }
        return null;
    }

    public function logout(User $user, int $type = 0): bool
    {
        try {
            $currentToken = $user->token();
            $currentTokenId = $currentToken ? $currentToken->id : null;
            
            if ($type == 1) {
                return $this->revokeToken($user, $currentTokenId);
            } else {
                return $this->revokeAllTokens($user, $currentTokenId);
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    public function generateAccessToken(User $user): string
    {
        return $user->createToken('web-app')->accessToken;
    }

    public function revokeToken(User $user, ?string $tokenId): bool
    {
        if (!$tokenId) {
            return false;
        }
        return DB::table("oauth_access_tokens")->where("id", $tokenId)->delete() > 0;
    }

    public function revokeAllTokens(User $user, ?string $currentTokenId): bool
    {
        $user->tokens->each(function ($token) use ($currentTokenId) {
            if ($currentTokenId && $token->id !== $currentTokenId) {
                $token->delete();
            } elseif (!$currentTokenId) {
                $token->delete();
            }
        });
        return true;
    }

    public function isAuthenticated(): bool
    {
        return Auth::guard('api')->check();
    }

    public function getAuthenticatedUser(): ?User
    {
        return Auth::guard('api')->user();
    }

    public function collectUserDetails(User $user, bool $generateToken = true): array
    {
        if ($generateToken) {
            $accessToken = $this->generateAccessToken($user);
        }
        
        $userData = $this->selectUserData($user);
        
        if ($generateToken) {
            $userData['access_token'] = $accessToken;
        }
        
        return $userData;
    }

    private function selectUserData(User $user): array
    {
        return User::where("id", $user->id)
            ->select('full_name', 'email', 'username', 'created_at')
            ->first()
            ->toArray();
    }
}
