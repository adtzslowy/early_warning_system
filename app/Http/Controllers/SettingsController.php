<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Support\DisplayPreferences;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

final class SettingsController extends Controller
{
    public function edit(): View
    {
        return view('settings.index', [
            'prefs' => DisplayPreferences::forCurrentUser(),
            'accents' => DisplayPreferences::ACCENTS,
            'ranges' => DisplayPreferences::RANGES,
            'metrics' => DisplayPreferences::METRICS,
            'cards' => DisplayPreferences::CARDS,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'accent' => ['required', Rule::in(array_keys(DisplayPreferences::ACCENTS))],
            'chart_range' => ['required', Rule::in(array_keys(DisplayPreferences::RANGES))],
            'chart_metric' => ['required', Rule::in(array_keys(DisplayPreferences::METRICS))],
        ]);

        // Checkbox tak dicentang tidak terkirim → bangun dari daftar card yang dikenal.
        $cards = [];
        foreach (array_keys(DisplayPreferences::CARDS) as $key) {
            $cards[$key] = $request->boolean("cards.{$key}");
        }

        // Normalisasi lewat resolver supaya tersimpan konsisten & valid.
        $preferences = DisplayPreferences::resolve([
            'accent' => $validated['accent'],
            'chart_range' => (int) $validated['chart_range'],
            'chart_metric' => $validated['chart_metric'],
            'cards' => $cards,
        ]);

        $user = Auth::user();
        $user->preferences = $preferences;
        $user->save();

        return redirect()
            ->route('settings.edit')
            ->with('status', 'Preferensi tampilan tersimpan.');
    }

    public function profile(): View
    {
        return view('settings.profile', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Edit nama/email dimatikan dulu — form ini menangani foto & ganti password.
        $validated = $request->validate([
            'foto_profil' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'], // maks 2MB
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ], [], [
            'foto_profil' => 'foto profil',
            'password' => 'password',
        ]);

        // Hapus foto lama bila diminta atau akan diganti.
        if (($request->boolean('hapus_foto') || $request->hasFile('foto_profil')) && $user->foto_profil) {
            Storage::disk('public')->delete($user->foto_profil);
            $user->foto_profil = null;
        }

        if ($request->hasFile('foto_profil')) {
            $user->foto_profil = $request->file('foto_profil')->store('foto-profil', 'public');
        }

        // Ganti password bila diisi (cast 'hashed' di model otomatis meng-hash).
        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()
            ->route('settings.profile')
            ->with('status', 'Profil berhasil diperbarui.');
    }

    public function deleteAccount(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        Auth::logout();

        if ($user->foto_profil) {
            Storage::disk('public')->delete($user->foto_profil);
        }

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')
            ->with('status', 'Akun Anda telah dihapus.');
    }
}
