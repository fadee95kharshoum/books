<?php
namespace App\Customs\Services;

use Illuminate\Support\Facades\Hash;

class PasswordService
{
    public function changePassword($data)
    {
        if(!password_verify($data['current_password'], auth()->user()->password)){
            return response()->json([
                'status' => 'failed',
                'message' => 'Password did not match the current password'
            ]);
        }

        $updatePassword = auth()->user()->update([
            'password' => Hash::make($data['password'])
        ]);
        if($updatePassword){
            return response()->json([
                'status' => 'success',
                'message' => 'password updated successfully'
            ]);
        }
        else{
            return response()->json([
                'status' => 'failed',
                'message' => 'An error occured while updating password'
            ]);
        }
    }

}
