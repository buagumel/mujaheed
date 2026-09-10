<scroll-view class="w-full h-full bg-slate-50 p-6">
    <column class="w-full gap-6 pt-10">
        <!-- Logo & Header -->
        <column class="items-center gap-3">
            <column class="w-16 h-16 bg-blue-600 rounded-2xl items-center justify-center shadow-md">
                <text class="text-3xl font-extrabold text-white">⚡</text>
            </column>
            <text class="text-2xl font-black text-slate-900 text-center">{{ $platformName }}</text>
            <text class="text-sm font-semibold text-slate-500 text-center">Sign in to your account to continue</text>
        </column>

        @if($errorMessage)
            <column class="w-full bg-red-50 border border-red-200 rounded-2xl p-4">
                <text class="text-xs font-bold text-red-600 text-center">{{ $errorMessage }}</text>
            </column>
        @endif

        <!-- Form -->
        <column class="w-full gap-4 bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
            <column class="gap-1.5">
                <text class="text-xs font-bold text-slate-700 uppercase tracking-wider">Email or Phone Number</text>
                <text-input name="email" placeholder="e.g. name@domain.com or 08012345678" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-sm font-medium text-slate-900" />
            </column>

            <column class="gap-1.5">
                <text class="text-xs font-bold text-slate-700 uppercase tracking-wider">Password</text>
                <text-input name="password" placeholder="••••••••••••" secure="true" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-sm font-medium text-slate-900" />
            </column>

            <row class="justify-end py-1">
                <pressable on-press="goToForgotPassword">
                    <text class="text-xs font-bold text-blue-600">Forgot Password?</text>
                </pressable>
            </row>

            <button on-press="login" class="w-full bg-blue-600 rounded-2xl py-4 items-center justify-center shadow-md">
                <text class="text-white font-bold text-base">Sign In</text>
            </button>
        </column>

        <!-- Footer link -->
        <row class="items-center justify-center gap-1.5 py-4">
            <text class="text-sm font-medium text-slate-600">Don't have an account?</text>
            <pressable on-press="goToRegister">
                <text class="text-sm font-bold text-blue-600">Create Account</text>
            </pressable>
        </row>
    </column>
</scroll-view>
