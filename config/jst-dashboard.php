<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Brand Configuration
    |--------------------------------------------------------------------------
    */

    'brand' => [
        'name' => 'Jordan Smart Travel',
        'short_name' => 'JST',
        'console_label' => 'MANAGEMENT CONSOLE',
    ],

    'default_role' => 'system_admin',

    /*
    |--------------------------------------------------------------------------
    | Dashboard Roles & Navigation
    |--------------------------------------------------------------------------
    */

    'roles' => [

        /*
        |----------------------------------------------------------------------
        | System Administrator
        |----------------------------------------------------------------------
        */

        'system_admin' => [

            'label' => 'System Administrator',

            'workspace' => 'Platform Management',

            'description' => 'Manage and monitor the entire JST platform.',

            'navigation' => [

                [
                    'type' => 'link',
                    'key' => 'overview',
                    'label' => 'Overview',
                    'icon' => 'layout-dashboard',
                ],

                [
                    'type' => 'group',
                    'key' => 'content-management',
                    'label' => 'Content Management',
                    'icon' => 'folder-open',

                    'items' => [

                        [
                            'key' => 'destinations',
                            'label' => 'Destinations',
                            'icon' => 'map-pin',
                        ],

                        [
                            'key' => 'events',
                            'label' => 'Events',
                            'icon' => 'calendar-days',
                        ],

                        [
                            'key' => 'activities',
                            'label' => 'Activities',
                            'icon' => 'activity',
                        ],

                        [
                            'key' => 'experiences',
                            'label' => 'Experiences',
                            'icon' => 'sparkles',
                        ],

                    ],
                ],

                [
                    'type' => 'group',
                    'key' => 'locations-classification',
                    'label' => 'Locations & Classification',
                    'icon' => 'map',

                    'items' => [

                        [
                            'key' => 'governorates',
                            'label' => 'Governorates',
                            'icon' => 'map',
                        ],

                        [
                            'key' => 'categories',
                            'label' => 'Categories',
                            'icon' => 'tags',
                        ],

                    ],
                ],

                [
                    'type' => 'group',
                    'key' => 'provider-management',
                    'label' => 'Provider Management',
                    'icon' => 'building-2',

                    'items' => [

                        [
                            'key' => 'providers',
                            'label' => 'Providers',
                            'icon' => 'building-2',
                        ],

                        [
                            'key' => 'services',
                            'label' => 'Services',
                            'icon' => 'briefcase',
                        ],

                    ],
                ],

                [
                    'type' => 'group',
                    'key' => 'trip-management',
                    'label' => 'Trip Management',
                    'icon' => 'route',

                    'items' => [

                        [
                            'key' => 'trips',
                            'label' => 'Trips',
                            'icon' => 'route',
                        ],

                        [
                            'key' => 'trip-items',
                            'label' => 'Itinerary Items',
                            'icon' => 'list',
                        ],

                    ],
                ],

                [
                    'type' => 'group',
                    'key' => 'financial-management',
                    'label' => 'Financial Management',
                    'icon' => 'wallet',

                    'items' => [

                        [
                            'key' => 'payments',
                            'label' => 'Payments',
                            'icon' => 'credit-card',
                        ],

                    ],
                ],

                [
                    'type' => 'group',
                    'key' => 'platform-management',
                    'label' => 'Platform Management',
                    'icon' => 'shield-check',

                    'items' => [

                        [
                            'key' => 'users',
                            'label' => 'Users',
                            'icon' => 'users',
                        ],

                        [
                            'key' => 'platform-settings',
                            'label' => 'Platform Settings',
                            'icon' => 'settings',
                        ],

                    ],
                ],

            ],
        ],

        /*
        |----------------------------------------------------------------------
        | Content Administrator
        |----------------------------------------------------------------------
        */

        'content_admin' => [

            'label' => 'Content Administrator',

            'workspace' => 'Content Workspace',

            'description' => 'Manage and review tourism content across JST.',

            'navigation' => [

                [
                    'type' => 'link',
                    'key' => 'overview',
                    'label' => 'Overview',
                    'icon' => 'layout-dashboard',
                ],

                [
                    'type' => 'group',
                    'key' => 'content-management',
                    'label' => 'Content Management',
                    'icon' => 'folder-open',

                    'items' => [

                        [
                            'key' => 'destinations',
                            'label' => 'Destinations',
                            'icon' => 'map-pin',
                        ],

                        [
                            'key' => 'events',
                            'label' => 'Events',
                            'icon' => 'calendar-days',
                        ],

                        [
                            'key' => 'activities',
                            'label' => 'Activities',
                            'icon' => 'activity',
                        ],

                        [
                            'key' => 'experiences',
                            'label' => 'Experiences',
                            'icon' => 'sparkles',
                        ],

                    ],
                ],

                [
                    'type' => 'group',
                    'key' => 'classification',
                    'label' => 'Classification',
                    'icon' => 'tags',

                    'items' => [

                        [
                            'key' => 'categories',
                            'label' => 'Categories',
                            'icon' => 'tags',
                        ],

                        [
                            'key' => 'governorates',
                            'label' => 'Governorates',
                            'icon' => 'map',
                        ],

                    ],
                ],

                [
                    'type' => 'group',
                    'key' => 'content-operations',
                    'label' => 'Content Operations',
                    'icon' => 'clipboard-check',

                    'items' => [

                        [
                            'key' => 'content-review',
                            'label' => 'Content Review',
                            'icon' => 'clipboard-check',
                        ],

                        [
                            'key' => 'media-library',
                            'label' => 'Media Library',
                            'icon' => 'image',
                        ],

                    ],
                ],

            ],
        ],

        /*
        |----------------------------------------------------------------------
        | Tourism Provider
        |----------------------------------------------------------------------
        */

        'provider' => [

            'label' => 'Tourism Provider',

            'workspace' => 'Provider Workspace',

            'description' => 'Manage your organization and tourism listings.',

            'navigation' => [

                [
                    'type' => 'link',
                    'key' => 'overview',
                    'label' => 'Overview',
                    'icon' => 'layout-dashboard',
                ],

                [
                    'type' => 'group',
                    'key' => 'business-management',
                    'label' => 'My Business',
                    'icon' => 'building-2',

                    'items' => [

                        [
                            'key' => 'organization-profile',
                            'label' => 'Organization Profile',
                            'icon' => 'user',
                        ],

                        [
                            'key' => 'my-services',
                            'label' => 'My Services',
                            'icon' => 'briefcase',
                        ],

                    ],
                ],

                [
                    'type' => 'group',
                    'key' => 'my-listings',
                    'label' => 'My Listings',
                    'icon' => 'store',

                    'items' => [

                        [
                            'key' => 'my-activities',
                            'label' => 'Activities',
                            'icon' => 'activity',
                        ],

                        [
                            'key' => 'my-experiences',
                            'label' => 'Experiences',
                            'icon' => 'sparkles',
                        ],

                    ],
                ],

                [
                    'type' => 'link',
                    'key' => 'my-submissions',
                    'label' => 'My Submissions',
                    'icon' => 'file-text',
                ],

                [
                    'type' => 'link',
                    'key' => 'account-settings',
                    'label' => 'Account Settings',
                    'icon' => 'settings',
                ],

            ],
        ],

    ],

];