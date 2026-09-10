<scroll-view class="w-full h-full bg-slate-50 p-5">
    <column class="w-full gap-5 pt-4 pb-12">
        <!-- Header -->
        <row class="w-full items-center justify-between">
            <pressable on-press="goToTransactions" class="w-9 h-9 bg-white rounded-full items-center justify-center border border-slate-200 shadow-sm">
                <text class="text-sm font-bold text-slate-700">←</text>
            </pressable>
            <text class="text-base font-black text-slate-900">Transaction Receipt</text>
            <pressable on-press="shareReceipt" class="w-9 h-9 bg-blue-600 rounded-full items-center justify-center shadow-sm">
                <text class="text-sm text-white">↑</text>
            </pressable>
        </row>

        @if(!$transaction)
            <column class="w-full bg-red-50 border border-red-200 rounded-2xl p-4">
                <text class="text-xs font-bold text-red-600 text-center">{{ $errorMessage ?? 'Receipt not found.' }}</text>
            </column>
        @else
            <!-- Status Badge -->
            <column class="w-full items-center gap-4">
                @if($transaction['status'] === 'successful')
                    <column class="w-20 h-20 bg-emerald-100 rounded-full items-center justify-center">
                        <text class="text-4xl">✅</text>
                    </column>
                    <column class="items-center gap-1">
                        <text class="text-xl font-black text-emerald-700">Payment Successful</text>
                        <text class="text-xs font-medium text-slate-500">{{ $transaction['service_type'] }} completed</text>
                    </column>
                @elseif($transaction['status'] === 'pending')
                    <column class="w-20 h-20 bg-amber-100 rounded-full items-center justify-center">
                        <text class="text-4xl">⏳</text>
                    </column>
                    <column class="items-center gap-1">
                        <text class="text-xl font-black text-amber-700">Processing...</text>
                        <text class="text-xs font-medium text-slate-500">{{ $transaction['service_type'] }} is pending</text>
                    </column>
                @else
                    <column class="w-20 h-20 bg-red-100 rounded-full items-center justify-center">
                        <text class="text-4xl">❌</text>
                    </column>
                    <column class="items-center gap-1">
                        <text class="text-xl font-black text-red-700">Transaction Failed</text>
                        <text class="text-xs font-medium text-slate-500">{{ $transaction['service_type'] }}</text>
                    </column>
                @endif
            </column>

            <!-- Receipt Card -->
            <column class="w-full bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <!-- Amount -->
                <column class="w-full bg-slate-50 p-5 items-center border-b border-slate-100">
                    <text class="text-3xl font-black text-slate-900">₦{{ $transaction['amount'] }}</text>
                </column>

                <!-- Details -->
                <column class="w-full p-5 gap-3.5">
                    @foreach([
                        ['Reference', $transaction['reference']],
                        ['Service', $transaction['service_type']],
                        ['Recipient', $transaction['recipient']],
                        ['Status', strtoupper($transaction['status'])],
                        ['Date', $transaction['created_at']],
                    ] as [$label, $value])
                        <row class="w-full items-center justify-between">
                            <text class="text-xs font-semibold text-slate-400">{{ $label }}</text>
                            <text class="text-xs font-bold text-slate-900 text-right max-w-[60%]">{{ $value }}</text>
                        </row>
                        <column class="w-full h-px bg-slate-50"></column>
                    @endforeach

                    @if($transaction['token'])
                        <column class="w-full bg-blue-50 border border-blue-200 rounded-2xl p-4 gap-1.5">
                            <text class="text-[10px] font-bold text-blue-500 uppercase">Electricity Token</text>
                            <text class="text-xl font-black text-blue-900 font-mono tracking-wider text-center">{{ $transaction['token'] }}</text>
                            @if($transaction['units'])
                                <text class="text-xs font-semibold text-blue-700 text-center">{{ $transaction['units'] }} kWh</text>
                            @endif
                        </column>
                    @endif

                    @if(!empty($pinsData))
                        <column class="w-full gap-2 pt-2">
                            <text class="text-xs font-black text-slate-700 uppercase">Exam PINs</text>
                            @foreach($pinsData as $pin)
                                <column class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 gap-1">
                                    <row class="w-full items-center justify-between">
                                        <text class="text-[10px] font-semibold text-slate-400">Serial</text>
                                        <text class="text-xs font-bold text-slate-700 font-mono">{{ $pin['serial'] ?? '—' }}</text>
                                    </row>
                                    <row class="w-full items-center justify-between">
                                        <text class="text-[10px] font-semibold text-slate-400">PIN</text>
                                        <text class="text-sm font-black text-blue-700 font-mono tracking-widest">{{ $pin['pin'] ?? '—' }}</text>
                                    </row>
                                </column>
                            @endforeach
                        </column>
                    @endif
                </column>
            </column>

            <!-- Actions -->
            <row class="w-full gap-3">
                <pressable on-press="goToHome" class="flex-1 bg-white border border-slate-200 rounded-2xl py-4 items-center justify-center">
                    <text class="text-xs font-bold text-slate-700">🏠 Home</text>
                </pressable>
                <button on-press="shareReceipt" class="flex-1 bg-blue-600 rounded-2xl py-4 items-center justify-center shadow-md">
                    <text class="text-white font-bold text-xs">↑ Share Receipt</text>
                </button>
            </row>
        @endif
    </column>
</scroll-view>
