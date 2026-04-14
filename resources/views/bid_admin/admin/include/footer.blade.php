<!-- jQuery -->
<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>
<!-- Bootstrap Core JS -->
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>
<!-- Feather Icon JS -->
<script src="{{ asset('assets/js/feather.min.js') }}" type="text/javascript"></script>
<!-- Slimscroll JS -->
<script src="{{ asset('assets/plugins/slimscroll/jquery.slimscroll.min.js') }}" type="text/javascript"></script>
<!-- Theme Settings JS -->
<script src="{{ asset('assets/js/theme-settings.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/js/greedynav.js') }}" type="text/javascript"></script>
<!-- Custom JS -->
<script src="{{ asset('assets/js/script.js') }}" type="text/javascript"></script>
@stack('scripts')
<script type="text/javascript">
   (function () {
      var wrapTablesForMobile = function () {
         var tables = document.querySelectorAll('.content table');

         tables.forEach(function (table) {
            if (table.closest('.admin-responsive-table') || table.closest('.table-responsive')) {
               return;
            }

            var wrapper = document.createElement('div');
            wrapper.className = 'admin-responsive-table';
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
         });
      };

      wrapTablesForMobile();

      var bell = document.getElementById('notification_popup');
      var list = document.getElementById('adminNotificationList');
      var badge = document.getElementById('adminNotificationBadge');
      var markAllButton = document.getElementById('adminNotificationMarkAll');

      if (!bell || !list) {
         return;
      }

      var dataUrl = bell.getAttribute('data-notifications-url');
      var markReadUrl = bell.getAttribute('data-notifications-mark-read');
      var markAllUrl = bell.getAttribute('data-notifications-mark-all');
      var csrfToken = document.querySelector('meta[name="csrf-token"]');
      var tokenValue = csrfToken ? csrfToken.getAttribute('content') : '';
      var currentUnread = 0;

      var typeMeta = function (type) {
         if (type === 'success') {
            return { icon: 'bi-check-circle-fill', color: 'text-success', bg: 'bg-success-subtle' };
         }
         if (type === 'danger' || type === 'error') {
            return { icon: 'bi-x-circle-fill', color: 'text-danger', bg: 'bg-danger-subtle' };
         }
         if (type === 'warning') {
            return { icon: 'bi-exclamation-triangle-fill', color: 'text-warning', bg: 'bg-warning-subtle' };
         }
         return { icon: 'bi-info-circle-fill', color: 'text-info', bg: 'bg-info-subtle' };
      };

      var updateBadge = function (count) {
         currentUnread = typeof count === 'number' ? count : currentUnread;
         badge.textContent = currentUnread > 0 ? String(currentUnread) : '';
         badge.style.display = currentUnread > 0 ? 'inline-block' : 'none';
      };

      var escapeHtml = function (value) {
         return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
      };

      var renderItems = function (items) {
         if (!items || !items.length) {
            list.innerHTML = '<div class="text-center text-muted py-4">No notifications yet.</div>';
            return;
         }

         list.innerHTML = items.map(function (item) {
            var meta = typeMeta(item.type);
            var unreadClass = item.is_read ? '' : 'unread';
            var link = item.url ? item.url : 'javascript:void(0);';

            return (
               '<a class="dropdown-item notification-item py-2 text-wrap border-bottom ' + unreadClass + '" ' +
               'data-notification-id="' + item.id + '" href="' + link + '">' +
               '<div class="d-flex">' +
               '<div class="flex-shrink-0">' +
               '<div class="avatar-sm me-2">' +
               '<span class="avatar-title ' + meta.bg + ' ' + meta.color + ' fs-18 rounded-circle">' +
               '<i class="bi ' + meta.icon + '"></i>' +
               '</span>' +
               '</div>' +
               '</div>' +
               '<div class="flex-grow-1">' +
               '<p class="mb-0 fw-semibold text-dark">' + escapeHtml(item.title) + '</p>' +
               '<p class="mb-1 text-wrap fs-14">' + escapeHtml(item.message) + '</p>' +
               '<div class="d-flex justify-content-between align-items-center">' +
               '<span class="fs-12"><i class="isax isax-clock me-1"></i>' + escapeHtml(item.time) + '</span>' +
               '</div>' +
               '</div>' +
               '</div>' +
               '</a>'
            );
         }).join('');
      };

      var fetchNotifications = function () {
         if (!dataUrl || typeof window.jQuery === 'undefined') {
            return;
         }

         return window.jQuery.get(dataUrl, { limit: 5 }).done(function (response) {
            updateBadge(parseInt(response.unread_count, 10) || 0);
            renderItems(response.items || []);
         });
      };

      var markRead = function (id) {
         if (!markReadUrl) {
            return;
         }

         if (navigator.sendBeacon && tokenValue) {
            var params = new URLSearchParams();
            params.append('id', id);
            params.append('_token', tokenValue);
            navigator.sendBeacon(markReadUrl, params);
            return;
         }

         if (typeof window.jQuery !== 'undefined') {
            window.jQuery.post(markReadUrl, { id: id, _token: tokenValue });
         }
      };

      var markAllRead = function () {
         if (!markAllUrl) {
            return;
         }

         if (navigator.sendBeacon && tokenValue) {
            var params = new URLSearchParams();
            params.append('_token', tokenValue);
            navigator.sendBeacon(markAllUrl, params);
            updateBadge(0);
            return;
         }

         if (typeof window.jQuery !== 'undefined') {
            window.jQuery.post(markAllUrl, { _token: tokenValue }).done(function (response) {
               updateBadge(parseInt(response.unread_count, 10) || 0);
               fetchNotifications();
            });
         }
      };

      list.addEventListener('click', function (event) {
         var target = event.target.closest('[data-notification-id]');
         if (!target) {
            return;
         }

         var id = target.getAttribute('data-notification-id');
         if (!id) {
            return;
         }

         markRead(id);
         if (currentUnread > 0) {
            updateBadge(currentUnread - 1);
         }
      });

      if (markAllButton) {
         markAllButton.addEventListener('click', function (event) {
            event.preventDefault();
            markAllRead();
         });
      }

      bell.addEventListener('shown.bs.dropdown', function () {
         fetchNotifications();
      });

      fetchNotifications();
      setInterval(fetchNotifications, 15000);
   })();
</script>
</body>
</html>
