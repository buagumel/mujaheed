<column class="w-full h-full bg-slate-50 justify-between">
    <scroll-view class="w-full flex-1 p-5">
        <column class="w-full gap-5 pt-4 pb-20">
            <!-- Header -->
            <row class="w-full items-center justify-between">
                <pressable on-press="goToHome" class="w-9 h-9 bg-white rounded-full items-center justify-center border border-slate-200 shadow-sm">
                    <text class="text-sm font-bold text-slate-700">←</text>
                </pressable>
                <text class="text-base font-black text-slate-900">Transaction History</text>
                <column class="w-9"></column>
            </row>

            <!-- Filter Chips -->
            <row class="w-full gap-2">
                @foreach(['all' => 'All', 'successful' => 'Success', 'pending' => 'Pending', 'failed' => 'Failed'] as $key => $label)
                    <pressable on-press="setFilter('{{ $key }}')" class="flex-1 py-2 rounded-xl items-center {{ $filter === $key ? 'bg-blue-600' : 'bg-white border border-slate-200' }}">
                        <text class="text-[10px] font-bold {{ $filter === $key ? 'text-white' : 'text-slate-600' }}">{{ $label }}</text>
                    </pressable>
                @endforeach
            </row>

            <!-- Transaction List -->
            @if(empty($transactions))
                <column class="w-full bg-white rounded-3xl p-8 items-center justify-center gap-3 border border-slate-100 mt-4">
                    <text class="text-3xl">📭</text>
                    <text class="text-sm font-bold text-slate-500">No transactions found</text>
                    <text class="text-xs font-medium text-slate-400">Try a different filter or make your first purchase!</text>
                </column>
            @else
                <column class="w-full gap-2">
                    @foreach($transactions as $tx)
                        <pressable on-press="viewReceipt('{{ $tx['reference'] }}')" class="w-full bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                            <row class="w-full items-center justify-between">
                                <row class="items-center gap-3">
                                    <column class="w-10 h-10 bg-slate-50 rounded-xl items-center justify-center">
                                        <text class="text-lg">{{ $tx['emoji'] }}</text>
                                    </column>
                                    <column class="gap-0.5">
                                        <text class="text-xs font-bold text-slate-900">{{ $tx['service_type'] }}</text>
                                        <text class="text-[10px] font-medium text-slate-400">{{ $tx['recipient'] }}</text>
                                        <text class="text-[9px] font-medium text-slate-300">{{ $tx['created_at'] }}</text>
                                    </column>
                                </row>
                                <column class="items-end gap-1">
                                    <text class="text-xs font-black text-slate-900">₦{{ $tx['amount'] }}</text>
                                    <column class="px-2 py-0.5 rounded-full {{ $tx['status'] === 'successful' ? 'bg-emerald-100' : ($tx['status'] === 'pending' ? 'bg-amber-100' : 'bg-red-100') }}">
                                        <text class="text-[9px] font-bold {{ $tx['status'] === 'successful' ? 'text-emerald-700' : ($tx['status'] === 'pending' ? 'text-amber-700' : 'text-red-700') }}">
                                            {{ strtoupper($tx['status']) }}
                                        </text>
                                    </column>
                                </column>
                            </row>
                        </pressable>
                    @endforeach

                    @if($hasMore)
                        <pressable on-press="loadMore" class="w-full bg-white border border-slate-200 rounded-2xl py-4 items-center justify-center mt-2">
                            <text class="text-xs font-bold text-blue-600">Load More Transactions</text>
                        </pressable>
                    @endif
                </column>
            @endif
        </column>
    </scroll-view>

    <row class="w-full bg-white border-t border-slate-100 py-3 px-6 justify-between items-center shadow-lg">
        <pressable on-press="goToHome" class="items-center gap-1"><text class="text-lg text-slate-400">🏠</text><text class="text-[10px] font-semibold text-slate-500">Home</text></pressable>
        <pressable on-press="goToWallet" class="items-center gap-1"><text class="text-lg text-slate-400">💼</text><text class="text-[10px] font-semibold text-slate-500">Wallet</text></pressable>
        <pressable on-press="goToTransactions" class="items-center gap-1"><text class="text-lg text-blue-600">📊</text><text class="text-[10px] font-bold text-blue-600">History</text></pressable>
        <pressable on-press="goToProfile" class="items-center gap-1"><text class="text-lg text-slate-400">👤</text><text class="text-[10px] font-semibold text-slate-500">Profile</text></pressable>
    </row>
</column>
