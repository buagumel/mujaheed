@extends('layouts.admin')

@section('title', 'Electricity DISCOs')

@section('content')
<div class="mk-card">
    <div class="mk-card-header">
        <div>
            <h3 class="mk-card-title">Electricity Providers (DISCOs)</h3>
            <p class="mk-card-subtitle">Manage convenience fees, payment limits and operator statuses</p>
        </div>
    </div>

    <div class="grid-2">
        @foreach($providers as $p)
            <div style="border:1px solid rgba(145, 158, 171, 0.2); border-radius:12px; padding:20px; background:var(--palette-background-paper);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                    <div>
                        <div style="font-size:1.05rem; font-weight:800;">{{ $p->name }}</div>
                        <div style="font-size:0.75rem; color:var(--palette-text-secondary); font-family:monospace;">Code: {{ $p->code }}</div>
                    </div>
                    <span class="badge {{ $p->status === 'active' ? 'badge-success' : 'badge-error' }}">{{ ucfirst($p->status) }}</span>
                </div>

                <form method="POST" action="{{ route('admin.electricity.update', $p->id) }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="fee_{{ $p->id }}">Convenience Fee (₦)</label>
                        <input type="number" id="fee_{{ $p->id }}" name="convenience_fee" class="form-control" value="{{ $p->convenience_fee }}" required>
                    </div>

                    <div style="display:flex; gap:12px;">
                        <div class="form-group" style="flex:1;">
                            <label class="form-label" for="min_{{ $p->id }}">Min Amount (₦)</label>
                            <input type="number" id="min_{{ $p->id }}" name="min_amount" class="form-control" value="{{ $p->min_amount }}" required>
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label class="form-label" for="max_{{ $p->id }}">Max Amount (₦)</label>
                            <input type="number" id="max_{{ $p->id }}" name="max_amount" class="form-control" value="{{ $p->max_amount }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ $p->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $p->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-sm">Update {{ $p->code }}</button>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endsection
