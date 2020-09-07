<?php

namespace App\Transformers;

use App\Transformers\Transformer;
use Laravel\Sanctum\NewAccessToken;

class AccessTokenTransformer extends Transformer
{
    public function transform(NewAccessToken $token)
    {
        return [
            'token' => $token->plainTextToken,
            'name' => $token->accessToken->name,
            'abilities' => $token->accessToken->abilities,
        ];
    }
}
