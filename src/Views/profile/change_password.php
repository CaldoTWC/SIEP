<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña - SIEP</title>
    <link rel="stylesheet" href="/SIEP/public/css/bootstrap.min.css">
    <link rel="stylesheet" href="/SIEP/public/css/style.css">
    <style>
        .password-requirements {
            font-size: 0.875rem;
            color: #6c757d;
            margin-top: 0.5rem;
        }
        .password-requirements ul {
            margin-bottom: 0;
            padding-left: 1.5rem;
        }
        .strength-meter {
            height: 5px;
            background-color: #e9ecef;
            border-radius: 3px;
            margin-top: 0.5rem;
            overflow: hidden;
        }
        .strength-meter-fill {
            height: 100%;
            transition: width 0.3s, background-color 0.3s;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">🔐 Cambiar Contraseña</h4>
                    </div>
                    <div class="card-body">
                        
                        <!-- Información del usuario -->
                        <div class="alert alert-info">
                            <strong>Usuario:</strong> <?php echo htmlspecialchars($user_profile['first_name'] . ' ' . $user_profile['last_name_p']); ?><br>
                            <strong>Email:</strong> <?php echo htmlspecialchars($user_profile['email']); ?><br>
                            <strong>Rol:</strong> <span class="badge badge-secondary"><?php echo htmlspecialchars($user_profile['role']); ?></span>
                        </div>
                        
                        <!-- Mensajes de error -->
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <strong>⚠️ Error:</strong>
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Formulario -->
                        <form method="POST" action="/SIEP/public/index.php?action=changePassword" id="changePasswordForm">
                            
                            <!-- Contraseña actual -->
                            <div class="form-group">
                                <label for="current_password">
                                    <strong>Contraseña Actual:</strong> <span class="text-danger">*</span>
                                </label>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="current_password" 
                                    name="current_password"
                                    required
                                    placeholder="Ingrese su contraseña actual">
                            </div>
                            
                            <hr>
                            
                            <!-- Nueva contraseña -->
                            <div class="form-group">
                                <label for="new_password">
                                    <strong>Nueva Contraseña:</strong> <span class="text-danger">*</span>
                                </label>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="new_password" 
                                    name="new_password"
                                    required
                                    minlength="8"
                                    placeholder="Ingrese su nueva contraseña">
                                
                                <!-- Medidor de fortaleza -->
                                <div class="strength-meter">
                                    <div class="strength-meter-fill" id="strengthMeter"></div>
                                </div>
                                <small id="strengthText" class="form-text text-muted"></small>
                                
                                <!-- Requisitos -->
                                <div class="password-requirements">
                                    <strong>Requisitos de contraseña:</strong>
                                    <ul>
                                        <li>Mínimo 8 caracteres</li>
                                        <li>Se recomienda usar mayúsculas, minúsculas, números y símbolos</li>
                                        <li>Debe ser diferente a la contraseña actual</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <!-- Confirmar contraseña -->
                            <div class="form-group">
                                <label for="confirm_password">
                                    <strong>Confirmar Nueva Contraseña:</strong> <span class="text-danger">*</span>
                                </label>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="confirm_password" 
                                    name="confirm_password"
                                    required
                                    placeholder="Confirme su nueva contraseña">
                                <small id="matchText" class="form-text"></small>
                            </div>
                            
                            <!-- Botones -->
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary btn-block" id="submitBtn">
                                    Cambiar Contraseña
                                </button>
                                <a href="/SIEP/public/index.php?action=<?php 
                                    echo match($user_profile['role']) {
                                        'student' => 'studentDashboard',
                                        'company' => 'companyDashboard',
                                        'upis', 'admin' => 'upisDashboard',
                                        default => 'home'
                                    };
                                ?>" class="btn btn-secondary btn-block">
                                    Cancelar
                                </a>
                            </div>
                        </form>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="/SIEP/public/js/jquery.min.js"></script>
    <script src="/SIEP/public/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validación en tiempo real
        const newPassword = document.getElementById('new_password');
        const confirmPassword = document.getElementById('confirm_password');
        const strengthMeter = document.getElementById('strengthMeter');
        const strengthText = document.getElementById('strengthText');
        const matchText = document.getElementById('matchText');
        const submitBtn = document.getElementById('submitBtn');
        
        // Medir fortaleza de contraseña
        newPassword.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (password.length >= 12) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;
            
            const percentage = (strength / 5) * 100;
            strengthMeter.style.width = percentage + '%';
            
            if (strength <= 2) {
                strengthMeter.style.backgroundColor = '#dc3545';
                strengthText.textContent = 'Débil';
                strengthText.className = 'form-text text-danger';
            } else if (strength <= 3) {
                strengthMeter.style.backgroundColor = '#ffc107';
                strengthText.textContent = 'Media';
                strengthText.className = 'form-text text-warning';
            } else {
                strengthMeter.style.backgroundColor = '#28a745';
                strengthText.textContent = 'Fuerte';
                strengthText.className = 'form-text text-success';
            }
        });
        
        // Verificar coincidencia de contraseñas
        confirmPassword.addEventListener('input', function() {
            if (this.value === '') {
                matchText.textContent = '';
                return;
            }
            
            if (this.value === newPassword.value) {
                matchText.textContent = '✓ Las contraseñas coinciden';
                matchText.className = 'form-text text-success';
            } else {
                matchText.textContent = '✗ Las contraseñas no coinciden';
                matchText.className = 'form-text text-danger';
            }
        });
        
        // Validar antes de enviar
        document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
            if (newPassword.value !== confirmPassword.value) {
                e.preventDefault();
                alert('Las contraseñas no coinciden. Por favor, verifique.');
                return false;
            }
            
            if (newPassword.value.length < 8) {
                e.preventDefault();
                alert('La contraseña debe tener al menos 8 caracteres.');
                return false;
            }
        });
    </script>
</body>
</html>