<scroll-view class="w-full h-full bg-slate-50 p-5">
    <column class="w-full gap-5 pt-4 pb-12">
        <!-- Header -->
        <row class="w-full items-center justify-between">
            <pressable on-press="goToWallet" class="w-9 h-9 bg-white rounded-full items-center justify-center border border-slate-200 shadow-sm">
                <text class="text-sm font-bold text-slate-700">←</text>
            </pressable>
            <text class="text-base font-black text-slate-900">Deposit / Fund Wallet</text>
            <column class="w-9"></column>
        </row>

        @if($errorMessage)
            <column class="w-full bg-red-50 border border-red-200 rounded-2xl p-4">
                <text class="text-xs font-bold text-red-600 text-center">{{ $errorMessage }}</text>
            </column>
        @endif

        <!-- Dedicated Bank Transfer (Instant & Recommended) -->
        <column class="w-full bg-gradient-to-br from-slate-900 to-blue-950 p-5 rounded-3xl gap-3 shadow-md">
            <row class="items-center justify-between">
                <row class="items-center gap-2">
                    <text class="text-base">⚡</text>
                    <text class="text-xs font-black text-white uppercase tracking-wider">Method 1: Instant Bank Transfer</text>
                </row>
                <column class="bg-emerald-500/20 px-2.5 py-0.5 rounded-full">
                    <text class="text-[10px] font-bold text-emerald-400">Zero Charge</text>
                </column>
            </row>
            <text class="text-xs font-medium text-slate-300">
                Transfer any amount to your personal virtual accounts below from your banking app. Your wallet will be credited in 5 seconds!
            </text>

            @foreach($virtualAccounts as $acc)
                <column class="w-full bg-white/10 p-3.5 rounded-2xl gap-1 border border-white/10 mt-1">
                    <row class="w-full items-center justify-between">
                        <text class="text-xs font-bold text-blue-200">{{ $acc['bank_name'] }}</text>
                        <text class="text-[10px] font-semibold text-slate-400">Automated 24/7</text>
                    </row>
                    <row class="w-full items-center justify-between bg-black/20 p-2.5 rounded-xl">
                        <text class="text-base font-black text-white font-mono tracking-widest">{{ $acc['account_number'] }}</text>
                        <column class="bg-blue-600 px-3 py-1 rounded-lg">
                            <text class="text-[10px] font-bold text-white">Bank Account</text>
                        </column>
                    </row>
                    <text class="text-[10px] font-medium text-slate-300">Name: {{ $acc['account_name'] }}</text>
                </column>
            @endforeach
        </column>

        <!-- Method 2: Online Gateway Card / USSD / Bank -->
        <column class="w-full bg-white p-5 rounded-3xl border border-slate-100 shadow-sm gap-4">
            <row class="items-center gap-2">
                <text class="text-base">💳</text>
                <text class="text-xs font-black text-slate-800 uppercase tracking-wider">Method 2: Debit Card / Online Payment</text>
            </row>

            <column class="gap-1.5">
                <text class="text-xs font-bold text-slate-700">Enter Amount (₦)</text>
                <text-input name="amount" placeholder="e.g. 2000" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-base font-bold text-slate-900" />
            </column>

            <!-- Quick Amount Chips -->
            <row class="w-full justify-between gap-2">
                <pressable on-press="setAmount('1000')" class="flex-1 bg-slate-100 p-2.5 rounded-xl items-center">
                    <text class="text-xs font-bold text-slate-700">₦1,000</text>
                </pressable>
                <pressable on-press="setAmount('2000')" class="flex-1 bg-slate-100 p-2.5 rounded-xl items-center">
                    <text class="text-xs font-bold text-slate-700">₦2,000</text>
                </pressable>
                <pressable on-press="setAmount('5000')" class="flex-1 bg-slate-100 p-2.5 rounded-xl items-center">
                    <text class="text-xs font-bold text-slate-700">₦5,000</text>
                </pressable>
                <pressable on-press="setAmount('10000')" class="flex-1 bg-slate-100 p-2.5 rounded-xl items-center">
                    <text class="text-xs font-bold text-slate-700">₦10,000</text>
                </pressable>
            </row>

            <button on-press="initiatePayment" class="w-full bg-blue-600 rounded-2xl py-4 items-center justify-center shadow-md mt-2">
                <text class="text-white font-bold text-base">Proceed to Payment →</text>
            </button>
        </column>
    </column>
</scroll-view>
