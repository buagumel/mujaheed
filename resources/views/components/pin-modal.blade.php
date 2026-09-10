<!-- Transaction PIN Confirmation Modal Component -->
<div id="transactionPinModal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(15, 23, 42, 0.65); backdrop-filter:blur(4px); align-items:center; justify-content:center; padding:16px;">
    <div style="background:#ffffff; border-radius:16px; max-width:400px; width:100%; padding:24px; box-shadow:0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04); border:1px solid rgba(145,158,171,0.2); animation: pinModalPop 0.25s cubic-bezier(0.4, 0, 0.2, 1);">
        <div style="text-align:center; margin-bottom:20px;">
            <div style="width:54px; height:54px; border-radius:50%; background:rgba(24, 119, 242, 0.1); color:var(--palette-primary-main, #1877f2); display:flex; align-items:center; justify-content:center; font-size:1.5rem; margin:0 auto 12px;">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <h3 style="font-size:1.25rem; font-weight:800; color:var(--palette-text-primary, #1c252e); margin-bottom:4px;">Security PIN Required</h3>
            <p style="font-size:0.85rem; color:var(--palette-text-secondary, #637381);">Enter your 4-digit transaction PIN to authorize this request.</p>
        </div>

        <form id="pinModalForm" onsubmit="submitWithPin(event)">
            <input type="hidden" name="pin" id="modal_pin_hidden">

            <!-- In-Card Error Alert Banner -->
            <div id="pinModalErrorAlert" style="display:none; background:rgba(255, 86, 48, 0.12); color:var(--palette-error-main, #FF5630); border:1px solid rgba(255, 86, 48, 0.24); padding:10px 14px; border-radius:10px; font-size:0.8125rem; font-weight:700; margin-bottom:18px; align-items:center; gap:8px;">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span id="pinModalErrorText" style="flex-grow:1;">Invalid Transaction PIN.</span>
            </div>

            <div style="display:flex; justify-content:center; gap:12px; margin-bottom:24px;">
                <input type="password" maxlength="1" class="pin-digit-input" id="pin_digit_1" autofocus onkeyup="movePinFocus(1, event)" style="width:50px; height:56px; font-size:1.5rem; text-align:center; border-radius:10px; border:2px solid rgba(145,158,171,0.24); font-weight:800; font-family:monospace;">
                <input type="password" maxlength="1" class="pin-digit-input" id="pin_digit_2" onkeyup="movePinFocus(2, event)" style="width:50px; height:56px; font-size:1.5rem; text-align:center; border-radius:10px; border:2px solid rgba(145,158,171,0.24); font-weight:800; font-family:monospace;">
                <input type="password" maxlength="1" class="pin-digit-input" id="pin_digit_3" onkeyup="movePinFocus(3, event)" style="width:50px; height:56px; font-size:1.5rem; text-align:center; border-radius:10px; border:2px solid rgba(145,158,171,0.24); font-weight:800; font-family:monospace;">
                <input type="password" maxlength="1" class="pin-digit-input" id="pin_digit_4" onkeyup="movePinFocus(4, event)" style="width:50px; height:56px; font-size:1.5rem; text-align:center; border-radius:10px; border:2px solid rgba(145,158,171,0.24); font-weight:800; font-family:monospace;">
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                <button type="button" class="btn btn-outline" onclick="closePinModal()" style="font-weight:700;">Cancel</button>
                <button type="submit" class="btn btn-primary" id="confirmPinBtn" style="font-weight:700;">
                    <i class="fa-solid fa-check"></i> Authorize
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes pinModalPop {
        from { opacity:0; transform:scale(0.92); }
        to { opacity:1; transform:scale(1); }
    }
    .pin-digit-input:focus {
        border-color: var(--palette-primary-main, #1877f2) !important;
        box-shadow: 0 0 0 3px rgba(24, 119, 242, 0.16) !important;
        outline: none;
    }
</style>

<script>
    let activeTargetForm = null;

    function openPinModal(formElement) {
        if (formElement && !formElement.reportValidity()) {
            return;
        }

        activeTargetForm = formElement;
        document.getElementById('pin_digit_1').value = '';
        document.getElementById('pin_digit_2').value = '';
        document.getElementById('pin_digit_3').value = '';
        document.getElementById('pin_digit_4').value = '';
        document.getElementById('modal_pin_hidden').value = '';
        
        hidePinModalError();
        
        const modal = document.getElementById('transactionPinModal');
        modal.style.display = 'flex';
        setTimeout(() => document.getElementById('pin_digit_1').focus(), 100);
    }

    function closePinModal() {
        document.getElementById('transactionPinModal').style.display = 'none';
        hidePinModalError();
        activeTargetForm = null;
    }

    function showPinModalError(msg) {
        const box = document.getElementById('pinModalErrorAlert');
        const text = document.getElementById('pinModalErrorText');
        if (box && text) {
            text.innerText = msg;
            box.style.display = 'flex';
        }
    }

    function hidePinModalError() {
        const box = document.getElementById('pinModalErrorAlert');
        if (box) box.style.display = 'none';
    }

    function movePinFocus(index, event) {
        const key = event.key;
        if (key === 'Backspace') {
            if (index > 1) {
                document.getElementById('pin_digit_' + (index - 1)).focus();
            }
            return;
        }

        const val = event.target.value;
        if (val && index < 4) {
            document.getElementById('pin_digit_' + (index + 1)).focus();
        }
    }

    function submitWithPin(event) {
        event.preventDefault();
        hidePinModalError();

        const d1 = document.getElementById('pin_digit_1').value;
        const d2 = document.getElementById('pin_digit_2').value;
        const d3 = document.getElementById('pin_digit_3').value;
        const d4 = document.getElementById('pin_digit_4').value;

        const pin = d1 + d2 + d3 + d4;

        if (pin.length !== 4) {
            showPinModalError('Please enter your complete 4-digit PIN.');
            return;
        }

        if (!activeTargetForm) return;

        const confirmBtn = document.getElementById('confirmPinBtn');
        const origText = confirmBtn.innerHTML;
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Verifying...';

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        // Perform AJAX pre-verification of PIN
        fetch('/security/verify-pin', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ pin: pin })
        })
        .then(res => res.json())
        .then(data => {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = origText;

            if (data.success) {
                let hiddenPinInput = activeTargetForm.querySelector('input[name="pin"]');
                if (!hiddenPinInput) {
                    hiddenPinInput = document.createElement('input');
                    hiddenPinInput.type = 'hidden';
                    hiddenPinInput.name = 'pin';
                    activeTargetForm.appendChild(hiddenPinInput);
                }
                hiddenPinInput.value = pin;
                closePinModal();
                activeTargetForm.submit();
            } else {
                showPinModalError(data.message || 'Invalid Transaction PIN.');
                document.getElementById('pin_digit_1').value = '';
                document.getElementById('pin_digit_2').value = '';
                document.getElementById('pin_digit_3').value = '';
                document.getElementById('pin_digit_4').value = '';
                document.getElementById('pin_digit_1').focus();
            }
        })
        .catch(err => {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = origText;
            showPinModalError('Network error verifying PIN. Please try again.');
        });
    }
</script>
