@extends('layouts.dashboard')

@section('dashboard-content')
    <div class="dashboard-page" data-dashboard-content>
        <div class="dashboard-page__heading">
            <div>
                <h1>Overview</h1>
                <p class="dashboard-range" data-date-range>Last 30 days</p>
            </div>
            <div class="dashboard-actions">
                <input type="hidden" name="period" value="30d" data-period>
                <div class="period-control" data-period-control>
                    <button type="button" class="period-trigger" data-period-trigger aria-haspopup="menu"
                        aria-expanded="false" aria-controls="dashboard-period-options">
                        <i data-lucide="calendar-range" aria-hidden="true"></i>
                        <span><span class="sr-only">Reporting period: </span><span data-period-label>Last 30 days</span></span>
                        <i class="period-control__chevron" data-lucide="chevron-down" aria-hidden="true"></i>
                    </button>
                    <div class="period-menu" id="dashboard-period-options" role="menu" aria-label="Reporting period" hidden>
                        <button type="button" class="period-option" role="menuitemradio" aria-checked="false" tabindex="-1" data-period-option="today">
                            <span>Today</span><i class="period-option__check" data-lucide="check" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="period-option" role="menuitemradio" aria-checked="false" tabindex="-1" data-period-option="7d">
                            <span>Last 7 days</span><i class="period-option__check" data-lucide="check" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="period-option is-selected" role="menuitemradio" aria-checked="true" tabindex="-1" data-period-option="30d">
                            <span>Last 30 days</span><i class="period-option__check" data-lucide="check" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="period-option" role="menuitemradio" aria-checked="false" tabindex="-1" data-period-option="90d">
                            <span>Last 90 days</span><i class="period-option__check" data-lucide="check" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="period-option" role="menuitemradio" aria-checked="false" tabindex="-1" data-period-option="this_month">
                            <span>This month</span><i class="period-option__check" data-lucide="check" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                <button type="button" class="button button--outline dashboard-export" data-export>
                    <i data-lucide="download" aria-hidden="true"></i><span data-export-label>Export CSV</span>
                </button>
                <span class="sr-only" role="status" aria-live="polite" data-export-status></span>
            </div>
        </div>

        <p class="dashboard-message hidden" role="status" aria-live="polite" data-dashboard-message></p>
        <div class="dashboard-skeleton" data-dashboard-loading aria-label="Loading platform data">
            <span></span><span></span><span></span><span></span><span></span><span></span></div>
        <section class="dashboard-data hidden" data-dashboard-data aria-label="Platform overview data">
            <div class="metric-grid">
                <article class="metric-card">
                    <div class="metric-card__top"><span>New destinations</span><span class="metric-icon"><i
                                data-lucide="map-pin" aria-hidden="true"></i></span></div><strong
                        data-kpi="total_destinations">—</strong>
                </article>
                <article class="metric-card">
                    <div class="metric-card__top"><span>New travelers</span><span
                            class="metric-icon metric-icon--sage"><i data-lucide="users" aria-hidden="true"></i></span>
                    </div><strong data-kpi="new_travelers">—</strong>
                </article>
                <article class="metric-card">
                    <div class="metric-card__top"><span>New providers</span><span class="metric-icon metric-icon--gold"><i
                                data-lucide="building-2" aria-hidden="true"></i></span></div><strong
                        data-kpi="total_providers">—</strong>
                </article>
                <article class="metric-card">
                    <div class="metric-card__top"><span>New activities</span><span class="metric-icon metric-icon--sage"><i
                                data-lucide="activity" aria-hidden="true"></i></span></div><strong
                        data-kpi="total_activities">—</strong>
                </article>
                <article class="metric-card">
                    <div class="metric-card__top"><span>New events</span><span class="metric-icon metric-icon--gold"><i
                                data-lucide="calendar-days" aria-hidden="true"></i></span></div><strong
                        data-kpi="total_events">—</strong>
                </article>
                <article class="metric-card">
                    <div class="metric-card__top"><span>Bookings</span><span class="metric-icon"><i
                                data-lucide="ticket-check" aria-hidden="true"></i></span></div><strong
                        data-kpi="total_bookings">—</strong>
                </article>
            </div>

            <div class="finance-grid">
                <article class="finance-card">
                    <div class="finance-card__label"><i data-lucide="wallet-cards" aria-hidden="true"></i><span>Booking GMV</span></div>
                    <strong data-kpi-money="booking_gmv">—</strong>
                </article>
                <article class="finance-card finance-card--commission">
                    <div class="finance-card__label"><i data-lucide="chart-no-axes-combined" aria-hidden="true"></i><span>Platform commission</span></div>
                    <strong data-kpi-money="platform_commission">—</strong>
                </article>
            </div>

            <div class="dashboard-panels dashboard-panels--charts">
                <article class="panel-card revenue-panel">
                    <div class="panel-heading">
                        <div>
                            <h2><i data-lucide="chart-no-axes-combined" aria-hidden="true"></i>Revenue over time</h2>
                            <p>Completed booking GMV and commission · JOD</p>
                        </div>
                    </div>
                    <div class="chart-wrap"><canvas data-revenue-chart
                            aria-label="Line chart showing booking GMV and platform commission over time"></canvas>
                        <p class="chart-empty hidden" data-revenue-empty>No completed bookings in this period.</p>
                    </div>
                    <div class="chart-legend"><span><i class="legend-dot legend-dot--teal"></i> Booking GMV</span><span><i
                                class="legend-dot legend-dot--gold"></i> Platform commission</span></div>
                </article>
                <article class="panel-card status-panel">
                    <div class="panel-heading">
                        <h2><i data-lucide="ticket-check" aria-hidden="true"></i>Booking status</h2>
                    </div>
                    <div class="status-chart-wrap"><canvas data-status-chart
                            aria-label="Doughnut chart showing booking status distribution"></canvas>
                        <div class="status-chart-center"><strong data-booking-total>0</strong><span>Total</span></div>
                        <p class="chart-empty hidden" data-status-empty>No bookings in this period.</p>
                    </div>
                    <ul class="status-legend" data-status-legend></ul>
                </article>
            </div>

            <div class="dashboard-panels dashboard-panels--lower">
                <article class="panel-card providers-panel">
                    <div class="panel-heading">
                        <div>
                            <h2>Leading providers</h2>
                        </div>
                    </div>
                    <div class="table-scroll" data-provider-table-wrap>
                        <table class="provider-table">
                            <thead>
                                <tr>
                                    <th scope="col">Provider</th>
                                    <th scope="col">Bookings</th>
                                    <th scope="col">Completed GMV</th>
                                </tr>
                            </thead>
                            <tbody data-provider-rows></tbody>
                        </table>
                    </div>
                    <p class="panel-empty hidden" data-provider-empty>No provider bookings in this period.</p>
                </article>
                <article class="panel-card activity-panel">
                    <div class="panel-heading">
                        <div>
                            <h2>Recent activity</h2>
                        </div>
                    </div>
                    <ol class="activity-list" data-activity-list></ol>
                    <p class="panel-empty hidden" data-activity-empty>No recent platform activity.</p>
                </article>
            </div>

            <section class="alerts-panel hidden" data-alerts-panel aria-label="Operational alerts">
                <div><span class="alert-mark"><i data-lucide="circle-alert" aria-hidden="true"></i></span>
                    <div>
                        <h2 data-alert-title>Platform alert</h2>
                        <p data-alert-description></p>
                    </div>
                </div><span class="alert-count" data-alert-count></span>
            </section>
        </section>
        <div class="dashboard-error hidden" data-dashboard-error role="alert"><span class="dashboard-error__icon"><i
                    data-lucide="wifi-off" aria-hidden="true"></i></span>
            <h2 data-error-title>We couldn’t load the dashboard.</h2>
            <p data-error-description>Check your connection and try again.</p><button class="button button--outline"
                type="button" data-retry><i data-lucide="rotate-cw" aria-hidden="true"></i> Try again</button>
        </div>
    </div>
@endsection
