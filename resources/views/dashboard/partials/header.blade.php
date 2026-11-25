
@php
    $displayName = auth()->user()?->name ?? 'Preview User';

    $initial = mb_strtoupper(
        mb_substr($displayName, 0, 1)
    );
@endphp

<header class="jst-header">

    <div class="navbar min-h-[4.25rem] px-4 sm:px-6">

        {{-- Left Section --}}
        <div class="navbar-start gap-3">

            {{-- Mobile Sidebar Toggle --}}
            <label
                for="jst-sidebar-toggle"
                class="btn btn-ghost btn-square lg:hidden"
                aria-label="Open navigation"
            >
                <i data-lucide="menu" class="size-5"></i>
            </label>

            <div class="min-w-0">

                <div class="flex items-center gap-2 text-xs text-base-content/50">
                    <span>JST</span>
                    <span>/</span>
                    <span>{{ $roleConfig['label'] }}</span>
                </div>

                <h1 class="mt-1 truncate text-base font-semibold sm:text-lg">
                    {{ $pageTitle }}
                </h1>

            </div>

        </div>

        {{-- Right Section --}}
        <div class="navbar-end gap-3">

            <div class="hidden text-right sm:block">

                <p class="text-sm font-semibold">
                    {{ $displayName }}
                </p>

                <p class="mt-0.5 text-xs text-base-content/50">
                    {{ $roleConfig['label'] }}
                </p>

            </div>

            <div
                class="avatar placeholder"
                title="{{ $displayName }}"
            >

                <div class="size-10 rounded-full bg-primary/10 text-primary">

                    <span class="text-sm font-bold">
                        {{ $initial }}
                    </span>

                </div>

            </div>

        </div>

    </div>

</header>
