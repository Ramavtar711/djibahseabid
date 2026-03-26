<!-- jQuery -->
<script src="{{ asset('seller/assets/js/jquery-3.7.1.min.js') }}" type="text/javascript"></script>
<!-- Bootstrap Core JS -->
<script src="{{ asset('seller/assets/js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>
<!-- Feather Icon JS -->
<script src="{{ asset('seller/assets/js/feather.min.js') }}" type="text/javascript"></script>
<!-- Slimscroll JS -->
<script src="{{ asset('seller/assets/plugins/slimscroll/jquery.slimscroll.min.js') }}" type="text/javascript"></script>
<!-- Theme Settings JS -->
<script src="{{ asset('seller/assets/js/theme-settings.js') }}" type="text/javascript"></script>
<script src="{{ asset('seller/assets/js/greedynav.js') }}" type="text/javascript"></script>
<!-- Custom JS -->
<script src="{{ asset('seller/assets/js/script.js') }}" type="text/javascript"></script>
<script type="text/javascript">
   (function () {
      var bell = document.getElementById('notification_popup');
      var list = document.getElementById('sellerNotificationList');
      var badge = document.getElementById('sellerNotificationBadge');
      var menuBadge = document.getElementById('sellerNotificationMenuBadge');
      var markAllButton = document.getElementById('sellerNotificationMarkAll');
      var pageList = document.getElementById('sellerNotificationPageList');

      if (!bell && !pageList) {
         return;
      }

      var dataUrl = bell ? bell.getAttribute('data-notifications-url') : null;
      var markReadUrl = bell ? bell.getAttribute('data-notifications-mark-read') : null;
      var markAllUrl = bell ? bell.getAttribute('data-notifications-mark-all') : null;
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
         if (badge) {
            badge.textContent = currentUnread > 0 ? String(currentUnread) : '';
            badge.style.display = currentUnread > 0 ? 'inline-block' : 'none';
         }
         if (menuBadge) {
            menuBadge.textContent = currentUnread > 0 ? String(currentUnread) : '';
            menuBadge.style.display = currentUnread > 0 ? 'inline-block' : 'none';
         }
      };

      var renderDropdownItems = function (items) {
         if (!list) {
            return;
         }
         if (!items || !items.length) {
            list.innerHTML = '<div class="text-center text-white-50 py-4">No notifications yet.</div>';
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
               '<p class="mb-0 fw-semibold text-dark">' + item.title + '</p>' +
               '<p class="mb-1 text-wrap fs-14">' + item.message + '</p>' +
               '<div class="d-flex justify-content-between align-items-center">' +
               '<span class="fs-12"><i class="isax isax-clock me-1"></i>' + item.time + '</span>' +
               '</div>' +
               '</div>' +
               '</div>' +
               '</a>'
            );
         }).join('');
      };

      var renderPageItems = function (items) {
         if (!pageList) {
            return;
         }
         if (!items || !items.length) {
            pageList.innerHTML = '<div class="text-white-50">No notifications yet.</div>';
            return;
         }

         pageList.innerHTML = items.map(function (item) {
            var meta = typeMeta(item.type);
            var unreadDot = item.is_read ? '' : '<div class="unread-dot"></div>';
            var linkStart = item.url ? '<a href="' + item.url + '" class="text-decoration-none text-white">' : '';
            var linkEnd = item.url ? '</a>' : '';

            return (
               '<div class="notify-card ' + (item.is_read ? 'read' : '') + '" data-notification-id="' + item.id + '">' +
               linkStart +
               '<div class="notify-icon ' + meta.bg + ' ' + meta.color + '">' +
               '<i class="bi ' + meta.icon + '"></i>' +
               '</div>' +
               '<div class="notify-content">' +
               '<div class="d-flex justify-content-between">' +
               '<h6 class="mb-1 fw-bold">' + item.title + '</h6>' +
               '<span class="notify-time">' + item.time + '</span>' +
               '</div>' +
               '<p class="small mb-0">' + item.message + '</p>' +
               '</div>' +
               linkEnd +
               unreadDot +
               '</div>'
            );
         }).join('');
      };

      var fetchNotifications = function (limit, renderTarget) {
         if (!dataUrl || typeof window.jQuery === 'undefined') {
            return;
         }
         return window.jQuery.get(dataUrl, { limit: limit }).done(function (response) {
            if (response && typeof response.unread_count !== 'undefined') {
               updateBadge(parseInt(response.unread_count, 10) || 0);
            }
            if (renderTarget === 'page') {
               renderPageItems(response.items || []);
            } else {
               renderDropdownItems(response.items || []);
            }
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
               if (response && typeof response.unread_count !== 'undefined') {
                  updateBadge(parseInt(response.unread_count, 10) || 0);
               }
            });
         }
      };

      if (list) {
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
            target.classList.add('opacity-75');
         });
      }

      if (pageList) {
         pageList.addEventListener('click', function (event) {
            var target = event.target.closest('[data-notification-id]');
            if (!target) {
               return;
            }
            var id = target.getAttribute('data-notification-id');
            if (id) {
               markRead(id);
               if (currentUnread > 0) {
                  updateBadge(currentUnread - 1);
               }
            }
         });
      }

      if (markAllButton) {
         markAllButton.addEventListener('click', function (event) {
            event.preventDefault();
            markAllRead();
         });
      }

      if (bell) {
         bell.addEventListener('shown.bs.dropdown', function () {
            fetchNotifications(5, 'dropdown');
         });
      }

      if (pageList) {
         fetchNotifications(50, 'page');
      } else {
         fetchNotifications(5, 'dropdown');
      }

      setInterval(function () {
         fetchNotifications(5, 'dropdown');
      }, 15000);
   })();
</script>
</body>
</html>
