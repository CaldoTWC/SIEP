<?php
/**
 * Vista de Todas las Notificaciones
 * Página completa con todas las notificaciones del usuario
 */

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit;
}

$pageTitle = 'Mis Notificaciones';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - SIEP</title>
    <link rel="stylesheet" href="/SIEP/public/css/notification.css">

</head>
<body>
    <!-- BARRA DE NAVEGACIÓN -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="/SIEP/public/index.php" class="nav-logo">SIEP</a>
            <ul class="nav-menu">
                <li class="nav-item"><a href="#hero" class="nav-link">Inicio</a></li>
                <li class="nav-item"><a href="#user-section" class="nav-link">Usuarios</a></li>
                <li class="nav-item"><a href="/SIEP/public/index.php?action=showLogin" class="nav-link btn-nav">Iniciar
                        Sesión</a></li>
                <li class="nav-item"><a href="/SIEP/public/index.php?action=showRegisterSelection"
                        class="nav-link btn-nav">Registrarse</a></li>
            </ul>
        </div>
    </nav>


    <div class="container">
        <div class="page-header">
            <h1>📬 Mis Notificaciones</h1>
            <p>Aquí puedes ver todas tus notificaciones</p>
        </div>
        <a href="javascript:history.back()" class="logout-btn">← Volver al Dashboard</a><br><br>

        <div class="header">
            <div class="header-actions">
                <button class="btn btn-secondary" onclick="markAllAsRead()">
                    Marcar todas como leídas
                </button>
            </div>
        </div>

        <div class="notifications-container">
            <?php if (empty($notifications)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📭</div>
                    <h3>No tienes notificaciones</h3>
                    <p>Cuando recibas notificaciones, aparecerán aquí.</p>
                </div>
            <?php else: ?>
                <?php foreach ($notifications as $notification): ?>
                    <?php
                    $unreadClass = $notification['is_read'] == 0 ? 'unread' : '';
                    $typeClass = '';
                    $typeLabel = '';
                    
                    // Determinar el tipo para el badge
                    if (strpos($notification['type'], 'aprobada') !== false || 
                        strpos($notification['type'], 'aprobado') !== false) {
                        $typeClass = 'type-aprobada';
                        $typeLabel = '✓ Aprobado';
                    } elseif (strpos($notification['type'], 'rechazada') !== false || 
                              strpos($notification['type'], 'rechazado') !== false) {
                        $typeClass = 'type-rechazada';
                        $typeLabel = '✗ Rechazado';
                    } elseif (strpos($notification['type'], 'disponible') !== false) {
                        $typeClass = 'type-disponible';
                        $typeLabel = '📄 Disponible';
                    } elseif (strpos($notification['type'], 'removida') !== false) {
                        $typeClass = 'type-removida';
                        $typeLabel = '⚠ Removido';
                    }

                    $timeAgo = getTimeAgo($notification['created_at']);
                    ?>
                    <div class="notification-item <?php echo $unreadClass; ?>" 
                         data-id="<?php echo $notification['id']; ?>"
                         data-link="<?php echo htmlspecialchars($notification['link'] ?? '#'); ?>"
                         onclick="handleNotificationClick(this)">
                        
                        <div class="notification-header">
                            <div class="notification-title">
                                <?php echo htmlspecialchars($notification['title']); ?>
                            </div>
                            <div class="notification-time">
                                <?php echo $timeAgo; ?>
                            </div>
                        </div>
                        
                        <div class="notification-message">
                            <?php echo htmlspecialchars($notification['message']); ?>
                        </div>
                        
                        <?php if ($typeLabel): ?>
                            <span class="notification-type <?php echo $typeClass; ?>">
                                <?php echo $typeLabel; ?>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function handleNotificationClick(element) {
            const notificationId = element.dataset.id;
            const link = element.dataset.link;

            // Marcar como leída
            const formData = new FormData();
            formData.append('notification_id', notificationId);

            fetch('index.php?action=markNotificationAsRead', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    element.classList.remove('unread');
                    
                    // Redirigir si hay link
                    if (link && link !== '#') {
                        window.location.href = link;
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function markAllAsRead() {
            fetch('index.php?action=markAllNotificationsAsRead', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remover clase unread de todos los elementos
                    document.querySelectorAll('.notification-item.unread').forEach(item => {
                        item.classList.remove('unread');
                    });
                    alert('Todas las notificaciones han sido marcadas como leídas');
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>

<?php
function getTimeAgo($datetime) {
    $now = new DateTime();
    $past = new DateTime($datetime);
    $diff = $now->diff($past);

    if ($diff->y > 0) return $diff->y . ' año' . ($diff->y > 1 ? 's' : '');
    if ($diff->m > 0) return $diff->m . ' mes' . ($diff->m > 1 ? 'es' : '');
    if ($diff->d > 0) return 'Hace ' . $diff->d . ' día' . ($diff->d > 1 ? 's' : '');
    if ($diff->h > 0) return 'Hace ' . $diff->h . ' hora' . ($diff->h > 1 ? 's' : '');
    if ($diff->i > 0) return 'Hace ' . $diff->i . ' minuto' . ($diff->i > 1 ? 's' : '');
    
    return 'Justo ahora';
}
?>