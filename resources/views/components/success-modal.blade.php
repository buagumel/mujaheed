<!-- Custom Clean Floating Success Alert Popup (100% No Background Tint, No Blur) -->
<div id="mkSuccessModal" class="mk-popup-overlay" style="display:none;">
    <div class="mk-popup-container">
        <!-- Close Button -->
        <button type="button" class="mk-popup-close-btn" onclick="closeSuccessModal()" aria-label="Close">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <!-- Animated Clean Success Badge -->
        <div class="mk-success-illustration-wrapper">
            <div class="mk-success-ripple-ring ring-1"></div>
            <div class="mk-success-ripple-ring ring-2"></div>
            <div class="mk-success-badge">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22 11.08V12C21.9988 14.1564 21.3005 16.2547 20.0093 17.9818C18.7182 19.709 16.9033 20.9725 14.8354 21.5839C12.7674 22.1953 10.5573 22.1219 8.53447 21.3746C6.51168 20.6273 4.78465 19.2461 3.61096 17.4371C2.43727 15.628 1.87979 13.4881 2.02168 11.3363C2.16356 9.18455 2.99721 7.13631 4.39828 5.49706C5.79935 3.85781 7.69279 2.71537 9.79619 2.24013C11.8996 1.7649 14.1003 1.98232 16.07 2.85999" stroke="#FFFFFF" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M22 4L12 14.01L9 11.01" stroke="#FFFFFF" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        <!-- Success Title & Content -->
        <h3 id="mkSuccessTitle" class="mk-success-title">Success!</h3>
        <p id="mkSuccessMessage" class="mk-success-message">Your operation was completed successfully.</p>

        <!-- Optional Transaction Details Box -->
        <div id="mkSuccessDetailsBox" class="mk-success-details" style="display:none;">
            <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                <span style="color:var(--palette-text-secondary); font-size:0.8125rem;">Reference:</span>
                <span id="mkSuccessRef" style="font-weight:700; font-family:monospace; font-size:0.8125rem;"></span>
            </div>
            <div style="display:flex; justify-content:space-between;">
                <span style="color:var(--palette-text-secondary); font-size:0.8125rem;">Amount:</span>
                <span id="mkSuccessAmount" style="font-weight:800; color:var(--palette-primary-main); font-size:0.9375rem;"></span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mk-success-actions">
            <a id="mkSuccessReceiptBtn" href="#" class="btn btn-outline btn-block btn-lg" style="display:none; font-weight:700;">
                <i class="fa-solid fa-receipt"></i> View Receipt
            </a>
            <button type="button" id="mkSuccessCloseBtn" class="btn btn-primary btn-block btn-lg" onclick="closeSuccessModal()">
                <i class="fa-solid fa-check"></i> Continue
            </button>
        </div>
    </div>
</div>

<style>
/* Clean Floating Popup Container (Zero Background, Zero Dark Tint, Zero Blur) */
.mk-popup-overlay {
    position: fixed;
    inset: 0;
    z-index: 1000000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    background: transparent !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
    pointer-events: none;
}

.mk-popup-container {
    position: relative;
    z-index: 2;
    width: 100%;
    max-width: 400px;
    background: #FFFFFF;
    border-radius: 20px;
    box-shadow: 0 16px 40px -4px rgba(0, 0, 0, 0.18), 0 0 0 1px rgba(145, 158, 171, 0.24);
    padding: 30px 24px 22px;
    text-align: center;
    pointer-events: auto;
    animation: mkPopupPop 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}

.mk-popup-close-btn {
    position: absolute;
    top: 14px;
    right: 14px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: rgba(145, 158, 171, 0.12);
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--palette-text-secondary);
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.8125rem;
}

.mk-popup-close-btn:hover {
    background: rgba(145, 158, 171, 0.24);
    color: var(--palette-text-primary);
    transform: rotate(90deg);
}

/* Success Illustration Wrapper & Animated Pulse */
.mk-success-illustration-wrapper {
    position: relative;
    width: 80px;
    height: 80px;
    margin: 0 auto 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.mk-success-ripple-ring {
    position: absolute;
    border-radius: 50%;
    background: rgba(34, 197, 94, 0.14);
    animation: mkSuccessRipple 2.2s cubic-bezier(0.25, 1, 0.5, 1) infinite;
}

.mk-success-ripple-ring.ring-1 {
    width: 80px;
    height: 80px;
}

.mk-success-ripple-ring.ring-2 {
    width: 98px;
    height: 98px;
    animation-delay: 0.4s;
}

.mk-success-badge {
    position: relative;
    z-index: 2;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #22C55E 0%, #118D57 100%);
    box-shadow: 0 10px 20px -4px rgba(34, 197, 94, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    animation: mkBadgeBounce 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}

.mk-success-title {
    font-size: 1.25rem;
    font-weight: 800;
    color: var(--palette-text-primary);
    margin-bottom: 6px;
    letter-spacing: -0.2px;
}

.mk-success-message {
    font-size: 0.875rem;
    color: var(--palette-text-secondary);
    line-height: 1.5;
    margin-bottom: 18px;
}

.mk-success-details {
    background: var(--palette-background-neutral);
    border-radius: 12px;
    padding: 12px 14px;
    margin-bottom: 18px;
    border: 1px solid rgba(145, 158, 171, 0.16);
    text-align: left;
}

.mk-success-actions {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

@keyframes mkPopupPop {
    from { opacity: 0; transform: scale(0.9) translateY(16px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}

@keyframes mkSuccessRipple {
    0% { transform: scale(0.85); opacity: 0.8; }
    100% { transform: scale(1.35); opacity: 0; }
}

@keyframes mkBadgeBounce {
    0% { transform: scale(0); }
    60% { transform: scale(1.12); }
    100% { transform: scale(1); }
}
</style>

<script>
window.showSuccessPopup = function(options) {
    const modal = document.getElementById('mkSuccessModal');
    if (!modal) return;

    options = options || {};
    const title = options.title || 'Success!';
    const message = options.message || 'Action completed successfully.';
    const buttonText = options.buttonText || 'Continue';
    const receiptUrl = options.receiptUrl || null;
    const ref = options.ref || null;
    const amount = options.amount || null;

    document.getElementById('mkSuccessTitle').innerText = title;
    document.getElementById('mkSuccessMessage').innerText = message;
    document.getElementById('mkSuccessCloseBtn').innerHTML = `<i class="fa-solid fa-check"></i> ${buttonText}`;

    const detailsBox = document.getElementById('mkSuccessDetailsBox');
    if (ref || amount) {
        detailsBox.style.display = 'block';
        document.getElementById('mkSuccessRef').innerText = ref || '';
        document.getElementById('mkSuccessAmount').innerText = amount || '';
    } else {
        detailsBox.style.display = 'none';
    }

    const receiptBtn = document.getElementById('mkSuccessReceiptBtn');
    if (receiptUrl) {
        receiptBtn.style.display = 'inline-flex';
        receiptBtn.href = receiptUrl;
    } else {
        receiptBtn.style.display = 'none';
    }

    modal.style.display = 'flex';
};

window.closeSuccessModal = function() {
    const modal = document.getElementById('mkSuccessModal');
    if (modal) {
        modal.style.display = 'none';
    }
};

// Automatic Trigger on Session Flash Success (Only on authenticated dashboard / platform views)
document.addEventListener('DOMContentLoaded', function() {
    @if(auth()->check() && session('success'))
        window.showSuccessPopup({
            title: 'Action Successful!',
            message: "{{ addslashes(session('success')) }}",
            receiptUrl: "{{ session('receipt_url') ? url(session('receipt_url')) : '' }}",
            ref: "{{ session('transaction_ref') ?? '' }}",
            amount: "{{ session('transaction_amount') ? '₦' . number_format(session('transaction_amount'), 2) : '' }}"
        });
    @endif
});
</script>
