<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-person-plus-fill text-primary" style="font-size: 3rem;"></i>
                        <h3 class="fw-bold mt-2">Crear Cuenta</h3>
                        <p class="text-muted">Únete a Novairlines y vuela mejor</p>
                    </div>

                    <?php if (isset($_GET['registro']) && $_GET['registro'] == 'error'): ?>
                        <div class="alert alert-danger text-center rounded-3">
                            <i class="bi bi-exclamation-triangle-fill"></i> El correo ya está registrado o hubo un error.
                        </div>
                    <?php endif; ?>

                    <form action="index.php" method="POST">
                        <input type="hidden" name="action" value="procesarRegistro">
                        
                        <div class="mb-3">
                            <label class="form-label text-secondary fw-semibold">Nombre Completo</label>
                            <input type="text" name="nombre" class="form-control form-control-lg rounded-3" placeholder="Ej. Carlos Martínez" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-secondary fw-semibold">Correo Electrónico</label>
                            <input type="email" name="email" class="form-control form-control-lg rounded-3" placeholder="tucorreo@ejemplo.com" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-secondary fw-semibold">Contraseña</label>
                            <input type="password" name="password" class="form-control form-control-lg rounded-3" placeholder="Crea una contraseña segura" required minlength="6">
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-3 fw-bold mb-3">
                            Crear Cuenta
                        </button>

                        <div class="text-center text-muted">
                            ¿Ya tienes una cuenta? <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" class="text-decoration-none fw-bold">Inicia Sesión</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
