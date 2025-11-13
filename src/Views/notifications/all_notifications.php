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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #6f1d1b 0%, #8b2a27 100%);
            color: white;
            padding: 30px;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .header-actions {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-secondary {
            background-color: rgba(255,255,255,0.2);
            color: white;
        }

        .btn-secondary:hover {
            background-color: rgba(255,255,255,0.3);
        }

        .notifications-container {
            padding: 0;
        }

        .notification-item {
            padding: 25px 30px;
            border-bottom: 1px solid #eee;
            transition: background-color 0.2s;
            cursor: pointer;
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
            width: 5px;
            background-color: #6f1d1b;
        }

        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 10px;
        }

        .notification-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }

        .notification-time {
            font-size: 13px;
            color: #999;
            white-space: nowrap;
        }

        .notification-message {
            font-size: 14px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        .notification-type {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .type-aprobada {
            background-color: #d4edda;
            color: #155724;
        }

        .type-rechazada {
            background-color: #f8d7da;
            color: #721c24;
        }

        .type-disponible {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .type-removida {
            background-color: #fff3cd;
            color: #856404;
        }

        .empty-state {
            padding: 60px 30px;
            text-align: center;
            color: #999;
        }

        .empty-state-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }

        .back-link {
            display: inline-block;
            margin: 20px 30px;
            color: #6f1d1b;
            text-decoration: none;
            font-weight: 500;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .header {
                padding: 20px;
            }

            .header h1 {
                font-size: 22px;
            }

            .notification-item {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📬 Mis Notificaciones</h1>
            <p>Aquí puedes ver todas tus notificaciones</p>
            <div class="header-actions">
                <button class="btn btn-secondary" onclick="markAllAsRead()">
                    Marcar todas como leídas
                </button>
            </div>
        </div>

        <a href="javascript:history.back()" class="back-link">← Volver</a>

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