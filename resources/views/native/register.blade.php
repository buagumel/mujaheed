<scroll-view class="w-full h-full bg-slate-50 p-6">
    <column class="w-full gap-6 pt-6 pb-12">
        <column class="items-center gap-2">
            <text class="text-2xl font-black text-slate-900 text-center">Create Free Account</text>
            <text class="text-xs font-semibold text-slate-500 text-center">Join thousands of customers enjoying fast recharge & data</text>
        </column>

        @if($errorMessage)
            <column class="w-full bg-red-50 border border-red-200 rounded-2xl p-4">
                <text class="text-xs font-bold text-red-600 text-center">{{ $errorMessage }}</text>
            </column>
        @endif

        <column class="w-full gap-3 bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
            <column class="gap-1">
                <text class="text-xs font-bold text-slate-700">Full Name</text>
                <text-input name="name" placeholder="e.g. John Doe" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm font-medium" />
            </column>

            <column class="gap-1">
                <text class="text-xs font-bold text-slate-700">Email Address</text>
                <text-input name="email" placeholder="john@example.com" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm font-medium" />
            </column>

            <column class="gap-1">
                <text class="text-xs font-bold text-slate-700">Phone Number</text>
                <text-input name="phone" placeholder="08012345678" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm font-medium" />
            </column>

            <column class="gap-1">
                <text class="text-xs font-bold text-slate-700">Password</text>
                <text-input name="password" placeholder="Min 8 characters" secure="true" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm font-medium" />
            </column>

            <column class="gap-1">
                <text class="text-xs font-bold text-slate-700">Confirm Password</text>
                <text-input name="password_confirmation" placeholder="Re-type password" secure="true" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm font-medium" />
            </column>

            <column class="gap-1">
                <text class="text-xs font-bold text-slate-700">Referral Code (Optional)</text>
                <text-input name="referral_code" placeholder="e.g. VTU123" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm font-medium uppercase font-mono" />
            </column>

            <button on-press="register" class="w-full bg-blue-600 rounded-2xl py-4 items-center justify-center shadow-md mt-2">
                <text class="text-white font-bold text-base">Register Now</text>
            </button>
        </column>

        <row class="items-center justify-center gap-1.5">
            <text class="text-sm font-medium text-slate-600">Already have an account?</text>
            <pressable on-press="goToLogin">
                <text class="text-sm font-bold text-blue-600">Sign In</text>
            </pressable>
        </row>
    </column>
</scroll-view>
