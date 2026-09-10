<column class="w-full h-full bg-slate-50 justify-between">
    <scroll-view class="w-full flex-1 p-5">
        <column class="w-full gap-5 pt-4 pb-20">
            <row class="w-full items-center justify-between">
                <pressable on-press="goToHome" class="w-9 h-9 bg-white rounded-full items-center justify-center border border-slate-200 shadow-sm">
                    <text class="text-sm font-bold text-slate-700">←</text>
                </pressable>
                <text class="text-base font-black text-slate-900">Notifications</text>
                @if($unreadCount > 0)
                    <pressable on-press="markAllRead" class="bg-blue-50 border border-blue-200 px-3 py-1.5 rounded-xl">
                        <text class="text-[10px] font-bold text-blue-700">Mark All Read</text>
                    </pressable>
                @else
                    <column class="w-20"></column>
                @endif
            </row>

            @if(empty($notifications))
                <column class="w-full bg-white rounded-3xl p-10 items-center justify-center gap-3 border border-slate-100 mt-4">
                    <text class="text-4xl">🔔</text>
                    <text class="text-sm font-bold text-slate-500">All caught up!</text>
                    <text class="text-xs font-medium text-slate-400">No new notifications at this time.</text>
                </column>
            @else
                <column class="w-full gap-2">
                    @foreach($notifications as $notif)
                        <column class="w-full bg-white p-4 rounded-2xl border {{ $notif['is_read'] ? 'border-slate-100' : 'border-blue-200 bg-blue-50/30' }} shadow-sm gap-1.5">
                            <row class="w-full items-start justify-between gap-2">
                                <row class="items-center gap-2 flex-1">
                                    <column class="w-8 h-8 bg-{{ $notif['is_read'] ? 'slate-100' : 'blue-100' }} rounded-lg items-center justify-center flex-shrink-0">
                                        <text class="text-sm">{{ $notif['type'] === 'success' ? '✅' : ($notif['type'] === 'warning' ? '⚠️' : ($notif['type'] === 'error' ? '❌' : '📢')) }}</text>
                                    </column>
                                    <column class="flex-1 gap-0.5">
                                        <text class="text-xs font-black text-slate-900">{{ $notif['title'] }}</text>
                                        <text class="text-[10px] font-medium text-slate-500">{{ $notif['message'] }}</text>
                                    </column>
                                </row>
                                @if(!$notif['is_read'])
                                    <column class="w-2 h-2 bg-blue-600 rounded-full mt-2 flex-shrink-0"></column>
                                @endif
                            </row>
                            <text class="text-[9px] font-medium text-slate-300 pl-10">{{ $notif['created_at'] }}</text>
                        </column>
                    @endforeach
                </column>
            @endif
        </column>
    </scroll-view>

    <row class="w-full bg-white border-t border-slate-100 py-3 px-6 justify-between items-center shadow-lg">
        <pressable on-press="goToHome" class="items-center gap-1"><text class="text-lg text-slate-400">🏠</text><text class="text-[10px] font-semibold text-slate-500">Home</text></pressable>
        <pressable on-press="goToWallet" class="items-center gap-1"><text class="text-lg text-slate-400">💼</text><text class="text-[10px] font-semibold text-slate-500">Wallet</text></pressable>
        <pressable on-press="goToTransactions" class="items-center gap-1"><text class="text-lg text-slate-400">📊</text><text class="text-[10px] font-semibold text-slate-500">History</text></pressable>
        <pressable on-press="goToProfile" class="items-center gap-1"><text class="text-lg text-slate-400">👤</text><text class="text-[10px] font-semibold text-slate-500">Profile</text></pressable>
    </row>
</column>
