<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\AuthorSlugRedirect;
use App\Support\AuthorProfile;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('cms.profile', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $user = $request->user();
            $previousSlug = $user->slug;

            $user->fill($request->validated());
            if ($user instanceof MustVerifyEmail && $user->isDirty('email')) {
                $user->email_verified_at = null;
            }
            $nextSlug = AuthorProfile::nextSlug($user, $user->name);
            $user->slug = $nextSlug;
            $user->save();

            AuthorSlugRedirect::query()
                ->where('userId', $user->id)
                ->where('slug', $nextSlug)
                ->delete();

            if ($previousSlug && $previousSlug !== $nextSlug) {
                AuthorSlugRedirect::updateOrCreate(
                    ['slug' => $previousSlug],
                    ['userId' => $user->id],
                );
            }
        });

        return Redirect::route('profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        abort(403, 'Akun CMS tidak dapat dihapus sendiri. Writer harus dihapus Admin dengan reassignment artikel.');
    }
}
