<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Support\DisplayPreferences;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
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
}
