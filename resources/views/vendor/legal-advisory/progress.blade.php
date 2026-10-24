<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Professional Work Progress</title>
<style>body{margin:0;background:#f5f7fb;color:#17243a;font:16px system-ui,sans-serif}main{max-width:960px;margin:auto;padding:24px 16px}.card{background:white;border:1px solid #dfe5ef;border-radius:14px;padding:20px;margin:16px 0;overflow-wrap:anywhere}h1{font-size:clamp(1.5rem,4vw,2.2rem)}.muted{color:#536178}.tag{display:inline-block;padding:4px 10px;background:#edf2f9;border-radius:99px}dl{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px}dt{color:#536178}dd{margin:4px 0;font-weight:600}a{color:#164eb5}time{font-size:.9rem;color:#536178}.notice{border-left:4px solid #ad7412;padding-left:14px}article{border-top:1px solid #e6e9ef;padding-top:14px;margin-top:14px}p{line-height:1.5}</style></head>
<body><main><a href="{{ route('vendor.legal.index') }}">← Back to engagements</a>
<h1>Professional work progress</h1>
<section class="card"><h2>{{ $record->professional_name }}</h2><dl><div><dt>Category</dt><dd>{{ strtoupper($record->category) }}</dd></div><div><dt>Engagement</dt><dd><span class="tag">{{ ucfirst($record->status) }}</span></dd></div><div><dt>Scope</dt><dd>{{ ucfirst($record->scope) }}</dd></div><div><dt>Business / branch</dt><dd>{{ $record->business_id ?? 'All' }} / {{ $record->branch_id ?? 'All' }}</dd></div></dl></section>
<section class="card"><h2>Work updates</h2><p class="notice"><strong>Important:</strong> These updates are reported by the assigned professional. A reference or a “Completed” status is not proof that a tax filing, audit, or other task was independently verified. Confirm with the relevant authority or original document.</p>
@forelse($updates as $update)
<article><h3>{{ $update->work_type }} <span class="tag">{{ str_replace('_',' ',ucfirst($update->status)) }}</span></h3>
<p class="muted">{{ $update->period ?: 'Period not specified' }} · <time>{{ $update->recorded_at }}</time></p>
<p>{{ $update->description }}</p>
@if($update->evidence_reference)<p><strong>Evidence reference (unverified):</strong> {{ $update->evidence_reference }}</p>@endif
</article>
@empty<p>No work updates have been recorded yet. Engagement acceptance alone does not establish work completion.</p>@endforelse
</section></main></body></html>
