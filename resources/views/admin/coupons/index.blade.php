@extends('layouts.admin')

@section('title', 'Coupons & Promo Codes')

@section('content')
<div class="mk-card" style="margin-bottom:28px;">
    <div class="mk-card-header">
        <div>
            <h3 class="mk-card-title">Coupons & Promo Codes</h3>
            <p class="mk-card-subtitle">Create promotional discounts and marketing vouchers for customers</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm" onclick="toggleCouponModal()">
            <i class="fa-solid fa-plus"></i> Create New Coupon
        </button>
    </div>

    <!-- Coupons Table -->
    <div class="table-responsive">
        <table class="mk-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Discount</th>
                    <th>Service</th>
                    <th>Min Order</th>
                    <th>Usage / Limit</th>
                    <th>Validity</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($coupons as $c)
                    <tr>
                        <td>
                            <span style="font-family:monospace; font-size:0.9375rem; font-weight:800; color:var(--palette-primary-main); letter-spacing:1px; background:rgba(24, 119, 242, 0.08); padding:4px 8px; border-radius:6px;">
                                {{ $c->code }}
                            </span>
                        </td>
                        <td style="font-weight:700;">
                            @if($c->discount_type === 'percentage')
                                {{ $c->discount_value }}% OFF
                                @if($c->max_discount_amount)
                                    <span style="font-size:0.75rem; color:var(--palette-text-secondary);">(Max ₦{{ number_format($c->max_discount_amount, 2) }})</span>
                                @endif
                            @else
                                ₦{{ number_format($c->discount_value, 2) }} Flat
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-info" style="text-transform:uppercase;">{{ $c->service_type }}</span>
                        </td>
                        <td>₦{{ number_format($c->min_order_amount, 2) }}</td>
                        <td>
                            <strong>{{ $c->usages_count ?? $c->used_count }}</strong> / 
                            {{ $c->usage_limit ? $c->usage_limit : '∞' }}
                        </td>
                        <td style="font-size:0.8125rem; color:var(--palette-text-secondary);">
                            @if($c->expires_at)
                                Exp: {{ $c->expires_at->format('M d, Y') }}
                            @else
                                Never Expires
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $c->status === 'active' ? 'badge-success' : 'badge-error' }}">
                                {{ ucfirst($c->status) }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                <form method="POST" action="{{ route('admin.coupons.toggle', $c->id) }}" style="margin:0;">
                                    @csrf
                                    <button type="submit" class="btn btn-outline btn-sm" title="Toggle Status">
                                        <i class="fa-solid fa-power-off"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.coupons.destroy', $c->id) }}" onsubmit="return confirm('Are you sure you want to delete this coupon?')" style="margin:0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline btn-sm" style="color:var(--palette-error-main);" title="Delete Coupon">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center; padding:32px; color:var(--palette-text-secondary);">
                            No promotional coupon codes created yet. Click "Create New Coupon" to add one.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px;">
        {{ $coupons->links() }}
    </div>
</div>

<!-- Create Coupon Modal -->
<div id="couponModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:2000; align-items:center; justify-content:center; backdrop-filter:blur(2px);">
    <div class="mk-card" style="width:100%; max-width:550px; margin:20px; max-height:90vh; overflow-y:auto;">
        <div class="mk-card-header">
            <h3 class="mk-card-title">Create Promo Code</h3>
            <button type="button" class="btn btn-outline btn-sm" onclick="toggleCouponModal()">&times;</button>
        </div>

        <form method="POST" action="{{ route('admin.coupons.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label" for="coupon_code">Coupon Code</label>
                <input type="text" id="coupon_code" name="code" class="form-control" required placeholder="e.g. WELCOME50" style="text-transform:uppercase; font-weight:700; letter-spacing:1px;">
            </div>

            <div class="grid-2" style="gap:14px;">
                <div class="form-group">
                    <label class="form-label">Discount Type</label>
                    <select name="discount_type" class="form-select" required>
                        <option value="fixed">Fixed Amount (₦)</option>
                        <option value="percentage">Percentage (%)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Discount Value</label>
                    <input type="number" name="discount_value" class="form-control" step="0.01" min="0.01" required placeholder="50 or 5">
                </div>
            </div>

            <div class="grid-2" style="gap:14px;">
                <div class="form-group">
                    <label class="form-label">Applicable Service</label>
                    <select name="service_type" class="form-select" required>
                        <option value="all">All Services</option>
                        <option value="data">Data Bundles</option>
                        <option value="airtime">Airtime</option>
                        <option value="electricity">Electricity</option>
                        <option value="cable">Cable TV</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Min Order Amount (₦)</label>
                    <input type="number" name="min_order_amount" class="form-control" step="0.01" min="0" placeholder="0">
                </div>
            </div>

            <div class="grid-2" style="gap:14px;">
                <div class="form-group">
                    <label class="form-label">Max Discount Amount (₦)</label>
                    <input type="number" name="max_discount_amount" class="form-control" step="0.01" min="0" placeholder="Optional cap">
                </div>

                <div class="form-group">
                    <label class="form-label">Total Usage Limit</label>
                    <input type="number" name="usage_limit" class="form-control" min="1" placeholder="Optional (e.g. 100)">
                </div>
            </div>

            <div class="grid-2" style="gap:14px;">
                <div class="form-group">
                    <label class="form-label">Starts At</label>
                    <input type="datetime-local" name="starts_at" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Expires At</label>
                    <input type="datetime-local" name="expires_at" class="form-control">
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:16px;">
                <i class="fa-solid fa-check"></i> Create Coupon Code
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function toggleCouponModal() {
        const modal = document.getElementById('couponModal');
        modal.style.display = (modal.style.display === 'flex') ? 'none' : 'flex';
    }
</script>
@endpush
@endsection
