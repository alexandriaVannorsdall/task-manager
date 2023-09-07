<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function getProfile(): JsonResponse
    {
        $user = Auth::user();

        return $this->success([
            'profile' => new UserResource($user),
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string']
        ]);

        /** @var User $user */
        $user = Auth::user();
        $user->name = $request->name;
        $user->save();

        return $this->success([
            'profile' => new UserResource($user),
        ]);
    }
}
