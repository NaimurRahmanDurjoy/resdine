<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessConfig;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BusinessConfigController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Settings/BusinessConfig/Index', [
            'configs' => BusinessConfig::all()->groupBy('group'),
            'pageTitle' => 'System configuration'
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'configs' => 'required|array',
            'configs.*.key' => 'required|string',
            'configs.*.value' => 'nullable|string',
            'configs.*.group' => 'nullable|string',
        ]);

        foreach ($validated['configs'] as $config) {
            BusinessConfig::set($config['key'], $config['value'], $config['group'] ?? 'general');
        }

        return back()->with('success', 'Configuration updated successfully.');
    }
}
