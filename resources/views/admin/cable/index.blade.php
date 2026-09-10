@extends('layouts.admin')

@section('title', 'Cable Packages')

@section('content')
<div class="mk-card">
    <div class="mk-card-header">
        <div>
            <h3 class="mk-card-title">Cable TV Packages</h3>
            <p class="mk-card-subtitle">Manage bouquets for DSTV, GOtv, and Startimes</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('createCableBox').style.display='block'">
            <i class="fa-solid fa-plus"></i> Add Package
        </button>
    </div>

    <!-- Create Package Box -->
    <div id="createCableBox" style="display:none; background:var(--palette-background-neutral); border:1px solid rgba(145, 158, 171, 0.2); border-radius:12px; padding:20px; margin-bottom:24px;">
        <h4 style="font-size:1rem; font-weight:700; margin-bottom:16px;">Add New Cable Package</h4>
        <form method="POST" action="{{ route('admin.cable.store') }}">
            @csrf
            <div class="grid-3">
                <div class="form-group">
                    <label class="form-label" for="provider">Provider</label>
                    <select id="provider" name="provider" class="form-select" required>
                        <option value="DSTV">DSTV</option>
                        <option value="GOTV">GOTV</option>
                        <option value="STARTIMES">STARTIMES</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="name">Package Name</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="GOtv Max" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="code">Package Code</label>
                    <input type="text" id="code" name="code" class="form-control" placeholder="gotv-max" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="provider_price">Cost Price (₦)</label>
                    <input type="number" id="provider_price" name="provider_price" class="form-control" step="0.01" placeholder="7200.00" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="selling_price">Selling Price (₦)</label>
                    <input type="number" id="selling_price" name="selling_price" class="form-control" step="0.01" placeholder="7350.00" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('createCableBox').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary btn-sm">Save Package</button>
            </div>
        </form>
    </div>

    <!-- Cable Packages Table -->
    <div class="table-responsive">
        <table class="mk-table">
            <thead>
                <tr>
                    <th>Provider</th>
                    <th>Package Name</th>
                    <th>Code</th>
                    <th>Provider Cost</th>
                    <th>Selling Price</th>
                    <th>Profit</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($plans as $cp)
                    <tr>
                        <td style="font-weight:700;">{{ $cp->provider }}</td>
                        <td>{{ $cp->name }}</td>
                        <td style="font-family:monospace; font-size:0.75rem;">{{ $cp->code }}</td>
                        <td>₦{{ number_format($cp->provider_price, 2) }}</td>
                        <td style="font-weight:700;">₦{{ number_format($cp->selling_price, 2) }}</td>
                        <td style="color:var(--palette-success-dark); font-weight:700;">
                            +₦{{ number_format($cp->selling_price - $cp->provider_price, 2) }}
                        </td>
                        <td>
                            <span class="badge {{ $cp->status === 'active' ? 'badge-success' : 'badge-error' }}">{{ $cp->status }}</span>
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
@endsection
