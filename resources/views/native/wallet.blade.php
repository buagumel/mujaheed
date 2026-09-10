<column class="w-full h-full bg-slate-50 justify-between">
    <scroll-view class="w-full flex-1 p-5">
        <column class="w-full gap-5 pt-4 pb-20">
            <!-- Header -->
            <row class="w-full items-center justify-between">
                <pressable on-press="goToHome" class="w-9 h-9 bg-white rounded-full items-center justify-center border border-slate-200 shadow-sm">
                    <text class="text-sm font-bold text-slate-700">←</text>
                </pressable>
                <text class="text-base font-black text-slate-900">My Wallet</text>
                <column class="w-9"></column>
            </row>

            <!-- Balance Card -->
            <column class="w-full bg-gradient-to-br from-blue-600 to-indigo-700 rounded-3xl p-6 shadow-lg shadow-blue-500/20 gap-4">
                <text class="text-xs font-bold text-blue-100 uppercase tracking-wider">Total Available Balance</text>
                <text class="text-3xl font-black text-white">₦{{ number_format($balance, 2) }}</text>

                <button on-press="goToFundWallet" class="w-full bg-white rounded-2xl py-3.5 items-center justify-center shadow-sm mt-2">
                    <text class="text-xs font-black text-blue-700">+ Fund Wallet via Instant Bank Transfer</text>
                </button>
            </column>

            <!-- Dedicated Virtual Bank Accounts -->
            <column class="w-full gap-3">
                <column class="gap-0.5">
                    <text class="text-sm font-black text-slate-800 uppercase tracking-wider">Personal Virtual Accounts</text>
                    <text class="text-xs font-medium text-slate-500">Transfer from any bank app to credit your wallet 24/7</text>
                </column>

                @foreach($virtualAccounts as $acc)
                    <column class="w-full bg-white p-4 rounded-2xl border border-slate-100 shadow-sm gap-2">
                        <row class="w-full items-center justify-between">
                            <row class="items-center gap-2">
                                <column class="w-8 h-8 bg-blue-50 rounded-lg items-center justify-center">
                                    <text class="text-sm font-bold text-blue-600">🏦</text>
                                </column>
                                <text class="text-xs font-bold text-slate-900">{{ $acc['bank_name'] }}</text>
                            </row>
                            <column class="bg-emerald-50 px-2.5 py-0.5 rounded-full">
                                <text class="text-[10px] font-bold text-emerald-700">Auto Credit</text>
                            </column>
                        </row>

                        <row class="w-full items-center justify-between bg-slate-50 p-2.5 rounded-xl">
                            <text class="text-base font-black text-blue-700 font-mono tracking-widest">{{ $acc['account_number'] }}</text>
                            <column class="bg-white border border-slate-200 px-3 py-1 rounded-lg">
                                <text class="text-[10px] font-bold text-slate-700">Account #</text>
                            </column>
                        </row>

                        <text class="text-[11px] font-medium text-slate-400">Account Name: <strong class="text-slate-700">{{ $acc['account_name'] }}</strong></text>
                    </column>
                @endforeach
            </column>

            <!-- Wallet Activity Log -->
            <column class="w-full gap-3">
                <text class="text-sm font-black text-slate-800 uppercase tracking-wider">Wallet Activity Log</text>

                @if(empty($walletHistory))
                    <column class="w-full bg-white rounded-2xl p-6 items-center justify-center gap-2 border border-slate-100">
                        <text class="text-xs font-semibold text-slate-400">No recent deposits or withdrawals</text>
                    </column>
                @else
                    <column class="w-full gap-2">
                        @foreach($walletHistory as $log)
                            <column class="w-full bg-white p-3.5 rounded-2xl border border-slate-100 shadow-sm gap-1">
                                <row class="w-full items-center justify-between">
                                    <text class="text-xs font-bold text-slate-800">{{ $log['description'] }}</text>
                                    <text class="text-xs font-black {{ $log['type'] === 'credit' ? 'text-emerald-600' : 'text-slate-900' }}">
                                        {{ $log['type'] === 'credit' ? '+' : '-' }}₦{{ $log['amount'] }}
                                    </text>
                                </row>
                                <row class="w-full items-center justify-between">
                                    <text class="text-[10px] font-medium text-slate-400">{{ $log['created_at'] }}</text>
                                    <text class="text-[10px] font-semibold text-slate-500">Bal: ₦{{ $log['balance_after'] }}</text>
                                </row>
                            </column>
                        @endforeach
                    </column>
                @endif
            </column>
        </column>
    </scroll-view>

    <!-- Bottom Navigation Bar -->
    <row class="w-full bg-white border-t border-slate-100 py-3 px-6 justify-between items-center shadow-lg">
        <pressable on-press="goToHome" class="items-center gap-1">
            <text class="text-lg text-slate-400">🏠</text>
            <text class="text-[10px] font-semibold text-slate-500">Home</text>
        </pressable>

        <pressable on-press="goToWallet" class="items-center gap-1">
            <text class="text-lg text-blue-600">💼</text>
            <text class="text-[10px] font-bold text-blue-600">Wallet</text>
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
