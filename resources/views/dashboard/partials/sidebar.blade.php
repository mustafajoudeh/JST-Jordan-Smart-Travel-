@php
    $brand = config('jst-dashboard.brand');
    $navigation = $roleConfig['navigation'] ?? [];

    $previewEnabled = app()->environment('local') && \Illuminate\Support\Facades\Route::has('dashboard.preview');

    /*
    | Resolve navigation URL.
    | Real route takes priority, local preview is the fallback.
    */
    $resolveUrl = function (array $item) use ($roleKey, $previewEnabled) {
        $routeName = $item['route'] ?? null;

        if ($routeName && \Illuminate\Support\Facades\Route::has($routeName)) {
            return route($routeName, $item['route_params'] ?? []);
        }

        if ($previewEnabled) {
            return route('dashboard.preview', [
                'role' => $roleKey,
                'page' => $item['key'],
            ]);
        }

        return '#';
    };
@endphp

<aside class="jst-sidebar">

    {{-- Brand --}}
    <div class="jst-sidebar__brand">

        <div class="jst-sidebar__logo">
            {{ $brand['short_name'] ?? 'JST' }}
        </div>

        <div class="min-w-0">

            <h2 class="truncate text-sm font-bold">
                {{ $brand['name'] ?? 'Jordan Smart Travel' }}
            </h2>

            <p class="mt-1 text-[10px] font-semibold tracking-widest text-base-content/45">
                {{ $brand['console_label'] ?? 'MANAGEMENT CONSOLE' }}
            </p>

        </div>

    </div>

    {{-- Workspace --}}
    <div class="jst-sidebar__workspace">
        {{ $roleConfig['workspace'] }}
    </div>

    {{-- Navigation --}}
    <nav class="jst-sidebar__nav" aria-label="Dashboard navigation">

        @foreach ($navigation as $entry)
            {{-- ==================================
                 Standalone Link
            =================================== --}}

            @if (($entry['type'] ?? null) === 'link')
                @php
                    $isActive = $activePage === $entry['key'];
                    $href = $resolveUrl($entry);
                @endphp

                <a href="{{ $href }}" class="jst-nav-link {{ $isActive ? 'is-active' : '' }}"
                    @if ($isActive) aria-current="page" @endif>

                    <i data-lucide="{{ $entry['icon'] }}" aria-hidden="true"></i>

                    <span>{{ $entry['label'] }}</span>

                </a>

                {{-- ==================================
                 Group with Nested Links
            =================================== --}}
            @elseif (($entry['type'] ?? null) === 'group')
                @php
                    $children = collect($entry['items'] ?? []);

                    $groupIsActive = $children->contains(fn($child) => $activePage === $child['key']);
                @endphp

                <details class="jst-nav-group" @if ($groupIsActive) open @endif>

                    <summary
                        class="jst-nav-group__summary
                            {{ $groupIsActive ? 'is-current' : '' }}">

                        <i data-lucide="{{ $entry['icon'] }}" aria-hidden="true"></i>

                        <span>{{ $entry['label'] }}</span>

                        <i data-lucide="chevron-down" class="jst-nav-chevron" aria-hidden="true"></i>

                    </summary>

                    {{-- Child Items --}}
                    <div class="jst-nav-group__items">

                        @foreach ($children as $item)
                            @php
                                $isActive = $activePage === $item['key'];
                                $href = $resolveUrl($item);
                            @endphp

                            <a href="{{ $href }}"
                                class="
                                    jst-nav-link
                                    jst-nav-link--nested
                                    {{ $isActive ? 'is-active' : '' }}
                                "
                                @if ($isActive) aria-current="page" @endif>

                                <i data-lucide="{{ $item['icon'] }}" aria-hidden="true"></i>

                                <span>{{ $item['label'] }}</span>

                            </a>
                        @endforeach

                    </div>

                </details>
            @endif
        @endforeach

    </nav>

    {{-- Sidebar Footer --}}
    <div class="jst-sidebar__footer">

        <div class="rounded-xl bg-base-200 p-3">

            <p class="text-sm font-semibold">
                {{ $roleConfig['label'] }}
            </p>

            <p class="mt-1 text-xs text-base-content/50">
                {{ $brand['short_name'] ?? 'JST' }} Management Workspace
            </p>

        </div>

    </div>

</aside>
