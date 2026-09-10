<column class="w-full h-full bg-slate-50 justify-between">
    <scroll-view class="w-full flex-1 p-5">
        <column class="w-full gap-5 pt-4 pb-20">
            <row class="w-full items-center justify-between">
                <pressable on-press="goToHome" class="w-9 h-9 bg-white rounded-full items-center justify-center border border-slate-200 shadow-sm">
                    <text class="text-sm font-bold text-slate-700">←</text>
                </pressable>
                <text class="text-base font-black text-slate-900">Pay Electricity</text>
                <column class="w-9"></column>
            </row>

            @if($errorMessage)
                <column class="w-full bg-red-50 border border-red-200 rounded-2xl p-4">
                    <text class="text-xs font-bold text-red-600 text-center">{{ $errorMessage }}</text>
                </column>
            @endif

            <column class="w-full bg-white p-5 rounded-3xl border border-slate-100 shadow-sm gap-4">
                <!-- Provider -->
                <column class="gap-1.5">
                    <text class="text-xs font-bold text-slate-700 uppercase">Electricity Provider (DISCO)</text>
                    @foreach($providers as $prov)
                        <pressable on-press="selectProvider('{{ $prov['code'] }}')" class="w-full p-3.5 rounded-xl border-2 {{ $provider === $prov['code'] ? 'border-blue-600 bg-blue-50' : 'border-slate-200 bg-slate-50' }}">
                            <row class="items-center justify-between">
                                <text class="text-xs font-bold {{ $provider === $prov['code'] ? 'text-blue-700' : 'text-slate-700' }}">{{ $prov['name'] }}</text>
                                @if($provider === $prov['code'])
                                    <text class="text-sm text-blue-600">●</text>
                                @endif
                            </row>
                        </pressable>
                    @endforeach
                </column>

                <!-- Meter Number + Verify -->
                <column class="gap-1.5">
                    <text class="text-xs font-bold text-slate-700 uppercase">Meter Number</text>
                    <row class="w-full gap-2">
                        <text-input name="meterNumber" placeholder="Enter meter number" class="flex-1 bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-sm font-medium text-slate-900" />
                        <pressable on-press="verifyMeter" class="bg-blue-600 px-4 rounded-xl items-center justify-center shadow-sm">
                            <text class="text-xs font-black text-white">Verify</text>
                        </pressable>
                    </row>
                </column>

                <!-- Verified Customer Info -->
                @if($meterVerified)
                    <column class="w-full bg-emerald-50 border border-emerald-200 rounded-2xl p-4 gap-1">
                        <row class="items-center gap-1.5">
                            <text class="text-xs font-black text-emerald-700">✓ Meter Verified</text>
                        </row>
                        <text class="text-xs font-bold text-slate-900">{{ $customerName }}</text>
                        @if($customerAddress)
                            <text class="text-[10px] font-medium text-slate-500">{{ $customerAddress }}</text>
                        @endif
                    </column>

                    <column class="gap-1.5">
                        <text class="text-xs font-bold text-slate-700 uppercase">Amount (₦)</text>
                        <text-input name="amount" placeholder="Min ₦500" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-base font-black text-slate-900" />
                    </column>

                    <row class="w-full justify-between gap-2">
                        @foreach(['1000','2000','5000','10000'] as $preset)
                            <pressable on-press="setAmount('{{ $preset }}')" class="flex-1 bg-slate-100 py-2 rounded-xl items-center">
                                <text class="text-[10px] font-bold text-slate-700">₦{{ $preset }}</text>
                            </pressable>
                        @endforeach
                    </row>

                    <button on-press="proceedToPinEntry" class="w-full bg-amber-500 rounded-2xl py-4 items-center justify-center shadow-md">
                        <text class="text-white font-bold text-base">Pay for Token →</text>
                    </button>
                @endif
            </column>
        </column>
    </scroll-view>

    <!-- PIN Modal -->
    @if($showPinModal)
        <column class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl p-6 shadow-2xl gap-5 border-t border-slate-200">
            <column class="items-center gap-1">
                <column class="w-10 h-1 bg-slate-200 rounded-full"></column>
                <text class="text-base font-black text-slate-900 mt-3">Confirm Electricity Payment</text>
                <text class="text-xs font-medium text-slate-500 text-center">₦{{ $amount }} for {{ $customerName }}</text>
            </column>
            @if($errorMessage)
                <column class="w-full bg-red-50 border border-red-200 rounded-xl p-3">
                    <text class="text-xs font-bold text-red-600 text-center">{{ $errorMessage }}</text>
                </column>
            @endif
            <column class="gap-1.5">
                <text class="text-xs font-bold text-slate-700 uppercase">Enter 4-Digit Transaction PIN</text>
                <text-input name="pin" placeholder="••••" secure="true" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-4 text-center text-2xl font-black font-mono tracking-[1rem]" />
            </column>
            <row class="w-full gap-3">
                <pressable on-press="cancelPin" class="flex-1 bg-slate-100 rounded-2xl py-4 items-center justify-center">
                    <text class="text-sm font-bold text-slate-700">Cancel</text>
                </pressable>
                <button on-press="pay" class="flex-1 bg-amber-500 rounded-2xl py-4 items-center justify-center shadow-md">
                    <text class="text-white font-bold text-sm">Pay Now</text>
                </button>
            </row>
        </column>
    @endif

    <row class="w-full bg-white border-t border-slate-100 py-3 px-6 justify-between items-center shadow-lg">
        <pressable on-press="goToHome" class="items-center gap-1"><text class="text-lg text-slate-400">🏠</text><text class="text-[10px] font-semibold text-slate-500">Home</text></pressable>
        <pressable on-press="goToWallet" class="items-center gap-1"><text class="text-lg text-slate-400">💼</text><text class="text-[10px] font-semibold text-slate-500">Wallet</text></pressable>
        <pressable on-press="goToTransactions" class="items-center gap-1"><text class="text-lg text-slate-400">📊</text><text class="text-[10px] font-semibold text-slate-500">History</text></pressable>
        <pressable on-press="goToProfile" class="items-center gap-1"><text class="text-lg text-slate-400">👤</text><text class="text-[10px] font-semibold text-slate-500">Profile</text></pressable>
    </row>
</column>
