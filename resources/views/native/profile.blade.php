<column class="w-full h-full bg-slate-50 justify-between">
    <scroll-view class="w-full flex-1 p-5">
        <column class="w-full gap-5 pt-4 pb-24">
            <!-- Header -->
            <row class="w-full items-center justify-between">
                <text class="text-xl font-black text-slate-900">My Profile</text>
                <column class="bg-blue-100 px-3 py-1 rounded-full">
                    <text class="text-[10px] font-bold text-blue-700 uppercase">{{ $user->getTierDisplayName() }}</text>
                </column>
            </row>

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

            <!-- Avatar + Info Card -->
            <column class="w-full bg-gradient-to-br from-blue-600 to-indigo-700 p-5 rounded-3xl shadow-lg gap-3">
                <row class="items-center gap-4">
                    <column class="w-14 h-14 bg-white/20 rounded-2xl items-center justify-center">
                        <text class="text-2xl font-black text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</text>
                    </column>
                    <column class="gap-0.5">
                        <text class="text-base font-black text-white">{{ $user->name }}</text>
                        <text class="text-xs font-medium text-blue-100">{{ $user->email }}</text>
                        <text class="text-xs font-medium text-blue-200">{{ $user->phone }}</text>
                    </column>
                </row>
                <row class="items-center justify-between pt-2 border-t border-white/10">
                    <column>
                        <text class="text-[10px] font-medium text-blue-200">Wallet Balance</text>
                        <text class="text-lg font-black text-white">₦{{ number_format($walletBalance, 2) }}</text>
                    </column>
                    <column class="bg-white/10 px-3 py-1.5 rounded-xl">
                        <text class="text-[10px] font-bold text-white">{{ $user->referral_code ?? '—' }}</text>
                        <text class="text-[9px] font-medium text-blue-200">Referral Code</text>
                    </column>
                </row>
            </column>

            <!-- Account Settings -->
            <column class="w-full gap-2">
                <text class="text-xs font-black text-slate-500 uppercase tracking-wider px-1">Account Settings</text>

                <column class="w-full bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                    <!-- Edit Profile -->
                    <pressable on-press="toggleEditProfile" class="w-full p-4 border-b border-slate-50">
                        <row class="w-full items-center justify-between">
                            <row class="items-center gap-3">
                                <column class="w-8 h-8 bg-blue-50 rounded-lg items-center justify-center"><text class="text-sm">👤</text></column>
                                <text class="text-xs font-bold text-slate-900">Edit Profile</text>
                            </row>
                            <text class="text-xs font-bold text-slate-400">{{ $showEditProfile ? '▲' : '▶' }}</text>
                        </row>
                    </pressable>

                    @if($showEditProfile)
                        <column class="w-full p-4 bg-slate-50 gap-3 border-b border-slate-100">
                            <column class="gap-1"><text class="text-[10px] font-bold text-slate-600 uppercase">Full Name</text>
                                <text-input name="name" placeholder="Your name" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-xs font-medium" /></column>
                            <column class="gap-1"><text class="text-[10px] font-bold text-slate-600 uppercase">Phone</text>
                                <text-input name="phone" placeholder="08012345678" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-xs font-medium" /></column>
                            <button on-press="saveProfile" class="w-full bg-blue-600 rounded-xl py-3 items-center justify-center shadow-sm">
                                <text class="text-white font-bold text-xs">Save Changes</text>
                            </button>
                        </column>
                    @endif

                    <!-- Change Password -->
                    <pressable on-press="toggleChangePassword" class="w-full p-4 border-b border-slate-50">
                        <row class="w-full items-center justify-between">
                            <row class="items-center gap-3">
                                <column class="w-8 h-8 bg-amber-50 rounded-lg items-center justify-center"><text class="text-sm">🔑</text></column>
                                <text class="text-xs font-bold text-slate-900">Change Password</text>
                            </row>
                            <text class="text-xs font-bold text-slate-400">{{ $showChangePassword ? '▲' : '▶' }}</text>
                        </row>
                    </pressable>

                    @if($showChangePassword)
                        <column class="w-full p-4 bg-slate-50 gap-3 border-b border-slate-100">
                            <column class="gap-1"><text class="text-[10px] font-bold text-slate-600 uppercase">Current Password</text>
                                <text-input name="currentPassword" placeholder="••••••••" secure="true" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-xs font-medium" /></column>
                            <column class="gap-1"><text class="text-[10px] font-bold text-slate-600 uppercase">New Password</text>
                                <text-input name="newPassword" placeholder="Min 8 characters" secure="true" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-xs font-medium" /></column>
                            <column class="gap-1"><text class="text-[10px] font-bold text-slate-600 uppercase">Confirm New Password</text>
                                <text-input name="newPasswordConfirm" placeholder="Re-enter new password" secure="true" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-xs font-medium" /></column>
                            <button on-press="savePassword" class="w-full bg-amber-500 rounded-xl py-3 items-center justify-center shadow-sm">
                                <text class="text-white font-bold text-xs">Update Password</text>
                            </button>
                        </column>
                    @endif

                    <!-- Transaction PIN -->
                    @if(!$hasPin)
                        <pressable on-press="toggleSetPin" class="w-full p-4 border-b border-slate-50">
                            <row class="w-full items-center justify-between">
                                <row class="items-center gap-3">
                                    <column class="w-8 h-8 bg-emerald-50 rounded-lg items-center justify-center"><text class="text-sm">🔐</text></column>
                                    <column>
                                        <text class="text-xs font-bold text-slate-900">Set Transaction PIN</text>
                                        <text class="text-[9px] font-medium text-amber-600">Not yet configured</text>
                                    </column>
                                </row>
                                <text class="text-xs font-bold text-slate-400">{{ $showSetPin ? '▲' : '▶' }}</text>
                            </row>
                        </pressable>

                        @if($showSetPin)
                            <column class="w-full p-4 bg-slate-50 gap-3 border-b border-slate-100">
                                <column class="gap-1"><text class="text-[10px] font-bold text-slate-600 uppercase">Enter 4-Digit PIN</text>
                                    <text-input name="pinStep1" placeholder="••••" secure="true" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-center text-lg font-black font-mono tracking-[1rem]" /></column>
                                <column class="gap-1"><text class="text-[10px] font-bold text-slate-600 uppercase">Confirm PIN</text>
                                    <text-input name="pinStep2" placeholder="••••" secure="true" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-center text-lg font-black font-mono tracking-[1rem]" /></column>
                                <button on-press="savePin" class="w-full bg-emerald-600 rounded-xl py-3 items-center justify-center shadow-sm">
                                    <text class="text-white font-bold text-xs">Set PIN</text>
                                </button>
                            </column>
                        @endif
                    @else
                        <pressable on-press="toggleChangePin" class="w-full p-4 border-b border-slate-50">
                            <row class="w-full items-center justify-between">
                                <row class="items-center gap-3">
                                    <column class="w-8 h-8 bg-emerald-50 rounded-lg items-center justify-center"><text class="text-sm">🔐</text></column>
                                    <column>
                                        <text class="text-xs font-bold text-slate-900">Change Transaction PIN</text>
                                        <text class="text-[9px] font-medium text-emerald-600">✓ PIN Active</text>
                                    </column>
                                </row>
                                <text class="text-xs font-bold text-slate-400">{{ $showChangePin ? '▲' : '▶' }}</text>
                            </row>
                        </pressable>

                        @if($showChangePin)
                            <column class="w-full p-4 bg-slate-50 gap-3 border-b border-slate-100">
                                <column class="gap-1"><text class="text-[10px] font-bold text-slate-600 uppercase">Current PIN</text>
                                    <text-input name="currentPin" placeholder="••••" secure="true" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-center text-lg font-black font-mono tracking-[1rem]" /></column>
                                <column class="gap-1"><text class="text-[10px] font-bold text-slate-600 uppercase">New PIN</text>
                                    <text-input name="pinStep1" placeholder="••••" secure="true" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-center text-lg font-black font-mono tracking-[1rem]" /></column>
                                <column class="gap-1"><text class="text-[10px] font-bold text-slate-600 uppercase">Confirm New PIN</text>
                                    <text-input name="pinStep2" placeholder="••••" secure="true" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-center text-lg font-black font-mono tracking-[1rem]" /></column>
                                <button on-press="changePin" class="w-full bg-blue-600 rounded-xl py-3 items-center justify-center shadow-sm">
                                    <text class="text-white font-bold text-xs">Update PIN</text>
                                </button>
                            </column>
                        @endif
                    @endif
                </column>
            </column>

            <!-- More Options -->
            <column class="w-full gap-2">
                <text class="text-xs font-black text-slate-500 uppercase tracking-wider px-1">More</text>
                <column class="w-full bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                    @foreach([
                        ['Refer & Earn', '🎁', 'goToReferrals'],
                        ['Upgrade Tier', '👑', 'goToTiers'],
                        ['Airtime to Cash', '🔄', 'goToAirtimeCash'],
                        ['Support Center', '💬', 'goToSupport'],
                    ] as [$label, $emoji, $action])
                        <pressable on-press="{{ $action }}" class="w-full p-4 border-b border-slate-50">
                            <row class="w-full items-center justify-between">
                                <row class="items-center gap-3">
                                    <column class="w-8 h-8 bg-slate-50 rounded-lg items-center justify-center"><text class="text-sm">{{ $emoji }}</text></column>
                                    <text class="text-xs font-bold text-slate-900">{{ $label }}</text>
                                </row>
                                <text class="text-xs font-bold text-slate-400">▶</text>
                            </row>
                        </pressable>
                    @endforeach
                </column>
            </column>

            <!-- Logout -->
            <button on-press="logout" class="w-full bg-red-50 border border-red-200 rounded-2xl py-4 items-center justify-center mt-2">
                <text class="text-sm font-bold text-red-600">Sign Out</text>
            </button>
        </column>
    </scroll-view>

    <!-- Bottom Nav -->
    <row class="w-full bg-white border-t border-slate-100 py-3 px-6 justify-between items-center shadow-lg">
        <pressable on-press="goToHome" class="items-center gap-1"><text class="text-lg text-slate-400">🏠</text><text class="text-[10px] font-semibold text-slate-500">Home</text></pressable>
        <pressable on-press="goToWallet" class="items-center gap-1"><text class="text-lg text-slate-400">💼</text><text class="text-[10px] font-semibold text-slate-500">Wallet</text></pressable>
        <pressable on-press="goToTransactions" class="items-center gap-1"><text class="text-lg text-slate-400">📊</text><text class="text-[10px] font-semibold text-slate-500">History</text></pressable>
        <pressable on-press="goToProfile" class="items-center gap-1"><text class="text-lg text-blue-600">👤</text><text class="text-[10px] font-bold text-blue-600">Profile</text></pressable>
    </row>
</column>
