<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sppg;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SppgController extends Controller
{
    public function edit()
    {
        return Inertia::render('Admin/Sppg/Edit', [
            'sppg' => Sppg::first(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'head_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $sppg = Sppg::first() ?? new Sppg();
        $sppg->fill($validated);
        $sppg->save();

        return back()->with('success', 'Informasi SPPG berhasil disimpan.');
    }
}
