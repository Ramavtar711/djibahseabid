@include('bid_admin.qc.include.header')

@include('bid_admin.qc.include.side_menu')

<style>
/* Notification Cards */
.notify-card {
    background: var(--glass);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    padding: 20px;
    display: flex;
    gap: 20px;
    color: #fff;
    margin-bottom: 15px;
    position: relative;
    transition: 0.3s ease;
}

.notify-card:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: translateX(5px);
}

.notify-card.urgent {
    border-left: 4px solid #ef4444;
    background: var(--glass);
}

/* Icons */
.notify-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

/* Content */
.notify-content { flex-grow: 1; }
.notify-time { font-size: 0.75rem; opacity: 0.5; }

.unread-dot {
    width: 8px;
    height: 8px;
    background: yellow;
    border-radius: 50%;
    position: absolute;
    right: 20px;
    top: 45px;
}

.time-label {
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #3b82f6;
    margin-left: 5px;
}

/* Buttons */
.btn-action-sm {
    background: rgba(255,255,255,0.05);
    border: 1px solid var(--glass-border);
    color: white;
    font-size: 0.8rem;
    padding: 8px 15px;
    border-radius: 10px;
}

.btn-notify-link {
    display: inline-block;
    margin-top: 10px;
    font-size: 0.8rem;
    color: #3b82f6;
    text-decoration: none;
    font-weight: 600;
}

.btn-notify-link:hover { text-decoration: underline; }
</style>

<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="page-title text-white">Notifications</h1>
                    <p class="text-white">Stay updated with QC activity</p>
                </div>
                <div class="col-auto"></div>
            </div>
        </div>

        <div class="notification-container" id="qcNotificationPageList">
            <div class="text-white-50">Loading notifications...</div>
        </div>
    </div>
</div>

@include('bid_admin.qc.include.footer')
