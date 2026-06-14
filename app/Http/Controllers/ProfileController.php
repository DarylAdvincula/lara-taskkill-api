<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\DeleteAccountRequest;
use App\Http\Requests\UpdateProfileRequest;

class ProfileController extends Controller
{
    public function update(UpdateProfileRequest $request)
    {
        $request->user()->update($request->validated());

        return response()->json([
            'message' => 'Profile has been updated!',
            'data' => ['profile' => $request->user()->fresh()]
        ]);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $request->user()->update($request->validated());

        return response()->json(['message' => 'Password has been updated!',]);
    }

    public function destroy(DeleteAccountRequest $request)
    {
        $request->user()->tokens()->delete();
        $request->user()->delete();
        
        return response()->json(['message' => 'Account has been deleted.']);
    }
}
