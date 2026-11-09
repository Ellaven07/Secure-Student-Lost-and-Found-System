
@extends('layouts.app')

@section('title', 'Notifications')
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
        <h1>Notifications</h1>
        <p>Stay updated on lost and found items and important announcements.</p>
    </div>

    <div class="notification-stats">
        <div class="notification-stat">
            <span>Total Notifications</span>
            <h2 id="totalCount">5</h2>
        </div>
        <div class="notification-stat">
            <span>Unread Notifications</span>
            <h2 id="unreadCount">3</h2>
        </div>
        <div class="notification-stat">
            <span>Read Notifications</span>
            <h2 id="readCount">2</h2>
        </div>
    </div>

    <div class="notification-panel">

        <div class="notification-toolbar">
            <div>
                <h2>Recent Notifications</h2>
                <p>Updates and alerts from the Lost and Found System</p>
            </div>

            <div class="notification-actions">
                <button type="button" onclick="markAllRead()">Mark all as read</button>
                <button type="button" onclick="clearNotifications()">Clear all</button>
            </div>
        </div>

        <div class="notification-filters">
            <button class="filter-btn active" onclick="filterNotifications('all', this)">All</button>
            <button class="filter-btn" onclick="filterNotifications('unread', this)">Unread</button>
            <button class="filter-btn" onclick="filterNotifications('read', this)">Read</button>
        </div>

        <div id="notificationList">

            <div class="notification-item unread success" data-status="unread">
                <div class="notification-icon">✓</div>
                <div class="notification-content">
                    <h3>Item Successfully Reported</h3>
                    <p>Your lost item report has been submitted successfully. You can check its status in your reports.</p>
                    <span class="notification-time">Today · 9:30 AM</span>
                </div>
                <span class="unread-dot"></span>
            </div>

            <div class="notification-item unread info" data-status="unread">
                <div class="notification-icon">🔎</div>
                <div class="notification-content">
                    <h3>Possible Item Match</h3>
                    <p>A newly reported found item may match the description of an item you reported.</p>
                    <span class="notification-time">Today · 8:15 AM</span>
                </div>
                <span class="unread-dot"></span>
            </div>

            <div class="notification-item unread warning" data-status="unread">
                <div class="notification-icon">📢</div>
                <div class="notification-content">
                    <h3>Claim Reminder</h3>
                    <p>Please remember to bring your valid student ID when claiming a found item.</p>
                    <span class="notification-time">Yesterday · 3:45 PM</span>
                </div>
                <span class="unread-dot"></span>
            </div>

            <div class="notification-item" data-status="read">
                <div class="notification-icon">📦</div>
                <div class="notification-content">
                    <h3>Found Item Received</h3>
                    <p>Your reported item has been received by the Lost and Found office for verification.</p>
                    <span class="notification-time">November 7 · 10:20 AM</span>
                </div>
                <span class="unread-dot read-dot"></span>
            </div>

            <div class="notification-item" data-status="read">
                <div class="notification-icon">ℹ️</div>
                <div class="notification-content">
                    <h3>System Announcement</h3>
                    <p>Always provide accurate descriptions and clear photos when submitting lost or found item reports.</p>
                    <span class="notification-time">November 6 · 1:00 PM</span>
                </div>
                <span class="unread-dot read-dot"></span>
            </div>

        </div>

        <div class="empty-notifications" id="emptyNotifications">
            <span>🔔</span>
            No notifications found.
        </div>

    </div>

</div>

<script>
    function updateNotificationCounts() {
        const items = document.querySelectorAll('.notification-item');
        const unread = document.querySelectorAll('.notification-item[data-status="unread"]').length;
        const total = items.length;

        document.getElementById('totalCount').textContent = total;
        document.getElementById('unreadCount').textContent = unread;
        document.getElementById('readCount').textContent = total - unread;

        document.querySelectorAll('.notification-item').forEach(item => {
            const dot = item.querySelector('.unread-dot');

            if (item.dataset.status === 'read') {
                item.classList.remove('unread');
                dot.classList.add('read-dot');
            } else {
                item.classList.add('unread');
                dot.classList.remove('read-dot');
            }
        });
    }

    function filterNotifications(status, button) {
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        button.classList.add('active');

        let visible = 0;

        document.querySelectorAll('.notification-item').forEach(item => {
            const matches = status === 'all' || item.dataset.status === status;
            item.style.display = matches ? 'flex' : 'none';

            if (matches) visible++;
        });

        document.getElementById('emptyNotifications').style.display =
            visible === 0 ? 'block' : 'none';
    }

    function markAllRead() {
        document.querySelectorAll('.notification-item').forEach(item => {
            item.dataset.status = 'read';
        });

        updateNotificationCounts();

        const activeFilter = document.querySelector('.filter-btn.active');

        if (activeFilter) {
            filterNotifications('all', document.querySelector('.filter-btn'));
        }
    }

    function clearNotifications() {
        if (!confirm('Are you sure you want to clear all sample notifications?')) {
            return;
        }

        document.getElementById('notificationList').innerHTML = '';
        updateNotificationCounts();

        document.getElementById('emptyNotifications').style.display = 'block';
    }

    updateNotificationCounts();
</script>

@endsection
