
<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();

        return view('user.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['nullable', 'string', 'max:100'],
            'email'      => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore(
                    $user->getKey(),
                    $user->getKeyName()
                ),
            ],
            'mobile' => ['nullable', 'string', 'max:25'],
            'photo'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        unset($validated['photo']);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store(
                'profiles',
                'public'
            );

            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $validated['profile_photo'] = $path;
        }

        $user->fill($validated);
        $user->save();

        return redirect()
            ->route('user.profile.edit')
            ->with('success', 'Data pribadi berhasil diperbarui!');
    }
}