<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\FinanceLedgerGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FinanceLedgerGroupController extends Controller
{
    public function index(Request $request): View
    {
        $groups = FinanceLedgerGroup::query()
            ->with('parent')
            ->withCount(['children', 'accounts'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->input('search'));

                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('group_code', 'like', '%' . $search . '%')
                        ->orWhere('group_name', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('group_code')
            ->paginate(20)
            ->withQueryString();

        $parentGroups = FinanceLedgerGroup::query()
            ->where('is_active', true)
            ->orderBy('group_name')
            ->get(['id', 'group_code', 'group_name', 'nature', 'currency']);

        return view('finance.ledger-groups.index', compact(
            'groups',
            'parentGroups'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'group_code' => strtoupper(trim((string) $request->input('group_code'))),
            'group_name' => trim((string) $request->input('group_name')),
            'currency' => filled($request->input('currency'))
                ? strtoupper(trim((string) $request->input('currency')))
                : null,
        ]);

        $validated = $request->validate([
            'group_code' => [
                'required',
                'string',
                'max:40',
                'regex:/^[A-Z0-9_-]+$/',
                'unique:finance_ledger_groups,group_code',
            ],
            'group_name' => ['required', 'string', 'max:150'],
            'parent_group_id' => [
                'nullable',
                'integer',
                'exists:finance_ledger_groups,id',
            ],
            'nature' => [
                'required',
                Rule::in(['asset', 'liability', 'equity', 'income', 'expense']),
            ],
            'currency' => ['nullable', 'string', 'max:10', 'regex:/^[A-Z0-9_-]+$/'],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);

        $parentId = $validated['parent_group_id'] ?? null;

        if ($parentId !== null) {
            $parent = FinanceLedgerGroup::query()->findOrFail($parentId);

            if (! $parent->is_active) {
                return back()->withInput()->withErrors([
                    'parent_group_id' => 'Inactive group cannot be selected as parent.',
                ]);
            }

            if (
                $parent->nature !== $validated['nature']
                || $parent->currency !== ($validated['currency'] ?? null)
            ) {
                return back()->withInput()->withErrors([
                    'parent_group_id' => 'Parent group must have the same nature and currency.',
                ]);
            }
        }

        $group = new FinanceLedgerGroup();
        $group->group_code = $validated['group_code'];
        $group->group_name = $validated['group_name'];
        $group->parent_group_id = $parentId;
        $group->nature = $validated['nature'];
        $group->currency = $validated['currency'] ?? null;
        $group->description = $validated['description'] ?? null;
        $group->is_active = true;
        $group->is_system = false;
        $group->created_by = $request->user()->id;
        $group->save();

        return redirect()
            ->route('employee.finance.groups.index')
            ->with('success', 'Ledger Group successfully created.');
    }
}