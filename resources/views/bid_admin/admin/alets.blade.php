@include('bid_admin.admin.include.header')

@include('bid_admin.admin.include.side_menu')

<style>
   .status-header {
      background: linear-gradient(135deg, rgba(8, 47, 73, 0.92), rgba(14, 116, 144, 0.82));
      border-radius: 18px;
      padding: 18px 24px;
      margin-bottom: 28px;
      color: #fff;
      box-shadow: 0 18px 40px rgba(8, 47, 73, 0.18);
   }

   .status-pill {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 13px;
      font-weight: 600;
      color: rgba(255, 255, 255, 0.92);
   }

   .alerts-shell {
      background:
         linear-gradient(180deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.04)),
         radial-gradient(circle at top left, rgba(56, 189, 248, 0.16), transparent 38%),
         #f4fbff;
      border-radius: 28px;
      padding: 28px;
      min-height: calc(100vh - 180px);
   }

   .alerts-title {
      font-size: 32px;
      font-weight: 800;
      color: #0f172a;
      margin-bottom: 6px;
   }

   .alerts-subtitle {
      color: #64748b;
      margin-bottom: 0;
   }

   .alerts-summary {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 12px 18px;
      border-radius: 999px;
      background: rgba(15, 118, 110, 0.1);
      color: #0f766e;
      font-weight: 700;
   }

   .notify-page-list {
      margin-top: 24px;
   }

   .notify-card {
      position: relative;
      display: flex;
      gap: 18px;
      align-items: flex-start;
      padding: 22px 24px;
      margin-bottom: 16px;
      border-radius: 20px;
      background: rgba(255, 255, 255, 0.88);
      border: 1px solid rgba(148, 163, 184, 0.18);
      box-shadow: 0 18px 36px rgba(15, 23, 42, 0.08);
      transition: transform 0.18s ease, box-shadow 0.18s ease;
   }

   .notify-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 22px 44px rgba(15, 23, 42, 0.12);
   }

   .notify-card.is-unread {
      border-left: 5px solid #2563eb;
      background: linear-gradient(135deg, rgba(239, 246, 255, 0.95), rgba(255, 255, 255, 0.92));
   }

   .notify-card.is-read {
      opacity: 0.92;
   }

   .notify-icon {
      width: 54px;
      height: 54px;
      border-radius: 16px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
      flex-shrink: 0;
   }

   .notify-content {
      flex: 1;
      min-width: 0;
   }

   .notify-title {
      color: #0f172a;
      font-weight: 800;
      font-size: 18px;
      margin-bottom: 4px;
   }

   .notify-message {
      color: #475569;
      margin-bottom: 12px;
      font-size: 15px;
   }

   .notify-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      align-items: center;
   }

   .notify-time {
      color: #64748b;
      font-size: 13px;
      font-weight: 600;
   }

   .notify-tag {
      display: inline-flex;
      align-items: center;
      padding: 6px 10px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 700;
      text-transform: capitalize;
   }

   .notify-dot {
      position: absolute;
      top: 22px;
      right: 22px;
      width: 12px;
      height: 12px;
      border-radius: 50%;
      background: #2563eb;
      box-shadow: 0 0 0 5px rgba(37, 99, 235, 0.12);
   }

   .alerts-empty {
      text-align: center;
      padding: 60px 20px;
      border-radius: 24px;
      background: rgba(255, 255, 255, 0.72);
      border: 1px dashed rgba(148, 163, 184, 0.5);
      color: #64748b;
   }

   .alerts-empty i {
      font-size: 34px;
      color: #0ea5e9;
      display: block;
      margin-bottom: 12px;
   }

   .alerts-loading {
      color: #475569;
      font-weight: 600;
   }

   @media (max-width: 767.98px) {
      .alerts-shell {
         padding: 18px;
         border-radius: 20px;
      }

      .alerts-title {
         font-size: 24px;
      }

      .notify-card {
         padding: 18px;
      }

      .status-header {
         padding: 16px;
      }
   }
</style>

<div class="page-wrapper">
   <div class="content container-fluid">
      <div class="status-header d-flex flex-wrap justify-content-between align-items-center gap-3">
         <div class="d-flex flex-wrap align-items-center gap-4">
            <div class="status-pill">
               <i class="bi bi-circle-fill text-success"></i>
               <span>SYSTEM STATUS: <strong>{{ $systemStatus ?? 'STANDBY' }}</strong></span>
            </div>
            <div class="status-pill">
               <i class="bi bi-circle-fill text-danger"></i>
               <span><strong>{{ $liveAuctionsCount ?? 0 }}</strong> Live Auctions</span>
            </div>
            <div class="status-pill">
               <i class="bi bi-circle-fill text-warning"></i>
               <span><strong>{{ $upcomingAuctionsCount ?? 0 }}</strong> Upcoming</span>
            </div>
            <div class="status-pill">
               <i class="bi bi-circle-fill text-success"></i>
               <span><strong>${{ number_format((float) ($revenueToday ?? 0), 2) }}</strong> Revenue Today</span>
            </div>
         </div>
         <div class="fw-bold">
            {{ $registeredBuyersCount ?? 0 }}
            <span class="text-white-50 fw-normal">Registered Buyers</span>
         </div>
      </div>

      <div class="alerts-shell">
         <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
            <div>
               <h1 class="alerts-title">Notifications</h1>
               <p class="alerts-subtitle">Admin alerts ab live notification data se aa rahe hain.</p>
            </div>

            <div class="d-flex flex-wrap gap-2 align-items-center">
               <div class="alerts-summary">
                  <i class="bi bi-bell-fill"></i>
                  <span><span id="adminPageUnreadCount">0</span> unread alerts</span>
               </div>
               <button type="button" class="btn btn-outline-primary" id="adminPageMarkAll">
                  <i class="bi bi-check2-all me-1"></i> Mark All as Read
               </button>
            </div>
         </div>

         <div class="notify-page-list" id="adminNotificationPageList">
            <div class="alerts-empty alerts-loading">
               <i class="bi bi-arrow-repeat"></i>
               Loading notifications...
            </div>
         </div>
      </div>
   </div>
</div>

@push('scripts')
<script type="text/javascript">
   (function () {
      var bell = document.getElementById('notification_popup');
      var pageList = document.getElementById('adminNotificationPageList');
      var pageUnreadCount = document.getElementById('adminPageUnreadCount');
      var pageMarkAllButton = document.getElementById('adminPageMarkAll');
      var csrfToken = document.querySelector('meta[name="csrf-token"]');
      var tokenValue = csrfToken ? csrfToken.getAttribute('content') : '';
      var dataUrl = bell ? bell.getAttribute('data-notifications-url') : '';
      var markReadUrl = bell ? bell.getAttribute('data-notifications-mark-read') : '';
      var markAllUrl = bell ? bell.getAttribute('data-notifications-mark-all') : '';
      var currentUnread = 0;
      var allItems = [];

      if (!pageList || !dataUrl || typeof window.jQuery === 'undefined') {
         return;
      }

      var escapeHtml = function (value) {
         return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
      };

      var typeMeta = function (type) {
         if (type === 'success') {
            return {
               icon: 'bi-check-circle-fill',
               iconClass: 'text-success',
               background: 'rgba(34, 197, 94, 0.12)',
               tagBackground: 'rgba(34, 197, 94, 0.14)',
               tagColor: '#15803d',
               label: 'success'
            };
         }
         if (type === 'danger' || type === 'error') {
            return {
               icon: 'bi-exclamation-octagon-fill',
               iconClass: 'text-danger',
               background: 'rgba(239, 68, 68, 0.12)',
               tagBackground: 'rgba(239, 68, 68, 0.14)',
               tagColor: '#b91c1c',
               label: 'risk'
            };
         }
         if (type === 'warning') {
            return {
               icon: 'bi-exclamation-triangle-fill',
               iconClass: 'text-warning',
               background: 'rgba(245, 158, 11, 0.14)',
               tagBackground: 'rgba(245, 158, 11, 0.18)',
               tagColor: '#b45309',
               label: 'warning'
            };
         }
         return {
            icon: 'bi-info-circle-fill',
            iconClass: 'text-info',
            background: 'rgba(14, 165, 233, 0.12)',
            tagBackground: 'rgba(14, 165, 233, 0.14)',
            tagColor: '#0369a1',
            label: 'info'
         };
      };

      var updateUnread = function (count) {
         currentUnread = typeof count === 'number' ? count : currentUnread;
         if (pageUnreadCount) {
            pageUnreadCount.textContent = String(currentUnread);
         }
      };

      var renderPageItems = function (items) {
         if (!items || !items.length) {
            pageList.innerHTML =
               '<div class="alerts-empty">' +
               '<i class="bi bi-bell-slash"></i>' +
               '<div class="fw-bold mb-1">No notifications yet</div>' +
               '<div>Jab nayi activity aayegi, yahin show hogi.</div>' +
               '</div>';
            return;
         }

         pageList.innerHTML = items.map(function (item) {
            var meta = typeMeta(item.type);
            var unreadClass = item.is_read ? 'is-read' : 'is-unread';
            var unreadDot = item.is_read ? '' : '<span class="notify-dot"></span>';
            var href = item.url ? item.url : 'javascript:void(0);';
            var title = escapeHtml(item.title);
            var message = escapeHtml(item.message);
            var time = escapeHtml(item.time);
            var tag = escapeHtml(meta.label);

            return (
               '<a href="' + href + '" class="text-decoration-none" data-notification-id="' + item.id + '">' +
               '<div class="notify-card ' + unreadClass + '">' +
               '<div class="notify-icon" style="background:' + meta.background + ';">' +
               '<i class="bi ' + meta.icon + ' ' + meta.iconClass + '"></i>' +
               '</div>' +
               '<div class="notify-content">' +
               '<div class="d-flex flex-wrap justify-content-between gap-2">' +
               '<div class="notify-title">' + title + '</div>' +
               '<div class="notify-time">' + time + '</div>' +
               '</div>' +
               '<div class="notify-message">' + message + '</div>' +
               '<div class="notify-meta">' +
               '<span class="notify-tag" style="background:' + meta.tagBackground + ';color:' + meta.tagColor + ';">' + tag + '</span>' +
               '</div>' +
               '</div>' +
               unreadDot +
               '</div>' +
               '</a>'
            );
         }).join('');
      };

      var fetchPageNotifications = function () {
         return window.jQuery.get(dataUrl, { limit: 50 }).done(function (response) {
            updateUnread(parseInt(response.unread_count, 10) || 0);
            allItems = response.items || [];
            renderPageItems(allItems);
         });
      };

      var markRead = function (id) {
         if (!markReadUrl || !id) {
            return;
         }

         return window.jQuery.post(markReadUrl, {
            id: id,
            _token: tokenValue
         }).done(function (response) {
            allItems = allItems.map(function (item) {
               if (String(item.id) === String(id)) {
                  item.is_read = true;
               }
               return item;
            });
            if (response && typeof response.unread_count !== 'undefined') {
               updateUnread(parseInt(response.unread_count, 10) || 0);
            }
            renderPageItems(allItems);
         });
      };

      var markAllRead = function () {
         if (!markAllUrl) {
            return;
         }

         return window.jQuery.post(markAllUrl, {
            _token: tokenValue
         }).done(function (response) {
            if (response && typeof response.unread_count !== 'undefined') {
               updateUnread(parseInt(response.unread_count, 10) || 0);
            } else {
               updateUnread(0);
            }
            fetchPageNotifications();
         });
      };

      pageList.addEventListener('click', function (event) {
         var target = event.target.closest('[data-notification-id]');
         if (!target) {
            return;
         }

         var id = target.getAttribute('data-notification-id');
         if (!id) {
            return;
         }

         markRead(id);
      });

      if (pageMarkAllButton) {
         pageMarkAllButton.addEventListener('click', function (event) {
            event.preventDefault();
            markAllRead();
         });
      }

      fetchPageNotifications();
      setInterval(fetchPageNotifications, 15000);
   })();
</script>
@endpush

@include('bid_admin.admin.include.footer')
