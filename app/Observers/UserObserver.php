<?php

namespace App\Observers;

use App\Models\User;
use App\Services\SupabaseStorageService;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Handle the User "deleting" event.
     * This will delete the user's avatar from Supabase before deleting the user
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleting(User $user)
    {
        // Delete avatar from Supabase if exists and is not a Google avatar
        if ($user->avatar && !str_contains($user->avatar, 'googleusercontent.com')) {
            try {
                $supabaseStorage = new SupabaseStorageService();
                $supabaseStorage->delete($user->avatar);
                
                Log::info('Deleted user avatar from Supabase', [
                    'user_id' => $user->id,
                    'avatar_path' => $user->avatar,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to delete user avatar from Supabase', [
                    'user_id' => $user->id,
                    'avatar_path' => $user->avatar,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
