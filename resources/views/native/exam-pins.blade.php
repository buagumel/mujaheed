<column class="w-full h-full bg-slate-50 justify-between">
    <scroll-view class="w-full flex-1 p-5">
        <column class="w-full gap-5 pt-4 pb-20">
            <row class="w-full items-center justify-between">
                <pressable on-press="goToHome" class="w-9 h-9 bg-white rounded-full items-center justify-center border border-slate-200 shadow-sm">
                    <text class="text-sm font-bold text-slate-700">←</text>
                </pressable>
                <text class="text-base font-black text-slate-900">Exam PINs (WAEC / NECO)</text>
                <column class="w-9"></column>
            </row>

            @if($errorMessage)
                <column class="w-full bg-red-50 border border-red-200 rounded-2xl p-4">
                    <text class="text-xs font-bold text-red-600 text-center">{{ $errorMessage }}</text>
                </column>
            @endif

            <!-- Package List -->
            <column class="w-full gap-3">
                <text class="text-xs font-black text-slate-700 uppercase tracking-wider">Select Exam Type</text>
                <column class="w-full gap-2">
                    @foreach($packages as $pkg)
                        <pressable on-press="selectPackage('{{ $pkg['code'] }}')" class="w-full p-4 rounded-2xl border-2 {{ $selectedPackageCode === $pkg['code'] ? 'border-blue-600 bg-blue-50' : 'border-slate-200 bg-white' }} shadow-sm">
                            <row class="w-full items-center justify-between">
                                <row class="items-center gap-3">
                                    <column class="w-10 h-10 bg-blue-100 rounded-xl items-center justify-center">
                                        <text class="text-base">🎓</text>
                                    </column>
                                    <column>
                                        <text class="text-xs font-black {{ $selectedPackageCode === $pkg['code'] ? 'text-blue-900' : 'text-slate-900' }}">{{ $pkg['name'] }}</text>
                                        <text class="text-[10px] font-semibold text-slate-400">Code: {{ $pkg['code'] }}</text>
                                    </column>
                                </row>
                                <text class="text-sm font-black text-blue-700">₦{{ $pkg['price'] }}</text>
                            </row>
                        </pressable>
                    @endforeach
                </column>
            </column>

            <!-- Quantity Picker -->
            @if($selectedPackageCode)
                <column class="w-full bg-white p-5 rounded-3xl border border-slate-100 shadow-sm gap-4">
                    <text class="text-xs font-black text-slate-700 uppercase tracking-wider">Quantity</text>
                    <row class="w-full items-center justify-between">
                        <pressable on-press="decreaseQty" class="w-12 h-12 bg-slate-100 rounded-xl items-center justify-center">
                            <text class="text-xl font-black text-slate-700">−</text>
                        </pressable>
                        <column class="items-center gap-0.5">
                            <text class="text-3xl font-black text-blue-700">{{ $quantity }}</text>
                            <text class="text-[10px] font-medium text-slate-400">PIN(s)</text>
                        </column>
                        <pressable on-press="increaseQty" class="w-12 h-12 bg-blue-600 rounded-xl items-center justify-center shadow-sm">
                            <text class="text-xl font-black text-white">+</text>
                        </pressable>
                    </row>

                    <column class="w-full bg-slate-50 p-3 rounded-xl">
                        <row class="w-full items-center justify-between">
                            <text class="text-xs font-semibold text-slate-600">Total Cost:</text>
                            <text class="text-base font-black text-blue-700">
                                ₦{{ number_format((float)str_replace(',', '', $selectedPackagePrice()) * $quantity, 2) }}
                            </text>
                        </row>
                    </column>

                    <button on-press="proceedToPinEntry" class="w-full bg-blue-600 rounded-2xl py-4 items-center justify-center shadow-md">
                        <text class="text-white font-bold text-base">Generate {{ $quantity }} PIN(s) →</text>
                    </button>
                </column>
            @endif
        </column>
    </scroll-view>

    <!-- PIN Modal -->
    @if($showPinModal)
        <column class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl p-6 shadow-2xl gap-5 border-t border-slate-200">
            <column class="items-center gap-1">
                <column class="w-10 h-1 bg-slate-200 rounded-full"></column>
                <text class="text-base font-black text-slate-900 mt-3">Confirm Purchase</text>
                <text class="text-xs font-medium text-slate-500 text-center">{{ $quantity }}x {{ $selectedPackageName() }}</text>
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
                <button on-press="purchase" class="flex-1 bg-blue-600 rounded-2xl py-4 items-center justify-center shadow-md">
                    <text class="text-white font-bold text-sm">Purchase</text>
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
