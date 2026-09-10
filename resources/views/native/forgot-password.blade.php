<scroll-view class="w-full h-full bg-slate-50 p-6">
    <column class="w-full gap-6 pt-10">
        <column class="items-center gap-2">
            <column class="w-16 h-16 bg-blue-100 rounded-2xl items-center justify-center">
                <text class="text-3xl font-extrabold text-blue-600">🔑</text>
            </column>
            <text class="text-2xl font-black text-slate-900 text-center">Reset Password</text>
            <text class="text-xs font-semibold text-slate-500 text-center">Enter your registered email to receive a recovery code</text>
        </column>

        @if($errorMessage)
            <column class="w-full bg-red-50 border border-red-200 rounded-2xl p-4">
                <text class="text-xs font-bold text-red-600 text-center">{{ $errorMessage }}</text>
            </column>
        @endif

        @if($successMessage)
            <column class="w-full bg-emerald-50 border border-emerald-200 rounded-2xl p-4">
                <text class="text-xs font-bold text-emerald-700 text-center">{{ $successMessage }}</text>
            </column>
        @endif

        @if($step === 1)
            <column class="w-full gap-4 bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <column class="gap-1.5">
                    <text class="text-xs font-bold text-slate-700 uppercase">Your Account Email</text>
                    <text-input name="email" placeholder="name@domain.com" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-sm font-medium" />
                </column>

                <button on-press="sendOtp" class="w-full bg-blue-600 rounded-2xl py-4 items-center justify-center shadow-md">
                    <text class="text-white font-bold text-base">Send Reset OTP</text>
                </button>
            </column>
        @else
            <column class="w-full gap-3 bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <column class="gap-1">
                    <text class="text-xs font-bold text-slate-700 uppercase">6-Digit Verification Code</text>
                    <text-input name="otp" placeholder="123456" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-center text-lg font-bold font-mono tracking-widest" />
                </column>

                <column class="gap-1">
                    <text class="text-xs font-bold text-slate-700 uppercase">New Password</text>
                    <text-input name="password" placeholder="Min 8 characters" secure="true" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm font-medium" />
                </column>

                <column class="gap-1">
                    <text class="text-xs font-bold text-slate-700 uppercase">Confirm New Password</text>
                    <text-input name="password_confirmation" placeholder="Re-enter password" secure="true" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm font-medium" />
                </column>

                <button on-press="resetPassword" class="w-full bg-blue-600 rounded-2xl py-4 items-center justify-center shadow-md mt-2">
                    <text class="text-white font-bold text-base">Update Password & Login</text>
                </button>
            </column>
        @endif

        <row class="items-center justify-center gap-1.5 py-4">
            <pressable on-press="goToLogin">
                <text class="text-sm font-bold text-blue-600">← Back to Sign In</text>
            </pressable>
        </row>
    </column>
</scroll-view>
