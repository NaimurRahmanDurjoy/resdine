<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketingCampaign;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class MarketingCampaignController extends Controller
{
    protected $imageService;

    public function __construct(ImageUploadService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');

        $campaigns = MarketingCampaign::when($search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/MarketingCampaigns/Index', [
            'campaigns' => $campaigns,
            'filters' => $request->only(['search', 'sort', 'direction']),
            'pageTitle' => 'Marketing Campaigns'
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/MarketingCampaigns/Create', [
            'pageTitle' => 'Add New Campaign'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|max:2048', // 2MB Max
            'link_url' => 'nullable|url',
            'type' => 'required|in:popup,banner,coupon',
            'priority' => 'required|integer',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'required|boolean',
        ]);

        try {
            $imagePath = $this->imageService->upload($request->file('image'), 'campaigns');
            
            MarketingCampaign::create(array_merge(collect($validated)->except('image')->toArray(), [
                'image_path' => $imagePath
            ]));

            return redirect()->route('admin.marketing-campaigns.index')->with('success', 'Campaign created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create campaign: ' . $e->getMessage());
        }
    }

    public function edit(MarketingCampaign $marketingCampaign)
    {
        return Inertia::render('Admin/MarketingCampaigns/Edit', [
            'campaign' => $marketingCampaign,
            'pageTitle' => 'Edit Campaign: ' . $marketingCampaign->title
        ]);
    }

    public function update(Request $request, MarketingCampaign $marketingCampaign)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
            'link_url' => 'nullable|url',
            'type' => 'required|in:popup,banner,coupon',
            'priority' => 'required|integer',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'required|boolean',
        ]);

        try {
            $data = collect($validated)->except('image')->toArray();

            if ($request->hasFile('image')) {
                $this->imageService->delete($marketingCampaign->image_path);
                $data['image_path'] = $this->imageService->upload($request->file('image'), 'campaigns');
            }

            $marketingCampaign->update($data);

            return redirect()->route('admin.marketing-campaigns.index')->with('success', 'Campaign updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update campaign: ' . $e->getMessage());
        }
    }

    public function destroy(MarketingCampaign $marketingCampaign)
    {
        $this->imageService->delete($marketingCampaign->image_path);
        $marketingCampaign->delete();

        return redirect()->route('admin.marketing-campaigns.index')->with('success', 'Campaign deleted successfully.');
    }
}
