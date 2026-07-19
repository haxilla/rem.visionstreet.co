@php
    $flyer = $flyer ?? $data['flyer'];

    /*
     * wizardStep represents the highest step the user has unlocked.
     *
     * 1 = Property
     * 2 = Details
     * 3 = Photos
     * 4 = Design
     */
    $highestUnlockedStep = max(1, min(4, (int) ($flyer->wizardStep ?? 1)));

    /*
     * Determine the page currently being displayed from the URL.
     * No named routes are required.
     */
    $currentPath = trim(request()->path(), '/');

    if (
        str_contains($currentPath, 'flyer/create') ||
        str_contains($currentPath, 'flyer/property')
    ) {
        $currentStep = 1;
    } elseif (str_contains($currentPath, 'flyer/details')) {
        $currentStep = 2;
    } elseif (str_contains($currentPath, 'flyer/photos')) {
        $currentStep = 3;
    } elseif (str_contains($currentPath, 'flyer/design')) {
        $currentStep = 4;
    } else {
        $currentStep = 1;
    }

    /*
     * Text has been removed as a separate wizard step.
     */
    $steps = [
        1 => [
            'title' => 'Property',
            'url' => url('/member/flyer/create') . '?flyerId=' . $flyer->id,
        ],

        2 => [
            'title' => 'Details',
            'url' => url('/member/flyer/details') . '?flyerId=' . $flyer->id,
        ],

        3 => [
            'title' => 'Photos',
            'url' => url('/member/flyer/photos') . '?flyerId=' . $flyer->id,
        ],

        4 => [
            'title' => 'Design',
            'url' => url('/member/flyer/design') . '?flyerId=' . $flyer->id,
        ],
    ];
@endphp

<div class="mb-8">
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-black/5">

        {{-- Desktop wizard --}}
        <div class="hidden md:flex">

            @foreach ($steps as $stepNumber => $step)

                @php
                    $isCurrent = $stepNumber === $currentStep;

                    /*
                     * Any unlocked step other than the current page may be clicked.
                     * This lets the user move backward or return to an already
                     * unlocked later step, but never skip beyond wizardStep.
                     */
                    $isUnlocked = $stepNumber <= $highestUnlockedStep;
                    $isClickable = $isUnlocked && !$isCurrent;

                    /*
                     * A step is visually complete when the user has advanced
                     * beyond it in the stored wizard progress.
                     */
                    $isCompleted = $stepNumber < $highestUnlockedStep;

                    if ($isCurrent) {
                        $stepClasses = 'bg-[#123f91] text-white';
                        $circleClasses = 'bg-white text-[#123f91]';
                    } elseif ($isCompleted) {
                        $stepClasses = 'bg-emerald-600 text-white hover:bg-emerald-700';
                        $circleClasses = 'bg-white text-emerald-700';
                    } elseif ($isUnlocked) {
                        $stepClasses = 'bg-slate-600 text-white hover:bg-slate-700';
                        $circleClasses = 'bg-white text-slate-700';
                    } else {
                        $stepClasses = 'bg-slate-100 text-slate-400';
                        $circleClasses = 'bg-slate-200 text-slate-500';
                    }
                @endphp

                <div class="relative min-w-0 flex-1">

                    @if ($isClickable)
                        <a
                            href="{{ $step['url'] }}"
                            class="relative flex min-h-[84px] items-center px-6 py-4 transition {{ $stepClasses }}"
                        >
                    @else
                        <div
                            class="relative flex min-h-[84px] items-center px-6 py-4 {{ $stepClasses }}"
                            @if (!$isUnlocked)
                                aria-disabled="true"
                            @endif
                        >
                    @endif

                        <div class="relative z-20 flex min-w-0 items-center gap-3">

                            <span
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-sm font-black shadow-sm {{ $circleClasses }}"
                            >
                                @if ($isCompleted && !$isCurrent)
                                    ✓
                                @else
                                    {{ $stepNumber }}
                                @endif
                            </span>

                            <div class="min-w-0">
                                <div class="truncate font-black">
                                    {{ $step['title'] }}
                                </div>

                                <div class="mt-0.5 text-xs font-semibold opacity-80">
                                    @if ($isCurrent)
                                        Current step
                                    @elseif ($isCompleted)
                                        Completed
                                    @elseif ($isUnlocked)
                                        Available
                                    @else
                                        Not available yet
                                    @endif
                                </div>
                            </div>

                        </div>

                        @if (!$loop->last)
                            {{-- White border between chevrons --}}
                            <span
                                class="pointer-events-none absolute right-[-22px] top-0 z-30 h-full w-6 bg-white"
                                style="clip-path: polygon(0 0, 100% 50%, 0 100%);"
                            ></span>

                            {{-- Chevron using the current step's background --}}
                            <span
                                class="pointer-events-none absolute right-[-18px] top-0 z-40 h-full w-6 {{ $stepClasses }}"
                                style="clip-path: polygon(0 0, 100% 50%, 0 100%);"
                            ></span>
                        @endif

                    @if ($isClickable)
                        </a>
                    @else
                        </div>
                    @endif

                </div>

            @endforeach

        </div>

        {{-- Mobile wizard --}}
        <div class="grid gap-2 p-3 md:hidden">

            @foreach ($steps as $stepNumber => $step)

                @php
                    $isCurrent = $stepNumber === $currentStep;
                    $isUnlocked = $stepNumber <= $highestUnlockedStep;
                    $isClickable = $isUnlocked && !$isCurrent;
                    $isCompleted = $stepNumber < $highestUnlockedStep;

                    if ($isCurrent) {
                        $mobileClasses = 'bg-[#123f91] text-white';
                        $mobileCircleClasses = 'bg-white text-[#123f91]';
                    } elseif ($isCompleted) {
                        $mobileClasses = 'bg-emerald-600 text-white';
                        $mobileCircleClasses = 'bg-white text-emerald-700';
                    } elseif ($isUnlocked) {
                        $mobileClasses = 'bg-slate-600 text-white';
                        $mobileCircleClasses = 'bg-white text-slate-700';
                    } else {
                        $mobileClasses = 'bg-slate-100 text-slate-400';
                        $mobileCircleClasses = 'bg-slate-200 text-slate-500';
                    }
                @endphp

                @if ($isClickable)
                    <a
                        href="{{ $step['url'] }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-3 transition {{ $mobileClasses }}"
                    >
                @else
                    <div
                        class="flex items-center gap-3 rounded-xl px-4 py-3 {{ $mobileClasses }}"
                        @if (!$isUnlocked)
                            aria-disabled="true"
                        @endif
                    >
                @endif

                    <span
                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-black {{ $mobileCircleClasses }}"
                    >
                        @if ($isCompleted && !$isCurrent)
                            ✓
                        @else
                            {{ $stepNumber }}
                        @endif
                    </span>

                    <div class="flex-1">
                        <div class="font-black">
                            {{ $step['title'] }}
                        </div>
                    </div>

                    <div class="text-xs font-bold opacity-80">
                        @if ($isCurrent)
                            Current
                        @elseif ($isCompleted)
                            Completed
                        @elseif ($isUnlocked)
                            Available
                        @else
                            Locked
                        @endif
                    </div>

                @if ($isClickable)
                    </a>
                @else
                    </div>
                @endif

            @endforeach

        </div>

    </div>
</div>