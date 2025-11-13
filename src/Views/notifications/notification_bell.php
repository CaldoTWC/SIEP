<?php
/**
 * Componente de Notificaciones (Campanita)
 * 
 * INSTRUCCIONES DE USO:
 * 1. Incluir este archivo en el header/navbar donde quieras mostrar la campanita
 * 2. Asegurarse de tener jQuery cargado
 * 3. Incluir: <?php include 'views/notifications/notification_bell.php'; ?>
 * 
 * REQUISITOS:
 * - Sesión iniciada con $_SESSION['user_id']
 * - jQuery
 * - Font Awesome (para el ícono de campana) o usar emoji 🔔
 */

// Solo mostrar para usuarios logueados (no UPIS)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'upis') {
    return;
}
?>

<style>
/* Estilos del componente de notificaciones */
.notification-bell-container {
    position: relative;
    display: inline-block;
    margin: 0 15px;
}

.notification-bell-icon {
    position: relative;
    cursor: pointer;
    font-size: 24px;
    color: #333;
    transition: color 0.3s;
}

.notification-bell-icon:hover {
    color: #6f1d1b;
}

.notification-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background-color: #dc3545;
    color: white;
    border-radius: 50%;
    padding: 2px 6px;
    font-size: 11px;
    font-weight: bold;
    min-width: 18px;
    text-align: center;
    display: none;
}

.notification-badge.show {
    display: block;
}

.notification-dropdown {
    position: absolute;
    top: 45px;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    width: 380px;
    max-height: 500px;
    overflow-y: auto;
    z-index: 1000;
    display: none;
}

.notification-dropdown.show {
    display: block;
}

.notification-header {
    padding: 15px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #f8f9fa;
    border-radius: 8px 8px 0 0;
}

.notification-header h4 {
    margin: 0;
    font-size: 16px;
    color: #333;
}

.mark-all-read {
    color: #6f1d1b;
    font-size: 13px;
    cursor: pointer;
    text-decoration: none;
}

.mark-all-read:hover {
    text-decoration: underline;
}

.notification-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.notification-item {
    padding: 15px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    transition: background-color 0.2s;
    position: relative;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-item.unread {
    background-color: #e3f2fd;
}

.notification-item.unread::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background-color: #6f1d1b;
}

.notification-title {
    font-weight: bold;
    font-size: 14px;
    color: #333;
    margin-bottom: 5px;
}

.notification-message {
    font-size: 13px;
    color: #666;
    margin-bottom: 5px;
    line-height: 1.4;
}

.notification-time {
    font-size: 11px;
    color: #999;
}

.notification-empty {
    padding: 40px 20px;
    text-align: center;
    color: #999;
}

.notification-footer {
    padding: 12px;
    text-align: center;
    border-top: 1px solid #eee;
    background-color: #f8f9fa;
    border-radius: 0 0 8px 8px;
}

.notification-footer a {
    color: #6f1d1b;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
}

.notification-footer a:hover {
    text-decoration: underline;
}

/* Loading spinner */
.notification-loading {
    padding: 20px;
    text-align: center;
    color: #999;
}

/* Responsive */
@media (max-width: 480px) {
    .notification-dropdown {
        width: 90vw;
        right: -20px;
    }
}
</style>

<!-- HTML del componente -->
<div class="notification-bell-container">
    <!-- Ícono de campana -->
    <div class="notification-bell-icon" id="notificationBellIcon">
        <!-- Puedes usar Font Awesome o emoji -->
        🔔
        <!-- O con Font Awesome: <i class="fas fa-bell"></i> -->
        <span class="notification-badge" id="notificationBadge">0</span>
    </div>

    <!-- Dropdown de notificaciones -->
    <div class="notification-dropdown" id="notificationDropdown">
        <div class="notification-header">
            <h4>Notificaciones</h4>
            <a href="#" class="mark-all-read" id="markAllRead">Marcar todas como leídas</a>
        </div>
        
        <div id="notificationContent">
            <div class="notification-loading">
                Cargando notificaciones...
            </div>
        </div>

        <div class="notification-footer">
            <a href="index.php?action=showAllNotifications">Ver todas las notificaciones</a>
        </div>
    </div>
</div>

<script>
// JavaScript del componente de notificaciones
(function() {
    const bellIcon = document.getElementById('notificationBellIcon');
    const badge = document.getElementById('notificationBadge');
    const dropdown = document.getElementById('notificationDropdown');
    const notificationContent = document.getElementById('notificationContent');
    const markAllReadBtn = document.getElementById('markAllRead');

    // Toggle dropdown
    bellIcon.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdown.classList.toggle('show');
        
        if (dropdown.classList.contains('show')) {
            loadNotifications();
        }
    });

    // Cerrar dropdown al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!dropdown.contains(e.target) && !bellIcon.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    });

    // Actualizar contador cada 30 segundos
    updateUnreadCount();
    setInterval(updateUnreadCount, 30000);

    // Actualizar contador de no leídas
    function updateUnreadCount() {
        fetch('index.php?action=getUnreadCount', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.count > 0) {
                badge.textContent = data.count > 99 ? '99+' : data.count;
                badge.classList.add('show');
            } else {
                badge.classList.remove('show');
            }
        })
        .catch(error => console.error('Error al obtener contador:', error));
    }

    // Cargar notificaciones
    function loadNotifications() {
        notificationContent.innerHTML = '<div class="notification-loading">Cargando notificaciones...</div>';

        fetch('index.php?action=getNotificationsDropdown', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                notificationContent.innerHTML = '<div class="notification-empty">Error al cargar notificaciones</div>';
                return;
            }

            if (data.notifications.length === 0) {
                notificationContent.innerHTML = '<div class="notification-empty">No tienes notificaciones</div>';
                return;
            }

            renderNotifications(data.notifications);
        })
        .catch(error => {
            console.error('Error:', error);
            notificationContent.innerHTML = '<div class="notification-empty">Error al cargar notificaciones</div>';
        });
    }

    // Renderizar notificaciones
    function renderNotifications(notifications) {
        let html = '<ul class="notification-list">';

        notifications.forEach(notification => {
            const unreadClass = notification.is_read == 0 ? 'unread' : '';
            const timeAgo = getTimeAgo(notification.created_at);

            html += `
                <li class="notification-item ${unreadClass}" data-id="${notification.id}" data-link="${notification.link || '#'}">
                    <div class="notification-title">${escapeHtml(notification.title)}</div>
                    <div class="notification-message">${escapeHtml(notification.message)}</div>
                    <div class="notification-time">${timeAgo}</div>
                </li>
            `;
        });

        html += '</ul>';
        notificationContent.innerHTML = html;

        // Agregar eventos de clic a cada notificación
        document.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', function() {
                const notificationId = this.dataset.id;
                const link = this.dataset.link;

                markAsRead(notificationId, function() {
                    if (link && link !== '#') {
                        window.location.href = link;
                    }
                });
            });
        });
    }

    // Marcar como leída
    function markAsRead(notificationId, callback) {
        const formData = new FormData();
        formData.append('notification_id', notificationId);

        fetch('index.php?action=markNotificationAsRead', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateUnreadCount();
                if (callback) callback();
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Marcar todas como leídas
    markAllReadBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        fetch('index.php?action=markAllNotificationsAsRead', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateUnreadCount();
                loadNotifications();
            }
        })
        .catch(error => console.error('Error:', error));
    });

    // Calcular tiempo relativo
    function getTimeAgo(datetime) {
        const now = new Date();
        const past = new Date(datetime);
        const diffMs = now - past;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);

        if (diffMins < 1) return 'Justo ahora';
        if (diffMins < 60) return `Hace ${diffMins} min`;
        if (diffHours < 24) return `Hace ${diffHours}h`;
        if (diffDays < 7) return `Hace ${diffDays}d`;
        
        return past.toLocaleDateString('es-MX', { day: 'numeric', month: 'short' });
    }

    // Escapar HTML para seguridad
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
})();
</script>