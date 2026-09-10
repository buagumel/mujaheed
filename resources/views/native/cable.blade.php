<column class="w-full h-full bg-slate-50 justify-between">
    <scroll-view class="w-full flex-1 p-5">
        <column class="w-full gap-5 pt-4 pb-20">
            <row class="w-full items-center justify-between">
                <pressable on-press="goToHome" class="w-9 h-9 bg-white rounded-full items-center justify-center border border-slate-200 shadow-sm">
                    <text class="text-sm font-bold text-slate-700">←</text>
                </pressable>
                <text class="text-base font-black text-slate-900">Cable TV Subscription</text>
                <column class="w-9"></column>
            </row>

            @if($errorMessage)
                <column class="w-full bg-red-50 border border-red-200 rounded-2xl p-4">
                    <text class="text-xs font-bold text-red-600 text-center">{{ $errorMessage }}</text>
                </column>
            @endif

            <column class="w-full bg-white p-5 rounded-3xl border border-slate-100 shadow-sm gap-4">
                <!-- Provider Selection -->
                <text class="text-xs font-black text-slate-700 uppercase tracking-wider">Select Provider</text>
                <row class="w-full justify-between gap-3">
                    @foreach($providers as $prov)
                        <pressable on-press="selectProvider('{{ $prov['code'] }}')" class="flex-1 py-3.5 rounded-2xl items-center gap-1.5 border-2 {{ $provider === $prov['code'] ? 'border-purple-600 bg-purple-50' : 'border-slate-200 bg-white' }}">
                            <text class="text-xl">{{ $prov['emoji'] }}</text>
                            <text class="text-[10px] font-bold {{ $provider === $prov['code'] ? 'text-purple-700' : 'text-slate-600' }}">{{ $prov['name'] }}</text>
                        </pressable>
                    @endforeach
                </row>

                <!-- Decoder / Smartcard Number -->
                <column class="gap-1.5">
                    <text class="text-xs font-bold text-slate-700 uppercase">Decoder / Smartcard Number</text>
                    <row class="w-full gap-2">
                        <text-input name="decoderNumber" placeholder="e.g. 7042800700" class="flex-1 bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-sm font-medium text-slate-900" />
                        <pressable on-press="verifyDecoder" class="bg-purple-600 px-4 rounded-xl items-center justify-center shadow-sm">
                            <text class="text-xs font-black text-white">Verify</text>
                        </pressable>
                    </row>
                </column>

                @if($decoderVerified)
                    <column class="w-full bg-emerald-50 border border-emerald-200 rounded-2xl p-4 gap-1">
                        <text class="text-xs font-black text-emerald-700">✓ Decoder Verified</text>
                        <text class="text-xs font-bold text-slate-900">{{ $customerName }}</text>
                    </column>
                @endif
            </column>

            <!-- Plans -->
            @if($decoderVerified && !empty($plans))
                <column class="w-full gap-3">
                    <text class="text-xs font-black text-slate-700 uppercase tracking-wider">Select a Bouquet</text>
                    <column class="w-full gap-2">
                        @foreach($plans as $plan)
                            <pressable on-press="selectPlan('{{ $plan['id'] }}')" class="w-full p-3.5 rounded-2xl border-2 {{ $selectedPlanId === $plan['id'] ? 'border-purple-600 bg-purple-50' : 'border-slate-200 bg-white' }} shadow-sm">
                                <row class="w-full items-center justify-between">
                                    <text class="text-xs font-bold {{ $selectedPlanId === $plan['id'] ? 'text-purple-800' : 'text-slate-900' }}">{{ $plan['name'] }}</text>
                                    <row class="items-center gap-2">
                                        <text class="text-sm font-black text-purple-700">₦{{ $plan['amount'] }}</text>
                                        @if($selectedPlanId === $plan['id'])
                                            <column class="w-5 h-5 bg-purple-600 rounded-full items-center justify-center">
                                                <text class="text-[10px] font-black text-white">✓</text>
                                            </column>
                                        @endif
                                    </row>
                                </row>
                            </pressable>
                        @endforeach
                    </column>

                    @if($selectedPlanId)
                        <button on-press="proceedToPinEntry" class="w-full bg-purple-600 rounded-2xl py-4 items-center justify-center shadow-md">
                            <text class="text-white font-bold text-base">Subscribe Now →</text>
                        </button>
                    @endif
                </column>
            @endif
        </column>
    </scroll-view>

    <!-- PIN Modal -->
    @if($showPinModal)
        <column class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl p-6 shadow-2xl gap-5 border-t border-slate-200">
            <column class="items-center gap-1">
                <column class="w-10 h-1 bg-slate-200 rounded-full"></column>
                <text class="text-base font-black text-slate-900 mt-3">Confirm Cable Subscription</text>
                <text class="text-xs font-medium text-slate-500 text-center">{{ $provider }} for {{ $customerName }}</text>
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
                <button on-press="pay" class="flex-1 bg-purple-600 rounded-2xl py-4 items-center justify-center shadow-md">
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
