<column class="w-full h-full bg-slate-50 justify-between">
    <scroll-view class="w-full flex-1 p-5">
        <column class="w-full gap-5 pt-4 pb-20">
            <!-- Header -->
            <row class="w-full items-center justify-between">
                <pressable on-press="goToHome" class="w-9 h-9 bg-white rounded-full items-center justify-center border border-slate-200 shadow-sm">
                    <text class="text-sm font-bold text-slate-700">←</text>
                </pressable>
                <text class="text-base font-black text-slate-900">Buy Airtime</text>
                <column class="w-9"></column>
            </row>

            @if($errorMessage)
                <column class="w-full bg-red-50 border border-red-200 rounded-2xl p-4">
                    <text class="text-xs font-bold text-red-600 text-center">{{ $errorMessage }}</text>
                </column>
            @endif

            <!-- Network Selection -->
            <column class="w-full bg-white p-5 rounded-3xl border border-slate-100 shadow-sm gap-4">
                <text class="text-xs font-black text-slate-700 uppercase tracking-wider">Select Network</text>

                <row class="w-full justify-between gap-3">
                    @foreach($networks as $net)
                        <pressable on-press="selectNetwork('{{ $net['code'] }}')" class="flex-1 py-3 rounded-2xl items-center gap-1 border-2 {{ $network === $net['code'] ? 'border-blue-600 bg-blue-50' : 'border-slate-200 bg-white' }}">
                            <text class="text-xl">{{ $net['emoji'] }}</text>
                            <text class="text-[10px] font-bold {{ $network === $net['code'] ? 'text-blue-700' : 'text-slate-600' }}">{{ $net['label'] }}</text>
                        </pressable>
                    @endforeach
                </row>

                <column class="gap-1.5">
                    <text class="text-xs font-bold text-slate-700 uppercase">Recipient Phone</text>
                    <text-input name="phone" placeholder="08012345678" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-sm font-medium text-slate-900" />
                </column>

                <column class="gap-1.5">
                    <text class="text-xs font-bold text-slate-700 uppercase">Amount (₦)</text>
                    <text-input name="amount" placeholder="e.g. 500" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-base font-black text-slate-900" />
                </column>

                <!-- Quick Amount Buttons -->
                <row class="w-full justify-between gap-2">
                    @foreach(['100','200','500','1000'] as $preset)
                        <pressable on-press="setAmount('{{ $preset }}')" class="flex-1 bg-slate-100 py-2 rounded-xl items-center">
                            <text class="text-xs font-bold text-slate-700">₦{{ $preset }}</text>
                        </pressable>
                    @endforeach
                </row>

                <button on-press="proceedToPinEntry" class="w-full bg-blue-600 rounded-2xl py-4 items-center justify-center shadow-md mt-2">
                    <text class="text-white font-bold text-base">Continue →</text>
                </button>
            </column>
        </column>
    </scroll-view>

    <!-- PIN Entry Modal Bottom Sheet -->
    @if($showPinModal)
        <column class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl p-6 shadow-2xl gap-5 border-t border-slate-200">
            <column class="items-center gap-1">
                <column class="w-10 h-1 bg-slate-200 rounded-full"></column>
                <text class="text-base font-black text-slate-900 mt-3">Confirm Transaction</text>
                <text class="text-xs font-medium text-slate-500 text-center">₦{{ $amount }} Airtime to {{ $phone }} via {{ $network }}</text>
            </column>

            @if($errorMessage)
                <column class="w-full bg-red-50 border border-red-200 rounded-xl p-3">
                    <text class="text-xs font-bold text-red-600 text-center">{{ $errorMessage }}</text>
                </column>
            @endif

            <column class="gap-1.5">
                <text class="text-xs font-bold text-slate-700 uppercase">Enter Your 4-Digit Transaction PIN</text>
                <text-input name="pin" placeholder="••••" secure="true" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-4 text-center text-2xl font-black font-mono tracking-[1rem]" />
            </column>

            <row class="w-full gap-3">
                <pressable on-press="cancelPin" class="flex-1 bg-slate-100 rounded-2xl py-4 items-center justify-center">
                    <text class="text-sm font-bold text-slate-700">Cancel</text>
                </pressable>
                <button on-press="purchase" class="flex-1 bg-blue-600 rounded-2xl py-4 items-center justify-center shadow-md">
                    <text class="text-white font-bold text-sm">Buy Airtime</text>
                </button>
            </row>
        </column>
    @endif

    <!-- Bottom Navigation Bar -->
    <row class="w-full bg-white border-t border-slate-100 py-3 px-6 justify-between items-center shadow-lg">
        <pressable on-press="goToHome" class="items-center gap-1">
            <text class="text-lg text-slate-400">🏠</text>
            <text class="text-[10px] font-semibold text-slate-500">Home</text>
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
