<column class="w-full h-full bg-slate-50 items-center justify-between p-6">
    <column class="w-full items-center justify-center gap-6 mt-16">
        <column class="w-20 h-20 bg-amber-100 rounded-3xl items-center justify-center">
            <text class="text-3xl font-extrabold text-amber-600">🛠️</text>
        </column>

        <column class="items-center gap-2 text-center">
            <text class="text-2xl font-black text-slate-900">System Maintenance</text>
            <text class="text-sm font-medium text-slate-600 px-4 text-center">
                We are performing scheduled upgrades to improve your recharge experience. We will be back online shortly!
            </text>
        </column>

        @if($errorMessage)
            <column class="w-full bg-red-50 border border-red-200 rounded-2xl p-4">
                <text class="text-xs font-bold text-red-600 text-center">{{ $errorMessage }}</text>
            </column>
        @endif

        <button on-press="checkStatus" class="w-full bg-blue-600 rounded-2xl py-4 items-center justify-center shadow-md">
            <text class="text-white font-bold text-base">Refresh & Retry</text>
        </button>
    </column>

    <column class="w-full items-center gap-2 mb-6">
        <text class="text-xs font-semibold text-slate-400">Need urgent assistance?</text>
        <text class="text-xs font-bold text-blue-600">{{ $supportEmail }} | {{ $supportPhone }}</text>
    </column>
</column>
