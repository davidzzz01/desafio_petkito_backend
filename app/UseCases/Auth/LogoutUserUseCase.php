<?php

namespace App\UseCases\Auth;

use Illuminate\Http\Request;

class LogoutUserUseCase
{
    public function execute(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout realizado com sucesso']);
    }
}
