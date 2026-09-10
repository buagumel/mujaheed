@extends('layouts.admin')

@section('title', 'Airtime Discounts')

@section('content')
<div class="mk-card">
    <div class="mk-card-header">
        <div>
            <h3 class="mk-card-title">Airtime Network Discounts</h3>
            <p class="mk-card-subtitle">Set discount percentages and minimum/maximum transaction limits per telecom operator</p>
        </div>
    </div>

    <div class="grid-2">
        @foreach($networks as $net)
            <div style="border:1px solid rgba(145, 158, 171, 0.2); border-radius:12px; padding:20px; background:var(--palette-background-paper);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                    <div style="font-size:1.125rem; font-weight:800;">{{ $net->network }}</div>
                    <span class="badge {{ $net->status === 'active' ? 'badge-success' : 'badge-error' }}">{{ ucfirst($net->status) }}</span>
                </div>

                <form method="POST" action="{{ route('admin.airtime.update', $net->network) }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="disc_{{ $net->id }}">Customer Discount Percentage (%)</label>
                        <input type="number" id="disc_{{ $net->id }}" name="airtime_discount_percent" class="form-control" step="0.1" min="0" max="20" value="{{ $net->airtime_discount_percent }}" required>
                    </div>

                    <div style="display:flex; gap:12px;">
                        <div class="form-group" style="flex:1;">
                            <label class="form-label" for="min_{{ $net->id }}">Min (₦)</label>
                            <input type="number" id="min_{{ $net->id }}" name="airtime_min_amount" class="form-control" value="{{ $net->airtime_min_amount }}" required>
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label class="form-label" for="max_{{ $net->id }}">Max (₦)</label>
                            <input type="number" id="max_{{ $net->id }}" name="airtime_max_amount" class="form-control" value="{{ $net->airtime_max_amount }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Service Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ $net->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $net->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-sm">Save {{ $net->network }} Settings</button>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endsection
