<?php
/* ===================================================================
VISTA: REGISTRAR USUARIO (COMPLETAMENTE INACTIVA)
Este archivo está pausado para evitar errores de variables no definidas,
ya que aún no manejamos la base de datos ni el controlador de registro.
===================================================================

<div class="max-w-md mx-auto my-16 p-8 bg-white rounded-2xl shadow-md border border-gray-100">
    <h2 class="text-2xl font-bold text-[#0A192F] text-center mb-6">Registrar Usuario</h2>

    <?php if (!empty($error)): ?>
        <p class="text-red-500 text-sm text-center mb-4 bg-red-50 p-2 rounded-lg border border-red-100">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="#" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
            <input type="text" name="nombre" value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" required
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:border-[#0070F3] transition-all">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:border-[#0070F3] transition-all">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
            <input type="password" name="password" required
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:border-[#0070F3] transition-all">
        </div>

        <button type="submit" class="w-full bg-[#0070F3] hover:bg-[#0051CC] text-white py-3 rounded-xl font-bold transition-all shadow-sm mt-2">
            Registrar
        </button>
    </form>
</div>

*/
?>