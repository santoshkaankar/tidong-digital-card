<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ledger Groups</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f3f5f9; color: #172033; }
        header { background: #14532d; color: white; padding: 18px 22px; }
        main { max-width: 1200px; margin: 26px auto; padding: 0 16px; }
        .card { background: white; padding: 22px; margin-bottom: 20px; border-radius: 12px; box-shadow: 0 3px 12px #0000000d; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; }
        label { display: block; font-weight: bold; margin-bottom: 6px; }
        input, select, textarea { box-sizing: border-box; width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; }
        textarea { min-height: 75px; }
        button, .button { display: inline-block; padding: 10px 15px; border: 0; border-radius: 7px; background: #1d4ed8; color: white; text-decoration: none; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 11px 9px; border-bottom: 1px solid #e2e8f0; }
        th { background: #f8fafc; }
        .alert { padding: 12px; border-radius: 7px; margin-bottom: 14px; }
        .success { background: #dcfce7; color: #166534; }
        .error { background: #fee2e2; color: #991b1b; }
        .muted { color: #64748b; }
        .actions { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 15px; }
        .table-wrap { overflow-x: auto; }
    </style>
</head>
<body>
<header><strong>Finance Management — Ledger Groups</strong></header>

<main>
    <p><a href="{{ route('employee.finance.index') }}">Finance Dashboard</a> |
       <a href="{{ route('employee.finance.manage') }}">Finance Management</a></p>

    <h1>Ledger Groups</h1>
    <p class="muted">Create and review accounting groups. Group codes must be unique.</p>

    @if (session('success'))
        <div class="alert success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert error">
            <strong>Please correct the following:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="card">
        <h2>Create Ledger Group</h2>
        <form method="POST" action="{{ route('employee.finance.groups.store') }}">
            @csrf

            <div class="grid">
                <div>
                    <label for="group_code">Group Code *</label>
                    <input id="group_code" name="group_code" value="{{ old('group_code') }}"
                           maxlength="40" pattern="[A-Za-z0-9_-]+" required>
                    <small>Letters, numbers, underscore and hyphen only.</small>
                </div>

                <div>
                    <label for="group_name">Group Name *</label>
                    <input id="group_name" name="group_name" value="{{ old('group_name') }}"
                           maxlength="150" required>
                </div>

                <div>
                    <label for="nature">Nature *</label>
                    <select id="nature" name="nature" required>
                        <option value="">Select nature</option>
                        @foreach (['asset' => 'Asset', 'liability' => 'Liability', 'equity' => 'Equity', 'income' => 'Income', 'expense' => 'Expense'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('nature') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="currency">Currency</label>
                    <select id="currency" name="currency">
                        <option value="">Not specified</option>
                        <option value="INR" @selected(old('currency') === 'INR')>INR</option>
                        <option value="TCOIN" @selected(old('currency') === 'TCOIN')>T-Coin (TCOIN)</option>
                    </select>
                </div>

                <div>
                    <label for="parent_group_id">Parent Group</label>
                    <select id="parent_group_id" name="parent_group_id">
                        <option value="">No parent (top-level group)</option>
                        @foreach ($parentGroups as $parent)
                            <option value="{{ $parent->id }}" @selected((string) old('parent_group_id') === (string) $parent->id)>
                                {{ $parent->group_code }} — {{ $parent->group_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="description">Description</label>
                    <textarea id="description" name="description" maxlength="5000">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="actions">
                <button type="submit">Create Group</button>
                <a class="button" href="{{ route('employee.finance.index') }}">Back to Finance</a>
            </div>
        </form>
    </section>

    <section class="card">
        <h2>Existing Groups</h2>

        <form method="GET" action="{{ route('employee.finance.groups.index') }}">
            <div class="grid">
                <div>
                    <label for="search">Search by Group Code or Name</label>
                    <input id="search" name="search" value="{{ request('search') }}"
                           placeholder="Enter code or part of name">
                </div>
            </div>
            <div class="actions">
                <button type="submit">Search</button>
                <a class="button" href="{{ route('employee.finance.groups.index') }}">Clear</a>
            </div>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Group Name</th>
                        <th>Parent</th>
                        <th>Nature</th>
                        <th>Currency</th>
                        <th>Child Groups</th>
                        <th>Ledgers</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($groups as $group)
                        <tr>
                            <td>{{ $group->group_code }}</td>
                            <td>{{ $group->group_name }}</td>
                            <td>{{ $group->parent ? $group->parent->group_code . ' — ' . $group->parent->group_name : '—' }}</td>
                            <td>{{ ucfirst($group->nature) }}</td>
                            <td>{{ $group->currency ?: '—' }}</td>
                            <td>{{ $group->children_count }}</td>
                            <td>{{ $group->accounts_count }}</td>
                            <td>{{ $group->is_active ? 'Active' : 'Inactive' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">No ledger groups found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="actions">{{ $groups->links() }}</div>
    </section>
</main>
</body>
</html>