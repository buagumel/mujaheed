<column class="w-full h-full bg-slate-50">

    <!-- ═══════════════════════════════════════════ -->
    <!-- Hero Slide Panel                            -->
    <!-- ═══════════════════════════════════════════ -->
    <column class="w-full bg-gradient-to-b from-{{ $slide['color_from'] }} to-{{ $slide['color_to'] }} flex-1 items-center justify-center p-8 gap-6 relative" style="min-height: 56%;">

        <!-- Skip button top-right -->
        @if($currentSlide < $totalSlides)
            <pressable on-press="skip" class="absolute top-12 right-6 bg-white/20 px-4 py-2 rounded-xl">
                <text class="text-xs font-bold text-white">Skip</text>
            </pressable>
        @endif

        <!-- Giant Emoji Icon -->
        <column class="w-28 h-28 bg-white/20 backdrop-blur-sm rounded-[2rem] items-center justify-center shadow-2xl border border-white/30">
            <text class="text-6xl">{{ $slide['emoji'] }}</text>
        </column>

        <!-- Badge Pill -->
        <column class="bg-white/25 border border-white/30 px-4 py-1.5 rounded-full">
            <text class="text-[10px] font-black text-white uppercase tracking-[0.15em]">{{ $slide['badge'] }}</text>
        </column>

        <!-- Headline -->
        <text class="text-3xl font-black text-white text-center leading-tight">{{ $slide['title'] }}</text>

        <!-- Subtitle -->
        <text class="text-sm font-medium text-white/80 text-center leading-relaxed px-2">{{ $slide['subtitle'] }}</text>
    </column>

    <!-- ═══════════════════════════════════════════ -->
    <!-- Feature List + Navigation Panel             -->
    <!-- ═══════════════════════════════════════════ -->
    <column class="w-full bg-white px-6 py-6 gap-5" style="min-height: 44%;">

        <!-- Feature Bullets -->
        <column class="w-full gap-2.5">
            @foreach($slide['features'] as $feature)
                <row class="w-full items-center gap-3 bg-slate-50 px-4 py-3 rounded-2xl border border-slate-100">
                    <text class="text-sm font-semibold text-slate-800">{{ $feature }}</text>
                </row>
            @endforeach
        </column>

        <!-- Dot Indicators + Nav Controls -->
        <row class="w-full items-center justify-between mt-2">

            <!-- Prev Button or placeholder -->
            @if($currentSlide > 1)
                <pressable on-press="prev" class="w-11 h-11 bg-slate-100 rounded-full items-center justify-center">
                    <text class="text-base font-black text-slate-600">←</text>
                </pressable>
            @else
                <column class="w-11"></column>
            @endif

            <!-- Slide Dots -->
            <row class="items-center gap-2.5">
                @for($i = 1; $i <= $totalSlides; $i++)
                    <pressable on-press="goToSlide({{ $i }})">
                        <column class="{{ $currentSlide === $i ? 'w-7 h-2.5 bg-blue-600' : 'w-2.5 h-2.5 bg-slate-200' }} rounded-full"></column>
                    </pressable>
                @endfor
            </row>

            <!-- Next / Get Started Button -->
            @if($currentSlide < $totalSlides)
                <pressable on-press="next" class="w-11 h-11 bg-blue-600 rounded-full items-center justify-center shadow-md shadow-blue-500/30">
                    <text class="text-base font-black text-white">→</text>
                </pressable>
            @else
                <pressable on-press="next" class="bg-emerald-600 px-5 h-11 rounded-full items-center justify-center shadow-md shadow-emerald-500/30">
                    <text class="text-xs font-black text-white">Get Started →</text>
                </pressable>
            @endif
        </row>

        <!-- Sign In Link (on last slide) -->
        @if($currentSlide === $totalSlides)
            <row class="w-full items-center justify-center gap-1.5">
                <text class="text-xs font-medium text-slate-500">Already have an account?</text>
                <pressable on-press="skip">
                    <text class="text-xs font-black text-blue-600">Sign In →</text>
                </pressable>
            </row>
        @endif
    </column>

</column>
