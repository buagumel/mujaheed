@extends('layouts.app')

@section('title', 'Open Support Ticket')
@section('header_title', 'Create Support Ticket')

@section('content')
<div style="max-width: 760px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('tickets.index') }}" class="btn btn-outline btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Back to Tickets
        </a>
    </div>

    <div class="mk-card">
        <div class="mk-card-header">
            <div>
                <h3 class="mk-card-title">Submit a New Support Request</h3>
                <p class="mk-card-subtitle">Our technical team will review and reply to you as soon as possible</p>
            </div>
        </div>

        <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="form-label" for="subject">Ticket Subject / Title</label>
                <input type="text" id="subject" name="subject" class="form-control" placeholder="e.g. Delayed data delivery for MTN 08012345678" value="{{ old('subject') }}" required>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label" for="category">Category / Service</label>
                    <select id="category" name="category" class="form-select" required>
                        <option value="general">General Inquiry</option>
                        <option value="airtime">Airtime Recharge</option>
                        <option value="data">Data Bundle</option>
                        <option value="electricity">Electricity Bill / Token</option>
                        <option value="cable">Cable TV Subscription</option>
                        <option value="billing">Wallet Deposit / Billing</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="priority">Priority Level</label>
                    <select id="priority" name="priority" class="form-select" required>
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="message">Detailed Explanation</label>
                <textarea id="message" name="message" class="form-control" rows="5" placeholder="Please describe your issue in detail, including phone numbers, transaction references, or error messages..." required>{{ old('message') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="attachment">Attachment / Screenshot (Optional)</label>
                <input type="file" id="attachment" name="attachment" class="form-control" accept="image/png,image/jpeg,image/webp">
                <span style="font-size:0.75rem; color:var(--palette-text-secondary); margin-top:4px; display:block;">Supported: JPG, PNG, WEBP (Max: 3MB)</span>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:20px;">
                <i class="fa-solid fa-paper-plane"></i> Submit Support Ticket
            </button>
        </form>
    </div>
</div>
@endsection
