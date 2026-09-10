@extends('layouts.admin')

@section('title', 'Data Bundles')

@section('content')
<div class="mk-card" style="margin-bottom:28px;">
    <div class="mk-card-header">
        <div>
            <h3 class="mk-card-title">Data Plans Management</h3>
            <p class="mk-card-subtitle">Configure provider cost prices, customer selling prices, and active plans</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('createPlanBox').style.display='block'">
            <i class="fa-solid fa-plus"></i> Add New Plan
        </button>
    </div>

    <!-- Filter Bar -->
    <form method="GET" action="{{ route('admin.data.index') }}" style="margin-bottom:20px; background:var(--palette-background-neutral); padding:16px; border-radius:12px; display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
        <div style="font-weight:700; font-size:0.85rem; color:var(--palette-text-secondary);"><i class="fa-solid fa-filter"></i> Filter Plans:</div>
        <select name="network" class="form-select" style="max-width:160px; font-weight:600; font-size:0.85rem;" onchange="this.form.submit()">
            <option value="">All Networks</option>
            <option value="MTN" {{ request('network') === 'MTN' ? 'selected' : '' }}>MTN</option>
            <option value="AIRTEL" {{ request('network') === 'AIRTEL' ? 'selected' : '' }}>AIRTEL</option>
            <option value="GLO" {{ request('network') === 'GLO' ? 'selected' : '' }}>GLO</option>
            <option value="9MOBILE" {{ request('network') === '9MOBILE' ? 'selected' : '' }}>9MOBILE</option>
        </select>
        <select name="provider" class="form-select" style="max-width:180px; font-weight:600; font-size:0.85rem;" onchange="this.form.submit()">
            <option value="">All Providers</option>
            <option value="bilalsada" {{ request('provider') === 'bilalsada' ? 'selected' : '' }}>BilalsadaSub</option>
            <option value="alrahuz" {{ request('provider') === 'alrahuz' ? 'selected' : '' }}>AlrahuzData</option>
            <option value="n3tdata" {{ request('provider') === 'n3tdata' ? 'selected' : '' }}>N3TData</option>
            <option value="superjara" {{ request('provider') === 'superjara' ? 'selected' : '' }}>Superjara</option>
            <option value="mock" {{ request('provider') === 'mock' ? 'selected' : '' }}>Mock Test</option>
        </select>
        @if(request('network') || request('provider'))
            <a href="{{ route('admin.data.index') }}" class="btn btn-outline btn-sm" style="font-size:0.8rem;"><i class="fa-solid fa-xmark"></i> Clear Filters</a>
        @endif
    </form>

    <!-- Create Plan Accordion Box -->
    <div id="createPlanBox" style="display:none; background:var(--palette-background-neutral); border:1px solid rgba(145, 158, 171, 0.2); border-radius:12px; padding:20px; margin-bottom:24px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border-bottom:1px solid rgba(145, 158, 171, 0.16); padding-bottom:12px;">
            <h4 style="font-size:1rem; font-weight:700; margin:0;">Create New Data Plan</h4>
            <span style="font-size:0.75rem; color:var(--palette-primary-main); font-weight:700;"><i class="fa-solid fa-cloud-arrow-down"></i> Auto-Fetch Enabled</span>
        </div>

        <!-- Provider Plan Preset Auto-Fill Dropdown -->
        <div class="form-group" style="background:#FFF; padding:14px; border-radius:10px; border:1px dashed rgba(24, 119, 242, 0.4); margin-bottom:20px;">
            <label class="form-label" style="font-size:0.8rem; color:var(--palette-primary-main); font-weight:800;">
                <i class="fa-solid fa-wand-magic-sparkles"></i> Select Preset Plan from Provider API Catalog to Auto-Fill
            </label>
            <select id="presetPlanSelect" class="form-select" onchange="applyPresetPlan(this)" style="font-weight:600; font-size:0.875rem;">
                <option value="">-- Choose a Provider Plan to Auto-Fill Details --</option>
            </select>
        </div>

        <form method="POST" action="{{ route('admin.data.store') }}">
            @csrf
            <div class="grid-3">
                <div class="form-group">
                    <label class="form-label" for="provider">API Provider</label>
                    <select id="provider" name="provider" class="form-select" required onchange="fetchProviderPlans(this.value)">
                        <option value="bilalsada">BilalsadaSub</option>
                        <option value="alrahuz">AlrahuzData</option>
                        <option value="n3tdata">N3TData</option>
                        <option value="superjara">Superjara</option>
                        <option value="mock">Mock Simulator</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="network">Network</label>
                    <select id="network" name="network" class="form-select" required>
                        <option value="MTN">MTN</option>
                        <option value="AIRTEL">AIRTEL</option>
                        <option value="GLO">GLO</option>
                        <option value="9MOBILE">9MOBILE</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="name">Plan Name</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="1.0 GB SME" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="code">Plan Code (Unique API Code)</label>
                    <input type="text" id="code" name="code" class="form-control" placeholder="MTN-1GB-NEW" required style="font-family:monospace; font-weight:700;">
                </div>
                <div class="form-group">
                    <label class="form-label" for="type">Plan Type</label>
                    <input type="text" id="type" name="type" class="form-control" placeholder="SME / Corporate" value="SME" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="size">Data Size</label>
                    <input type="text" id="size" name="size" class="form-control" placeholder="1GB" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="validity">Validity</label>
                    <input type="text" id="validity" name="validity" class="form-control" placeholder="30 Days" value="30 Days" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="provider_price">Provider Cost (₦)</label>
                    <input type="number" id="provider_price" name="provider_price" class="form-control" step="0.01" placeholder="260.00" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="selling_price">Selling Price (₦)</label>
                    <input type="number" id="selling_price" name="selling_price" class="form-control" step="0.01" placeholder="290.00" required>
                </div>
            </div>
            <div class="form-group" style="margin-top:8px;">
                <label class="form-label" for="status">Status</label>
                <select id="status" name="status" class="form-select">
                    <option value="active">Active (Visible to customers)</option>
                    <option value="inactive">Inactive (Disabled)</option>
                </select>
            </div>
            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('createPlanBox').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary btn-sm">Save Plan</button>
            </div>
        </form>
    </div>

    <!-- Data Plans Table -->
    <div class="table-responsive">
        <table class="mk-table">
            <thead>
                <tr>
                    <th>Network</th>
                    <th>API Provider</th>
                    <th>Plan Name</th>
                    <th>Plan Code</th>
                    <th>Type / Validity</th>
                    <th>Provider Cost</th>
                    <th>Selling Price</th>
                    <th>Net Profit</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($plans as $p)
                    @php 
                        $profit = $p->selling_price - $p->provider_price;
                        $providerNames = [
                            'bilalsada' => 'BilalsadaSub',
                            'alrahuz'   => 'AlrahuzData',
                            'n3tdata'   => 'N3TData',
                            'superjara' => 'Superjara',
                            'mock'      => 'Mock Test',
                        ];
                        $pName = $providerNames[$p->provider ?? 'bilalsada'] ?? ucfirst($p->provider ?? 'bilalsada');
                    @endphp
                    <tr>
                        <td style="font-weight:700;">{{ $p->network }}</td>
                        <td>
                            <span class="badge badge-info" style="font-weight:700; font-size:0.75rem; letter-spacing:0.3px;">
                                <i class="fa-solid fa-server" style="font-size:0.65rem; margin-right:3px;"></i> {{ $pName }}
                            </span>
                        </td>
                        <td style="font-weight:600;">{{ $p->name }}</td>
                        <td style="font-family:monospace; font-size:0.75rem; font-weight:700; color:var(--palette-primary-main);">{{ $p->code }}</td>
                        <td><span class="badge badge-neutral">{{ $p->type }} ({{ $p->validity }})</span></td>
                        <td>₦{{ number_format($p->provider_price, 2) }}</td>
                        <td style="font-weight:800;">₦{{ number_format($p->selling_price, 2) }}</td>
                        <td style="font-weight:800; color:{{ $profit >= 0 ? 'var(--palette-success-dark,#15803D)' : 'var(--palette-error-main)' }};">
                            {{ $profit >= 0 ? '+' : '' }}₦{{ number_format($profit, 2) }}
                        </td>
                        <td>
                            <span class="badge {{ $p->status === 'active' ? 'badge-success' : 'badge-error' }}">{{ ucfirst($p->status) }}</span>
                        </td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                <button type="button" class="btn btn-outline btn-sm" onclick="editDataPlan({{ json_encode($p) }})" title="Edit Plan & Price">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </button>
                                <form method="POST" action="{{ route('admin.data.destroy', $p->id) }}" onsubmit="return confirm('Delete {{ $p->network }} - {{ $p->name }}?')" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline btn-sm" style="color:var(--palette-error-main);" title="Delete Plan">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px;">
        {{ $plans->links() }}
    </div>
</div>

<!-- Edit Data Plan Modal -->
<div id="editPlanModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center; padding:16px;">
    <div class="mk-card" style="width:100%; max-width:600px; max-height:90vh; overflow-y:auto; padding:24px; border-radius:16px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1px solid rgba(145,158,171,0.16); padding-bottom:12px;">
            <h3 style="font-size:1.1rem; font-weight:800; margin:0;" id="editModalTitle">Edit Data Plan</h3>
            <button type="button" class="btn btn-outline btn-sm" onclick="closeEditModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form id="editPlanForm" method="POST" action="">
            @csrf
            @method('PUT')

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label" for="edit_provider">API Provider</label>
                    <select id="edit_provider" name="provider" class="form-select" required>
                        <option value="bilalsada">BilalsadaSub</option>
                        <option value="alrahuz">AlrahuzData</option>
                        <option value="n3tdata">N3TData</option>
                        <option value="superjara">Superjara</option>
                        <option value="mock">Mock Simulator</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_name">Plan Name</label>
                    <input type="text" id="edit_name" name="name" class="form-control" required>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label" for="edit_code">API Plan Code (Numeric / Provider Code)</label>
                    <input type="text" id="edit_code" name="code" class="form-control" required style="font-family:monospace; font-weight:700;">
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_status">Plan Status</label>
                    <select id="edit_status" name="status" class="form-select">
                        <option value="active">Active (Visible to customers)</option>
                        <option value="inactive">Inactive (Disabled)</option>
                    </select>
                </div>
            </div>

            <div class="grid-3">
                <div class="form-group">
                    <label class="form-label" for="edit_type">Plan Type</label>
                    <input type="text" id="edit_type" name="type" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_size">Size</label>
                    <input type="text" id="edit_size" name="size" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_validity">Validity</label>
                    <input type="text" id="edit_validity" name="validity" class="form-control" required>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label" for="edit_provider_price">Provider Cost (₦)</label>
                    <input type="number" id="edit_provider_price" name="provider_price" class="form-control" step="0.01" min="0" required oninput="calcEditProfit()" style="font-weight:700;">
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_selling_price">Customer Selling Price (₦)</label>
                    <input type="number" id="edit_selling_price" name="selling_price" class="form-control" step="0.01" min="0" required oninput="calcEditProfit()" style="font-weight:800;">
                </div>
            </div>

            <!-- Automatic Profit Calculation Display Box -->
            <div style="background:rgba(24, 119, 242, 0.06); border:1px solid rgba(24, 119, 242, 0.16); border-radius:12px; padding:14px; margin-bottom:18px; display:flex; justify-content:space-between; align-items:center;">
                <span style="font-weight:700; font-size:0.875rem; color:var(--palette-text-secondary);">Calculated Profit per Sale:</span>
                <span id="editProfitDisplay" style="font-size:1.15rem; font-weight:800; color:var(--palette-success-dark,#15803D);">+₦0.00</span>
            </div>

            <div class="form-group">
                <label class="form-label" for="edit_status">Plan Status</label>
                <select id="edit_status" name="status" class="form-select">
                    <option value="active">Active (Visible to customers)</option>
                    <option value="inactive">Inactive (Disabled)</option>
                </select>
            </div>

            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" class="btn btn-outline btn-sm" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-check"></i> Update Data Plan</button>
            </div>
        </form>
    </div>
</div>

<script>
function editDataPlan(plan) {
    document.getElementById('editModalTitle').innerText = 'Edit Data Plan: ' + plan.network + ' ' + plan.name;
    document.getElementById('editPlanForm').action = '/admin/data/' + plan.id;
    document.getElementById('edit_provider').value = plan.provider || 'bilalsada';
    document.getElementById('edit_name').value = plan.name;
    document.getElementById('edit_code').value = plan.code;
    document.getElementById('edit_type').value = plan.type;
    document.getElementById('edit_size').value = plan.size;
    document.getElementById('edit_validity').value = plan.validity;
    document.getElementById('edit_provider_price').value = plan.provider_price;
    document.getElementById('edit_selling_price').value = plan.selling_price;
    document.getElementById('edit_status').value = plan.status;
    
    calcEditProfit();
    
    const modal = document.getElementById('editPlanModal');
    modal.style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editPlanModal').style.display = 'none';
}

function calcEditProfit() {
    const cost = parseFloat(document.getElementById('edit_provider_price').value) || 0;
    const sell = parseFloat(document.getElementById('edit_selling_price').value) || 0;
    const profit = sell - cost;
    
    const display = document.getElementById('editProfitDisplay');
    display.innerText = (profit >= 0 ? '+₦' : '-₦') + Math.abs(profit).toFixed(2);
    display.style.color = profit >= 0 ? 'var(--palette-success-dark, #15803D)' : 'var(--palette-error-main, #FF5630)';
}

let currentProviderCatalog = [];

function fetchProviderPlans(provider) {
    const select = document.getElementById('presetPlanSelect');
    select.innerHTML = '<option value="">Loading plans for ' + provider + '...</option>';
    
    fetch('/admin/data/provider-plans?provider=' + provider)
        .then(res => res.json())
        .then(data => {
            currentProviderCatalog = data.plans || [];
            select.innerHTML = '<option value="">-- Select Plan from ' + provider + ' Catalog (' + currentProviderCatalog.length + ' Available) --</option>';
            
            currentProviderCatalog.forEach((p, idx) => {
                const opt = document.createElement('option');
                opt.value = idx;
                opt.textContent = '[' + p.network + '] ' + p.name + ' - Code: ' + p.code + ' (Cost: ₦' + parseFloat(p.provider_price).toFixed(2) + ')';
                select.appendChild(opt);
            });
        })
        .catch(() => {
            select.innerHTML = '<option value="">-- Choose a Provider Plan to Auto-Fill Details --</option>';
        });
}

function applyPresetPlan(selectElem) {
    const idx = selectElem.value;
    if (idx === '' || !currentProviderCatalog[idx]) return;
    
    const plan = currentProviderCatalog[idx];
    document.getElementById('network').value = plan.network;
    document.getElementById('name').value = plan.name;
    document.getElementById('code').value = plan.code;
    document.getElementById('type').value = plan.type;
    document.getElementById('size').value = plan.size;
    document.getElementById('validity').value = plan.validity;
    document.getElementById('provider_price').value = plan.provider_price;
    document.getElementById('selling_price').value = plan.selling_price;
}

// Initial fetch on page load for default selected provider
document.addEventListener('DOMContentLoaded', function() {
    const providerSelect = document.getElementById('provider');
    if (providerSelect) {
        fetchProviderPlans(providerSelect.value);
    }
});
</script>
@endsection
