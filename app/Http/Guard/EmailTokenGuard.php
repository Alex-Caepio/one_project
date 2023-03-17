<?php


namespace App\Http\Guard;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class EmailTokenGuard implements Guard {
    use GuardHelpers;

    public const EMAIL_TOKEN_FIELD = 'email_token';
    public const EMAIL_TOKEN_NAME = 'email-access-token';

    protected $request;
    private ?PersonalAccessToken $token = null;

    public function __construct(UserProvider $provider, Request $request) {
        $this->request = $request;
        $this->provider = $provider;
        $this->request = $request;
    }

    public function user(): ?Authenticatable {
        if (!is_null($this->user)) {
            return $this->user;
        }

        $token = $this->request->get(self::EMAIL_TOKEN_FIELD);

        if (!empty($token)) {
            $this->getToken($token);
            if ($this->checkToken()) {
                $this->setUser($this->token->tokenable);
            }
        }

        return $this->user;
    }

    /**
     * Set the current user.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @return $this
     */
    public function setUser(Authenticatable $user)
    {
        $this->user = $user;
        $this->user->withAccessToken($this->token);
        return $this;
    }

    protected function getToken(string $token): ?PersonalAccessToken {
        if ($this->token) {
            return $this->token;
        }
        $this->token = PersonalAccessToken::findToken($token);
        return $this->token;
    }

    private function checkToken(): bool {
        if (!$this->token) {
            return false;
        }

        if ($this->token->name !== self::EMAIL_TOKEN_NAME){
            return false;
        }

        return true;
    }

    public function validate($credentials = []): bool {
        if (!$this->token) {
            return false;
        }
        return $this->provider->retrieveById($this->token->tokenable_id) !== null;
    }

}
