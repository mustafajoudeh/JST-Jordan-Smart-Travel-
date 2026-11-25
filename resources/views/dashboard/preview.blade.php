
@extends('layouts.dashboard')

@section('title', $pageTitle . ' | JST')

@section('content')

    <div class="mb-8">

        <p class="text-sm font-medium text-primary">
            {{ $roleConfig['workspace'] }}
        </p>

        <h2 class="mt-2 text-2xl font-bold sm:text-3xl">
            {{ $pageTitle }}
        </h2>

        <p class="mt-2 text-sm text-base-content/60">
            {{ $roleConfig['description'] }}
        </p>

    </div>

    {{-- Preview Cards --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">

        @foreach ([
            ['label' => 'Total Destinations', 'value' => '24', 'icon' => 'map-pin'],
            ['label' => 'Total Activities', 'value' => '48', 'icon' => 'activity'],
            ['label' => 'Pending Reviews', 'value' => '12', 'icon' => 'clipboard-check'],
            ['label' => 'Total Providers', 'value' => '18', 'icon' => 'building-2'],
        ] as $card)

            <div class="jst-preview-card card border bg-base-100">

                <div class="card-body p-5">

                    <div class="flex items-center justify-between">

                        <p class="text-sm font-medium text-base-content/60">
                            {{ $card['label'] }}
                        </p>

                        <span class="jst-preview-card__icon">
                            <i
                                data-lucide="{{ $card['icon'] }}"
                                class="size-5 text-primary"
                            ></i>
                        </span>

                    </div>

                    <p class="text-3xl font-bold">
                        {{ $card['value'] }}
                    </p>

                </div>

            </div>

        @endforeach

    </div>

    <div class="jst-content-placeholder mt-6 border border-base-300 p-8 text-center">

        <h3 class="font-semibold">
            {{ $pageTitle }} Content
        </h3>

        <p class="mt-2 text-sm text-base-content/50">
            This area will contain the actual page content.
        </p>

    </div>

@endsection
