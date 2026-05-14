<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BranchSetting;
use App\Models\Branch;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BusinessConfigController extends Controller
{
    public function index()
    {
        $branch = Branch::first();
        $settings = $branch ? BranchSetting::firstOrCreate(['branch_id' => $branch->id]) : null;

        return Inertia::render('Admin/Settings/BusinessConfig/Index', [
            'settings' => $settings,
            'currencies' => \App\Models\Currency::all(),
            'pageTitle' => 'Branch Configuration'
        ]);
    }

    public function update(Request $request)
    {
        $branch = Branch::first();
        if (!$branch) return back()->with('error', 'No branch found.');

        $validated = $request->validate([
            'currency_id' => 'nullable|exists:currencies,id',
            'vat_registration_no' => 'nullable|string',
            'vat_percentage' => 'nullable|numeric',
            'service_charge_percentage' => 'nullable|numeric',
            'is_vat_inclusive' => 'boolean',
            'timezone' => 'nullable|string',
            'language_code' => 'nullable|string',
            'opening_time' => 'nullable|string',
            'closing_time' => 'nullable|string',
            'receipt_header_title' => 'nullable|string',
            'receipt_footer_text' => 'nullable|string',
            'show_logo_on_receipt' => 'boolean',
            'invoice_prefix' => 'nullable|string',
        ]);

        BranchSetting::updateOrCreate(
            ['branch_id' => $branch->id],
            $validated
        );

        return back()->with('success', 'Configuration updated successfully.');
    }
}
