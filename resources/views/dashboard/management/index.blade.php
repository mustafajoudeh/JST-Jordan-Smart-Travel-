@extends('layouts.dashboard')

@php
    $managementPages = [
        'governorates' => [
            'title' => 'Governorates',
            'description' => 'Organize the regions used across destinations and events.',
            'create' => 'Add governorate',
            'search' => 'Search governorates',
        ],
        'categories' => [
            'title' => 'Categories',
            'description' => 'Keep tourism and service classifications useful and current.',
            'create' => 'Add category',
            'search' => 'Search categories',
        ],
        'platform-settings' => [
            'title' => 'Platform settings',
            'description' => 'Manage platform-wide defaults and booking rules.',
            'create' => 'Add setting',
            'search' => 'Search settings',
        ],
    ];
    $page = $managementPages[$module];
@endphp

@section('title', $page['title'].' | JST')
@section('page', 'admin-management')

@section('dashboard-content')
    <div class="management-page" data-management-page data-module="{{ $module }}">
        <header class="management-heading">
            <div>
                <h1>{{ $page['title'] }}</h1>
                <p>{{ $page['description'] }}</p>
            </div>
            <button type="button" class="button button--primary management-create" data-create>
                <i data-lucide="plus" aria-hidden="true"></i>
                <span>{{ $page['create'] }}</span>
            </button>
        </header>

        <div class="management-toolbar">
            <label class="management-search">
                <i data-lucide="search" aria-hidden="true"></i>
                <span class="sr-only">{{ $page['search'] }}</span>
                <input type="search" data-search placeholder="{{ $page['search'] }}" autocomplete="off">
            </label>

            @if($module === 'categories')
                <label class="management-filter">
                    <span class="sr-only">Category type</span>
                    <select class="text-input" data-kind-filter aria-label="Category type">
                        <option value="all">All types</option>
                        <option value="tourism">Tourism</option>
                        <option value="service">Services</option>
                    </select>
                </label>
            @endif

            @if($module === 'platform-settings')
                <label class="management-filter">
                    <span class="sr-only">Setting group</span>
                    <select class="text-input" data-group-filter aria-label="Setting group">
                        <option value="all">All groups</option>
                        <option value="booking_finance">Bookings &amp; revenue</option>
                        <option value="booking_policy">Booking policy</option>
                        <option value="other">Other settings</option>
                    </select>
                </label>
            @endif

            @if($module !== 'platform-settings')
                <label class="management-filter">
                    <span class="sr-only">Record status</span>
                    <select class="text-input" data-status-filter aria-label="Record status">
                        <option value="all">All statuses</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </label>
            @endif
        </div>

        <p class="management-feedback hidden" data-list-feedback role="status"></p>

        <section class="management-table-card" aria-label="{{ $page['title'] }} records">
            <div class="management-table-scroll">
                <table class="management-table">
                    <thead>
                        <tr>
                            @if($module === 'governorates')
                                <th scope="col"><button type="button" data-sort="name">Governorate <i data-lucide="arrow-down-wide-narrow" aria-hidden="true"></i></button></th>
                                <th scope="col">Destinations</th>
                                <th scope="col">Events</th>
                                <th scope="col">Providers</th>
                                <th scope="col">Status</th>
                                <th scope="col"><span class="sr-only">Actions</span></th>
                            @elseif($module === 'categories')
                                <th scope="col"><button type="button" data-sort="name">Category <i data-lucide="arrow-down-wide-narrow" aria-hidden="true"></i></button></th>
                                <th scope="col">Type</th>
                                <th scope="col">Destinations</th>
                                <th scope="col">Activities &amp; services</th>
                                <th scope="col">Status</th>
                                <th scope="col"><span class="sr-only">Actions</span></th>
                            @else
                                <th scope="col"><button type="button" data-sort="key">Setting <i data-lucide="arrow-down-wide-narrow" aria-hidden="true"></i></button></th>
                                <th scope="col">Group</th>
                                <th scope="col">Type</th>
                                <th scope="col">Current value</th>
                                <th scope="col">Scope</th>
                                <th scope="col"><span class="sr-only">Actions</span></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody data-record-rows></tbody>
                </table>
            </div>

            <div class="management-table-state" data-table-state role="status" aria-live="polite">
                <span class="management-spinner" aria-hidden="true"></span>
                <span>Loading records…</span>
            </div>

            <footer class="management-pagination hidden" data-pagination>
                <p data-pagination-summary></p>
                <div>
                    <button type="button" class="button button--outline" data-page-previous aria-label="Previous page">
                        <i data-lucide="chevron-left" aria-hidden="true"></i><span>Previous</span>
                    </button>
                    <span data-page-number></span>
                    <button type="button" class="button button--outline" data-page-next aria-label="Next page">
                        <span>Next</span><i data-lucide="chevron-right" aria-hidden="true"></i>
                    </button>
                </div>
            </footer>
        </section>

        <dialog class="management-dialog" data-management-dialog aria-labelledby="management-dialog-title">
            <div data-dialog-content></div>
        </dialog>
        <div class="management-toast hidden" data-management-toast role="status" aria-live="polite"></div>
    </div>
@endsection
