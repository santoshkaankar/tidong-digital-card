<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Platform\ModuleRegistry;
use App\Services\Platform\FeatureRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ModuleManagementController extends Controller
{
    public function index(ModuleRegistry $registry): View
    {
        return view('admin.modules.index', [
            'modules' => $registry->overview(),
            'features' => app(FeatureRegistry::class)->overview(),
        ]);
    }

    public function register(Request $request, ModuleRegistry $registry, string $code): RedirectResponse
    {
        $registry->register($code, (int) $request->user()->id);
        return back()->with('success', 'Module added to management.');
    }

    public function toggle(Request $request, ModuleRegistry $registry, string $code): RedirectResponse
    {
        $data = $request->validate(['enabled' => ['required', 'boolean']]);
        $registry->setEnabled($code, (bool) $data['enabled'], (int) $request->user()->id);
        return back()->with('success', 'Module status updated.');
    }
    public function toggleFeature(Request $request, FeatureRegistry $features, string $code): RedirectResponse
    {
        $data = $request->validate(['enabled' => ['required', 'boolean']]);
        $features->setEnabled($code, (bool) $data['enabled'], (int) $request->user()->id);
        return back()->with('success', 'Feature availability updated. Business permissions remain vendor-controlled.');
    }
}
