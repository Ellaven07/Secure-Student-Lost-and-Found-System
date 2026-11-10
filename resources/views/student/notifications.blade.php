
@extends(auth()->user()->isAdmin() ? 'layouts.app' : 'layouts.student')

@section('title', auth()->user()->isAdmin() ? 'Admin Notifications' : 'Notifications')
@section('page-label', 'Notifications')

@section('content')

<style>
    .notifications-page { color: #263a30; }

    .notification-heading { margin-bottom: 24px; }
    .notification-heading h1 {
        color: #174b38;
        font-size: 25px;
        font-weight: 700;
        margin-bottom: 6px;
    }
    .notification-heading p {
        color: #78857d;
        font-size: 14px;
    }

    .notification-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 22px;
    }
    .notification-stat {
        background: white;
        border: 1px solid #e8eee9;
        border-radius: 12px;
        padding: 19px;
        box-shadow: 0 3px 12px rgba(24,60,43,.04);
    }
    .notification-stat span {
        color: #829087;
        font-size: 12px;
    }
    .notification-stat h2 {
        color: #174b38;
        font-size: 25px;
        font-weight: 700;
        margin-top: 7px;
    }

    .notification-panel {
        background: white;
        border: 1px solid #e8eee9;
        border-radius: 13px;
        padding: 22px;
        box-shadow: 0 3px 12px rgba(24,60,43,.04);
    }
    .notification-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }
    .notification-toolbar h2 {
        color: #174b38;
        font-size: 17px;
        font-weight: 700;
    }
    .notification-toolbar p {
        color: #87928b;
        font-size: 12px;
        margin-top: 5px;
    }

    .notification-actions {
        display: flex;
        gap: 9px;
        flex-wrap: wrap;
    }
    .notification-actions button {
        border: 1px solid #e2e9e3;
        background: #fff;
        color: #496052;
        padding: 9px 12px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
    }
    .notification-actions button:hover {
        background: #f1f6f2;
    }

    .notification-filters {
        display: flex;
        gap: 8px;
        border-bottom: 1px solid #edf1ed;
        margin-bottom: 8px;
        overflow-x: auto;
    }
    .filter-btn {
        border: 0;
        background: transparent;
        color: #7a897f;
        padding: 12px 15px;
        font-size: 13px;
        cursor: pointer;
        white-space: nowrap;
        border-bottom: 2px solid transparent;
    }
    .filter-btn.active {
        color: #174b38;
        border-bottom-color: #d7b65d;
        font-weight: 700;
    }

    .notification-item {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 18px 10px;
        border-bottom: 1px solid #edf1ed;
        transition: background .2s;
    }
    .notification-item:last-child { border-bottom: 0; }
    .notification-item.unread {
        background: #f8faf8;
    }

    .notification-icon {
        width: 42px;
        height: 42px;
        flex-shrink: 0;
        border-radius: 11px;
        background: #edf4ef;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 19px;
    }
    .notification-item.success .notification-icon {
        background: #e7f5eb;
    }
    .notification-item.warning .notification-icon {
        background: #fff5df;
    }
    .notification-item.info .notification-icon {
        background: #eaf1fb;
    }

    .notification-content { flex: 1; min-width: 0; }
    .notification-content h3 {
        color: #30483a;
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 5px;
    }
    .notification-content p {
        color: #748178;
        font-size: 13px;
        line-height: 1.6;
        margin-bottom: 7px;
    }
    .notification-time {
        color: #9aa49d;
        font-size: 11px;
    }

    .unread-dot {
        width: 8px;
        height: 8px;
        background: #d7b65d;
        border-radius: 50%;
        margin-top: 7px;
        flex-shrink: 0;
    }
    .read-dot { visibility: hidden; }

    .notification-read-button {
        border: 0;
        background: transparent;
        color: #28634c;
        padding: 3px 0;
        font: inherit;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
    }
    .notification-read-button:hover { text-decoration: underline; }

    .empty-notifications {
        display: none;
        text-align: center;
        padding: 45px 15px;
        color: #849087;
        font-size: 13px;
    }
    .empty-notifications span {
        display: block;
        font-size: 34px;
        margin-bottom: 12px;
    }

    @media(max-width: 650px) {
        .notification-stats { grid-template-columns: 1fr; }
        .notification-panel { padding: 15px; }
        .notification-item { gap: 10px; }
        .notification-icon { width: 36px; height: 36px; }
    }
</style>

<div class="notifications-page">

    <div class="notification-heading">
        <h1>{{ $isAdmin ? 'Admin Notifications' : 'Notifications' }}</h1>
        <p>
            {{ $isAdmin
                ? 'Review student account activity and administration updates.'
                : 'See account updates and messages from your CvSU administrators.' }}
        </p>
    </div>

    <div class="notification-stats">
        <div class="notification-stat">
            <span>Total Notifications</span>
            <h2>{{ number_format($totalNotifications) }}</h2>
        </div>
        <div class="notification-stat">
            <span>Unread Notifications</span>
            <h2>{{ number_format($unreadNotifications) }}</h2>
        </div>
        <div class="notification-stat">
            <span>Read Notifications</span>
            <h2>{{ number_format($readNotifications) }}</h2>
        </div>
    </div>

    <div class="notification-panel">
        <div class="notification-toolbar">
            <div>
                <h2>Recent Notifications</h2>
                <p>
                    {{ $isAdmin
                        ? 'New student accounts and updates that need your attention.'
                        : 'Messages about your student account and its status.' }}
                </p>
            </div>

            <div class="notification-actions">
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" @disabled($unreadNotifications === 0)>Mark all as read</button>
                </form>
                <form method="POST" action="{{ route('notifications.clear') }}" onsubmit="return confirm('Clear all your notifications?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" @disabled($totalNotifications === 0)>Clear all</button>
                </form>
            </div>
        </div>

        <div class="notification-filters">
            <button class="filter-btn active" type="button" onclick="filterNotifications('all', this)">All</button>
            <button class="filter-btn" type="button" onclick="filterNotifications('unread', this)">Unread</button>
            <button class="filter-btn" type="button" onclick="filterNotifications('read', this)">Read</button>
        </div>

        <div id="notificationList">
            @forelse ($notifications as $notification)
                @php
                    $isRead = $notification->read_at !== null;
                    $notificationStyle = match ($notification->type) {
                        'success' => ['class' => 'success', 'icon' => '✓'],
                        'warning' => ['class' => 'warning', 'icon' => '!'],
                        default => ['class' => 'info', 'icon' => 'i'],
                    };
                @endphp

                <article
                    class="notification-item {{ $isRead ? '' : 'unread' }} {{ $notificationStyle['class'] }}"
                    data-status="{{ $isRead ? 'read' : 'unread' }}"
                >
                    <div class="notification-icon" aria-hidden="true">{{ $notificationStyle['icon'] }}</div>
                    <div class="notification-content">
                        <h3>{{ $notification->title }}</h3>
                        <p>{{ $notification->message }}</p>
                        <span class="notification-time">
                            {{ \Illuminate\Support\Carbon::parse($notification->created_at)->diffForHumans() }}
                        </span>
                        @if (! $isRead)
                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                @csrf
                                @method('PATCH')
                                <button class="notification-read-button" type="submit">Mark as read</button>
                            </form>
                        @endif
                    </div>
                    <span class="unread-dot {{ $isRead ? 'read-dot' : '' }}" aria-label="{{ $isRead ? 'Read' : 'Unread' }}"></span>
                </article>
            @empty
                <div class="empty-notifications" id="emptyNotifications" style="display: block;">
                    <span aria-hidden="true">♢</span>
                    {{ $isAdmin ? 'No administrator notifications yet.' : 'No student notifications yet.' }}
                </div>
            @endforelse
        </div>

        @if ($notifications->isNotEmpty())
            <div class="empty-notifications" id="emptyNotifications">
                <span aria-hidden="true">♢</span>
                No notifications match this filter.
            </div>
            <div class="pagination-wrapper">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>

<script>
    function filterNotifications(status, button) {
        document.querySelectorAll('.filter-btn').forEach((filterButton) => {
            filterButton.classList.remove('active');
        });

        button.classList.add('active');

        let visible = 0;

        document.querySelectorAll('.notification-item').forEach((item) => {
            const matches = status === 'all' || item.dataset.status === status;
            item.style.display = matches ? 'flex' : 'none';

            if (matches) {
                visible++;
            }
        });

        const emptyState = document.getElementById('emptyNotifications');

        if (emptyState) {
            emptyState.style.display = visible === 0 ? 'block' : 'none';
        }
    }
</script>

@endsection
