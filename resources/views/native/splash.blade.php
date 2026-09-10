<column class="w-full h-full bg-slate-900 items-center justify-center p-6 gap-6">
    <column class="items-center justify-center gap-3">
        <column class="w-24 h-24 bg-blue-600 rounded-3xl items-center justify-center shadow-lg">
            <text class="text-4xl font-extrabold text-white">⚡</text>
        </column>
        <text class="text-3xl font-black text-white text-center tracking-tight">{{ $platformName }}</text>
        <text class="text-sm font-bold text-blue-400 uppercase tracking-widest text-center">{{ $platformTagline }}</text>
    </column>

    <column class="items-center justify-center gap-2 mt-8">
        <activity-indicator class="text-blue-500" />
        <text class="text-xs font-semibold text-slate-400">Securing environment & loading...</text>
    </column>
</column>