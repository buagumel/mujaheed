@extends('layouts.admin')

@section('title', 'Broadcast & Email Engine')

@section('content')
<div style="margin-bottom:28px;">
    <h2 style="font-size:1.5rem; font-weight:800; color:var(--palette-text-primary);">Broadcast & Marketing Engine</h2>
    <p style="font-size:0.875rem; color:var(--palette-text-secondary);">Send targeted email campaigns and in-app system announcements to specific or all customers</p>
</div>

<div class="grid-3" style="margin-bottom:28px;">
    <!-- Broadcast Composer Form -->
    <div class="mk-card" style="grid-column: span 2;">
        <div class="mk-card-header">
            <div>
                <h3 class="mk-card-title">Compose New Broadcast</h3>
                <p class="mk-card-subtitle">Dispatch announcements instantly via email and notification center</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.broadcast.send') }}" id="broadcastForm">
            @csrf

            <div class="form-group">
                <label class="form-label" for="title">Broadcast Title / Email Subject</label>
                <input type="text" id="title" name="title" class="form-control" placeholder="e.g. Special Weekend Airtime & Data Discounts!" value="{{ old('title') }}" required>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label" for="target_audience">Target Audience Filter</label>
                    <select id="target_audience" name="target_audience" class="form-select" onchange="toggleAudienceMode(this.value)" required>
                        <option value="all">All Registered Customers ({{ $totalCustomers }})</option>
                        <option value="selected" {{ old('target_audience') === 'selected' ? 'selected' : '' }}>Select Specific Customers (Tick from list)</option>
                        <option value="active">Active Customers Only ({{ $activeCustomers }})</option>
                        <option value="inactive">Inactive / Suspended Customers</option>
                        <option value="funded">Funded Customers (Balance > ₦0)</option>
                        <option value="unfunded">Zero-Balance Customers (Balance = ₦0)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="channel">Delivery Channel</label>
                    <select id="channel" name="channel" class="form-select" required>
                        <option value="both" selected>Both (Email + In-App Notification)</option>
                        <option value="email">Email Broadcast Only</option>
                        <option value="notification">In-App Notification Center Only</option>
                    </select>
                </div>
            </div>

            <!-- INTERACTIVE CUSTOMER SELECTION TABLE (CHECKBOXES / TICK LIST) -->
            <div id="userSelectionContainer" style="display:none; margin-bottom:24px; border:1px solid rgba(145, 158, 171, 0.24); border-radius:12px; padding:16px; background:var(--palette-background-paper); box-shadow:var(--shadow-card);">
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:14px;">
                    <div>
                        <h4 style="font-size:0.9375rem; font-weight:700; color:var(--palette-text-primary); margin-bottom:2px;">
                            <i class="fa-solid fa-list-check" style="color:var(--palette-primary-main);"></i> Select Customers to Message
                        </h4>
                        <div style="font-size:0.75rem; color:var(--palette-text-secondary);">
                            Tick the specific user(s) you wish to send this message to.
                        </div>
                    </div>

                    <div style="display:flex; align-items:center; gap:10px;">
                        <span class="badge badge-info" id="selectedCounterBadge" style="font-size:0.75rem; padding:6px 10px;">0 Selected</span>
                        <button type="button" class="btn btn-outline btn-sm" onclick="toggleSelectAllUsers(true)">Select All</button>
                        <button type="button" class="btn btn-outline btn-sm" onclick="toggleSelectAllUsers(false)">Clear</button>
                    </div>
                </div>

                <!-- Live User Search Box -->
                <div style="margin-bottom:12px;">
                    <input type="text" id="userSearchInput" class="form-control" placeholder="🔍 Search customers by name, email, or phone number..." oninput="filterUserList(this.value)">
                </div>

                <!-- Scrollable Checkbox Table -->
                <div style="max-height:280px; overflow-y:auto; border:1px solid rgba(145, 158, 171, 0.16); border-radius:8px;">
                    <table class="mk-table" style="font-size:0.8125rem;">
                        <thead>
                            <tr style="position:sticky; top:0; z-index:10; background:var(--palette-background-neutral);">
                                <th style="width:44px; text-align:center;">
                                    <input type="checkbox" id="masterUserCheckbox" onchange="toggleSelectAllUsers(this.checked)" style="cursor:pointer; width:16px; height:16px;">
                                </th>
                                <th>Customer</th>
                                <th>Phone</th>
                                <th>Wallet Balance</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="userTableBody">
                            @foreach($users as $u)
                                <tr class="user-row" data-search="{{ strtolower($u->name . ' ' . $u->email . ' ' . $u->phone) }}" style="cursor:pointer;" onclick="toggleRowCheckbox(this, event)">
                                    <td style="text-align:center;">
                                        <input type="checkbox" name="user_ids[]" value="{{ $u->id }}" class="user-checkbox" onchange="updateSelectedCount()" style="cursor:pointer; width:16px; height:16px;">
                                    </td>
                                    <td>
                                        <div style="display:flex; align-items:center; gap:10px;">
                                            <div style="width:30px; height:30px; border-radius:50%; background:var(--palette-primary-lighter); color:var(--palette-primary-main); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.75rem;">
                                                {{ substr($u->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div style="font-weight:700; color:var(--palette-text-primary);">{{ $u->name }}</div>
                                                <div style="font-size:0.6875rem; color:var(--palette-text-secondary);">{{ $u->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="font-family:monospace;">{{ $u->phone ?? 'N/A' }}</td>
                                    <td style="font-weight:700; color:var(--palette-primary-main);">₦{{ number_format($u->wallet->balance ?? 0, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $u->status === 'active' ? 'badge-success' : 'badge-error' }}" style="font-size:0.6875rem;">{{ ucfirst($u->status) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="message">Broadcast Message Content</label>
                <textarea id="message" name="message" class="form-control" rows="6" placeholder="Write your broadcast announcement message here..." required>{{ old('message') }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg" onclick="return confirm('Are you ready to dispatch this broadcast message to the selected customer(s)?')">
                <i class="fa-solid fa-bullhorn"></i> Send Broadcast Now
            </button>
        </form>
    </div>

    <!-- Audience Quick Overview -->
    <div>
        <div class="mk-card" style="margin-bottom:24px;">
            <h4 style="font-size:1rem; font-weight:700; margin-bottom:16px;">Audience Reach</h4>

            <div style="display:flex; flex-direction:column; gap:12px; font-size:0.875rem;">
                <div style="display:flex; justify-content:space-between; padding-bottom:8px; border-bottom:1px solid rgba(145, 158, 171, 0.12);">
                    <span style="color:var(--palette-text-secondary);">Total Customers:</span>
                    <span style="font-weight:700;">{{ $totalCustomers }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding-bottom:8px; border-bottom:1px solid rgba(145, 158, 171, 0.12);">
                    <span style="color:var(--palette-text-secondary);">Active Accounts:</span>
                    <span style="font-weight:700; color:var(--palette-success-dark);">{{ $activeCustomers }}</span>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--palette-text-secondary);">SMTP Status:</span>
                    <span class="badge badge-success">Active & Ready</span>
                </div>
            </div>

            <div style="margin-top:16px; font-size:0.75rem; color:var(--palette-text-secondary); background:var(--palette-background-neutral); padding:12px; border-radius:8px;">
                <i class="fa-solid fa-circle-check" style="color:var(--palette-success-dark); margin-right:4px;"></i>
                You can tick specific customers, search by keyword, or select all with one click.
            </div>
        </div>
    </div>
</div>

<!-- Broadcast History Table -->
<div class="mk-card">
    <div class="mk-card-header">
        <h3 class="mk-card-title">Broadcast History & Log</h3>
    </div>

    <div class="table-responsive">
        <table class="mk-table">
            <thead>
                <tr>
                    <th>Date & Time</th>
                    <th>Title / Subject</th>
                    <th>Audience Filter</th>
                    <th>Channel</th>
                    <th>Delivered</th>
                    <th>Dispatched By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($campaigns as $camp)
                    <tr>
                        <td style="font-size:0.8125rem; color:var(--palette-text-secondary);">
                            {{ $camp->created_at->format('M d, Y h:i A') }}
                        </td>
                        <td style="font-weight:700;">{{ $camp->title }}</td>
                        <td>
                            <span class="badge badge-neutral" style="text-transform:capitalize;">{{ $camp->target_audience }}</span>
                        </td>
                        <td>
                            @if($camp->channel === 'both')
                                <span class="badge badge-info"><i class="fa-solid fa-envelope"></i> + <i class="fa-solid fa-bell"></i> Both</span>
                            @elseif($camp->channel === 'email')
                                <span class="badge badge-primary"><i class="fa-solid fa-envelope"></i> Email</span>
                            @else
                                <span class="badge badge-warning"><i class="fa-solid fa-bell"></i> Notification</span>
                            @endif
                        </td>
                        <td style="font-weight:700; color:var(--palette-success-dark);">{{ $camp->recipients_count }} Users</td>
                        <td style="font-size:0.8125rem;">{{ $camp->sender->name ?? 'Admin' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding:32px; color:var(--palette-text-secondary);">
                            No broadcast campaigns sent yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px;">
        {{ $campaigns->links() }}
    </div>
</div>

<script>
function toggleAudienceMode(val) {
    const container = document.getElementById('userSelectionContainer');
    if (val === 'selected') {
        container.style.display = 'block';
    } else {
        container.style.display = 'none';
    }
}

function filterUserList(query) {
    query = query.toLowerCase().trim();
    const rows = document.querySelectorAll('.user-row');
    rows.forEach(row => {
        const searchData = row.getAttribute('data-search');
        if (!query || searchData.includes(query)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function toggleSelectAllUsers(checked) {
    const checkboxes = document.querySelectorAll('.user-checkbox');
    checkboxes.forEach(cb => {
        // Only select currently visible rows in case of filtering
        const row = cb.closest('.user-row');
        if (row && row.style.display !== 'none') {
            cb.checked = checked;
        }
    });
    const master = document.getElementById('masterUserCheckbox');
    if (master) master.checked = checked;
    updateSelectedCount();
}

function toggleRowCheckbox(row, event) {
    // If click happened directly on checkbox, let default event proceed
    if (event.target.type === 'checkbox') return;
    const cb = row.querySelector('.user-checkbox');
    if (cb) {
        cb.checked = !cb.checked;
        updateSelectedCount();
    }
}

function updateSelectedCount() {
    const selected = document.querySelectorAll('.user-checkbox:checked').length;
    const badge = document.getElementById('selectedCounterBadge');
    if (badge) {
        badge.innerText = `${selected} Selected`;
    }
}

// Initial check on load
document.addEventListener('DOMContentLoaded', () => {
    const audienceSelect = document.getElementById('target_audience');
    if (audienceSelect && audienceSelect.value === 'selected') {
        toggleAudienceMode('selected');
    }
    updateSelectedCount();
});
</script>
@endsection
