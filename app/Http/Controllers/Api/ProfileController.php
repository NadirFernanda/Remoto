<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->loadMissing('freelancerProfile', 'profile');

        return response()->json([
            'id'                 => $user->id,
            'name'               => $user->name,
            'email'              => $user->email,
            'role'               => $user->role,
            'status'             => $user->status,
            'email_verified'     => !is_null($user->email_verified_at),
            'freelancer_profile' => $user->freelancerProfile,
            'profile'            => $user->profile,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name'         => 'sometimes|string|max:255',
            'password'     => 'sometimes|string|min:8|confirmed',
            // FreelancerProfile fields
            'bio'          => 'sometimes|nullable|string|max:2000',
            'cidade'       => 'sometimes|nullable|string|max:100',
            'habilidades'  => 'sometimes|nullable|string',
            'valor_hora'   => 'sometimes|nullable|numeric|min:0',
        ]);

        if (isset($data['name'])) {
            $user->name = strip_tags($data['name']);
        }

        if (isset($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        // Update freelancer profile if fields provided
        $profileFields = array_intersect_key($data, array_flip(['bio', 'cidade', 'habilidades', 'valor_hora']));
        if (!empty($profileFields) && $user->freelancerProfile) {
            $user->freelancerProfile->update($profileFields);
        }

        return response()->json([
            'message' => 'Perfil actualizado com sucesso.',
            'user'    => $user->refresh()->loadMissing('freelancerProfile'),
        ]);
    }
}
