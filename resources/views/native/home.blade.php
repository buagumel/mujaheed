<column class="w-full h-full bg-slate-50 justify-between">
    <scroll-view class="w-full flex-1 p-5">
        <column class="w-full gap-5 pt-4 pb-20">
            <!-- Header Bar -->
            <row class="w-full items-center justify-between">
                <row class="items-center gap-3">
                    <column class="w-11 h-11 bg-blue-100 rounded-full items-center justify-center border-2 border-blue-200">
                        <text class="text-base font-black text-blue-700">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</text>
                    </column>
                    <column>
                        <text class="text-xs font-semibold text-slate-500">Welcome back,</text>
                        <text class="text-base font-black text-slate-900">{{ $user->name ?? 'Customer' }}</text>
                    </column>
                </row>

                <pressable on-press="goToNotifications" class="w-10 h-10 bg-white rounded-full items-center justify-center border border-slate-200 shadow-sm relative">
                    <text class="text-base">🔔</text>
                    @if($unreadNotifications > 0)
                        <column class="w-4 h-4 bg-red-500 rounded-full items-center justify-center absolute -top-1 -right-1">
                            <text class="text-[9px] font-black text-white">{{ $unreadNotifications > 9 ? '9+' : $unreadNotifications }}</text>
                        </column>
                    @endif
                </pressable>
            </row>

            <!-- Wallet Card (Material Blue Gradient) -->
            <column class="w-full bg-gradient-to-br from-blue-600 to-indigo-700 rounded-3xl p-6 shadow-lg shadow-blue-500/20 gap-4">
                <row class="items-center justify-between">
                    <row class="items-center gap-2">
                        <text class="text-xs font-bold text-blue-100 uppercase tracking-wider">Available Balance</text>
                        <pressable on-press="toggleBalance">
                            <text class="text-xs text-blue-200">{{ $showBalance ? '👁️' : '🔒' }}</text>
                        </pressable>
                    </row>
                    <column class="bg-white/20 backdrop-blur-md px-3 py-1 rounded-full">
                        <text class="text-xs font-bold text-white uppercase">{{ $user->getTierDisplayName() }}</text>
                    </column>
                </row>

                <row class="items-baseline gap-1">
                    <text class="text-3xl font-black text-white tracking-tight">
                        {{ $showBalance ? '₦' . number_format($balance, 2) : '₦••••••••' }}
                    </text>
                </row>

                <row class="items-center justify-between pt-2 border-t border-white/10">
                    <pressable on-press="goToFundWallet" class="bg-white rounded-xl px-4 py-2.5 items-center justify-center shadow-sm">
                        <text class="text-xs font-black text-blue-700">+ Add Money</text>
                    </pressable>

                    <pressable on-press="goToWallet" class="bg-white/10 rounded-xl px-4 py-2.5 items-center justify-center">
                        <text class="text-xs font-bold text-white">Bank Accounts →</text>
                    </pressable>
                </row>
            </column>

            <!-- Announcement Banner if active -->
            @if($announcement)
                <row class="w-full bg-amber-50 border border-amber-200 rounded-2xl p-3.5 items-center gap-3">
                    <text class="text-base">📢</text>
                    <text class="text-xs font-semibold text-amber-800 flex-1">{{ $announcement }}</text>
                </row>
            @endif

            <!-- Quick Services Section -->
            <column class="w-full gap-3">
                <text class="text-sm font-black text-slate-800 uppercase tracking-wider">Quick Services</text>

                <!-- Grid 4 Services -->
                <row class="w-full justify-between gap-3">
                    <pressable on-press="goToAirtime" class="flex-1 bg-white p-4 rounded-2xl border border-slate-100 shadow-sm items-center gap-2">
                        <column class="w-12 h-12 bg-blue-50 rounded-xl items-center justify-center">
                            <text class="text-2xl">📱</text>
                        </column>
                        <text class="text-xs font-bold text-slate-900">Airtime</text>
                        <text class="text-[10px] font-semibold text-emerald-600">Cashback</text>
                    </pressable>

                    <pressable on-press="goToData" class="flex-1 bg-white p-4 rounded-2xl border border-slate-100 shadow-sm items-center gap-2">
                        <column class="w-12 h-12 bg-blue-50 rounded-xl items-center justify-center">
                            <text class="text-2xl">📶</text>
                        </column>
                        <text class="text-xs font-bold text-slate-900">Data Bundle</text>
                        <text class="text-[10px] font-semibold text-blue-600">SME & Direct</text>
                    </pressable>

                    <pressable on-press="goToElectricity" class="flex-1 bg-white p-4 rounded-2xl border border-slate-100 shadow-sm items-center gap-2">
                        <column class="w-12 h-12 bg-blue-50 rounded-xl items-center justify-center">
                            <text class="text-2xl">💡</text>
                        </column>
                        <text class="text-xs font-bold text-slate-900">Electricity</text>
                        <text class="text-[10px] font-semibold text-amber-600">Instant Token</text>
                    </pressable>

                    <pressable on-press="goToCable" class="flex-1 bg-white p-4 rounded-2xl border border-slate-100 shadow-sm items-center gap-2">
                        <column class="w-12 h-12 bg-blue-50 rounded-xl items-center justify-center">
                            <text class="text-2xl">📺</text>
                        </column>
                        <text class="text-xs font-bold text-slate-900">Cable TV</text>
                        <text class="text-[10px] font-semibold text-purple-600">DSTV/GOTV</text>
                    </pressable>
                </row>

                <!-- Additional 4 Services -->
                <row class="w-full justify-between gap-3">
                    <pressable on-press="goToExamPins" class="flex-1 bg-white p-4 rounded-2xl border border-slate-100 shadow-sm items-center gap-2">
                        <column class="w-12 h-12 bg-blue-50 rounded-xl items-center justify-center">
                            <text class="text-2xl">🎓</text>
                        </column>
                        <text class="text-xs font-bold text-slate-900">Exam PINs</text>
                        <text class="text-[10px] font-semibold text-blue-600">WAEC/NECO</text>
                    </pressable>

                    <pressable on-press="goToAirtimeCash" class="flex-1 bg-white p-4 rounded-2xl border border-slate-100 shadow-sm items-center gap-2">
                        <column class="w-12 h-12 bg-blue-50 rounded-xl items-center justify-center">
                            <text class="text-2xl">🔄</text>
                        </column>
                        <text class="text-xs font-bold text-slate-900">Airtime Swap</text>
                        <text class="text-[10px] font-semibold text-emerald-600">To Real Cash</text>
                    </pressable>

                    <pressable on-press="goToTiers" class="flex-1 bg-white p-4 rounded-2xl border border-slate-100 shadow-sm items-center gap-2">
                        <column class="w-12 h-12 bg-blue-50 rounded-xl items-center justify-center">
                            <text class="text-2xl">👑</text>
                        </column>
                        <text class="text-xs font-bold text-slate-900">Reseller Tier</text>
                        <text class="text-[10px] font-semibold text-amber-600">Wholesale</text>
                    </pressable>

                    <pressable on-press="goToReferrals" class="flex-1 bg-white p-4 rounded-2xl border border-slate-100 shadow-sm items-center gap-2">
                        <column class="w-12 h-12 bg-blue-50 rounded-xl items-center justify-center">
                            <text class="text-2xl">🎁</text>
                        </column>
                        <text class="text-xs font-bold text-slate-900">Refer & Earn</text>
                        <text class="text-[10px] font-semibold text-rose-600">₦ Bonus</text>
                    </pressable>
                </row>
            </column>

            <!-- Recent Activity Section -->
            <column class="w-full gap-3">
                <row class="w-full items-center justify-between">
                    <text class="text-sm font-black text-slate-800 uppercase tracking-wider">Recent Activity</text>
                    <pressable on-press="goToTransactions">
                        <text class="text-xs font-bold text-blue-600">View All →</text>
                    </pressable>
                </row>

                @if(empty($recentTransactions))
                    <column class="w-full bg-white rounded-2xl p-6 items-center justify-center gap-2 border border-slate-100">
                        <text class="text-2xl">📋</text>
                        <text class="text-xs font-semibold text-slate-400">No transactions recorded yet</text>
                    </column>
                @else
                    <column class="w-full gap-2">
                        @foreach($recentTransactions as $tx)
                            <pressable on-press="goToTransaction('{{ $tx['reference'] }}')" class="w-full bg-white p-3.5 rounded-2xl border border-slate-100 shadow-sm">
                                <row class="w-full items-center justify-between">
                                    <row class="items-center gap-3">
                                        <column class="w-10 h-10 bg-slate-50 rounded-xl items-center justify-center">
                                            <text class="text-base font-bold">💳</text>
                                        </column>
                                        <column>
                                            <text class="text-xs font-bold text-slate-900">{{ $tx['service_type'] }}</text>
                                            <text class="text-[10px] font-medium text-slate-400">{{ $tx['created_at'] }}</text>
                                        </column>
                                    </row>
                                    <column class="items-end">
                                        <text class="text-xs font-black text-slate-900">₦{{ $tx['amount'] }}</text>
                                        <text class="text-[10px] font-bold {{ $tx['status'] === 'successful' ? 'text-emerald-600' : ($tx['status'] === 'pending' ? 'text-amber-600' : 'text-red-500') }}">
                                            {{ strtoupper($tx['status']) }}
                                        </text>
                                    </column>
                                </row>
                            </pressable>
                        @endforeach
                    </column>
                @endif
            </column>
        </column>
    </scroll-view>

    <!-- Bottom Navigation Bar -->
    <row class="w-full bg-white border-t border-slate-100 py-3 px-6 justify-between items-center shadow-lg">
        <pressable on-press="goToHome" class="items-center gap-1">
            <text class="text-lg text-blue-600">🏠</text>
            <text class="text-[10px] font-bold text-blue-600">Home</text>
        </pressable>

        <pressable on-press="goToWallet" class="items-center gap-1">
            <text class="text-lg text-slate-400">💼</text>
            <text class="text-[10px] font-semibold text-slate-500">Wallet</text>
        </pressable>

        <pressable on-press="goToTransactions" class="items-center gap-1">
            <text class="text-lg text-slate-400">📊</text>
            <text class="text-[10px] font-semibold text-slate-500">History</text>
        </pressable>

        <pressable on-press="goToProfile" class="items-center gap-1">
            <text class="text-lg text-slate-400">👤</text>
            <text class="text-[10px] font-semibold text-slate-500">Profile</text>
        </pressable>
    </row>
</column>
