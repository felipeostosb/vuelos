<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Usuario</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 400px; margin: 60px auto; }
        input { display: block; width: 100%; padding: 8px; margin: 6px 0 14px; box-sizing: border-box; }
        button { padding: 10px 20px; background: #3498db; color: #fff; border: none; cursor: pointer; }
        button:hover { background: #2980b9; }
        .error { color: #e74c3c; margin-bottom: 12px; }
    </style>
</head>
<body>
    <h2>Registrar Usuario</h2>

    <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="index.php?action=insertar">
        <label>Nombre</label>
        <input type="text" name="nombre" value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>

        <label>Contraseña</label>
        <input type="password" name="password" required>

        <button type="submit">Registrar</button>
    </form>
</body>
</html>
