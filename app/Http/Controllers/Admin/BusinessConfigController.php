<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BranchSetting;
use App\Models\Branch;
use App\Models\Currency;
use Illuminate\Http\Request;
use Inertia\Inertia;
use DateTimeZone;

class BusinessConfigController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $activeBranchId = $user->getActiveBranchId();

        if ($request->has('branch_id') && strtolower($user->role->name ?? '') === 'admin') {
            $activeBranchId = $request->input('branch_id');
        }

        $branch = Branch::find($activeBranchId) ?: Branch::first();
        
        $settings = $branch ? BranchSetting::firstOrCreate(
            ['branch_id' => $branch->id],
            [
                'timezone' => 'Asia/Dhaka',
                'language_code' => 'en',
                'invoice_prefix' => 'INV'
            ]
        ) : null;

        return Inertia::render('Admin/Settings/BusinessConfig/Index', [
            'settings' => $settings,
            'selectedBranchId' => $branch ? $branch->id : null,
            'timezones' => DateTimeZone::listIdentifiers(),
            'currencies' => Currency::all(),
            'pageTitle' => 'Branch Configuration'
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $branchId = $request->input('branch_id') ?: $user->getActiveBranchId();

        if (strtolower($user->role->name ?? '') !== 'admin') {
            $branchId = $user->branch_id;
        }

        $branch = Branch::find($branchId);
        if (!$branch) return back()->with('error', 'No branch found.');

        $validated = $request->validate([
            'currency_id' => 'nullable|exists:currencies,id',
            'vat_registration_no' => 'nullable|string',
            'vat_percentage' => 'nullable|numeric',
            'service_charge_percentage' => 'nullable|numeric',
            'is_vat_inclusive' => 'boolean',
            'timezone' => ['required', 'timezone'],
            'language_code' => 'nullable|string',
            'opening_time' => 'nullable',
            'closing_time' => 'nullable',
            'receipt_header_title' => 'nullable|string',
            'receipt_footer_text' => 'nullable|string',
            'show_logo_on_receipt' => 'nullable|boolean',
            'invoice_prefix' => 'nullable|string',
        ]);

        BranchSetting::updateOrCreate(
            ['branch_id' => $branch->id],
            $validated
        );

        return back()->with('success', 'Configuration updated successfully.');
    }

    public function switchBranch(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id'
        ]);

        $user = auth()->user();
        if (strtolower($user->role->name ?? '') !== 'admin') {
            return back()->with('error', 'Unauthorized to switch branch.');
        }

        session(['active_branch_id' => $validated['branch_id']]);

        return back()->with('success', 'Switched to selected branch successfully.');
    }
}
