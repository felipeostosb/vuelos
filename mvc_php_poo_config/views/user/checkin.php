<main class="checkin-page">
    
    <section class="checkin-hero">
        <div class="checkin-hero-container">
            <h1 class="checkin-hero-title">Check-in y gestión de viajes</h1>
            <p class="checkin-hero-subtitle">Consulta, modifica y gestiona fácilmente</p>
        </div>
    </section>

    <div class="checkin-content">
        <div class="checkin-grid">
            
            <div class="checkin-card">
                <div class="checkin-card-image-wrapper">
                    <img src="https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=800&q=80" alt="Avión en vuelo" class="checkin-card-image">
                    <div class="checkin-card-image-overlay">
                        <h3 class="checkin-card-image-title">Web Check-in</h3>
                    </div>
                </div>
                <div class="checkin-card-content">
                    
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert-success">
                            <i class="fa-solid fa-check-circle"></i> ¡Check-in exitoso para <?php echo htmlspecialchars($_GET['pnr'] ?? ''); ?>!
                        </div>
                    <?php elseif (isset($_GET['error'])): ?>
                        <div class="alert-error">
                            <i class="fa-solid fa-circle-xmark"></i> Reserva no encontrada
                        </div>
                    <?php endif; ?>

                    <form action="index.php" method="POST" class="checkin-form">
                        <input type="hidden" name="action" value="procesarCheckin">
                        <div>
                            <p class="checkin-instruction">Ingresa tu código de reserva (PNR) para obtener tu tarjeta de embarque.</p>
                            <input type="text" name="pnr" required placeholder="Ej: XY8P2Q" class="checkin-input">
                        </div>
                        <button type="submit" class="btn btn--primary" style="width: 100%;">
                            <i class="fa-solid fa-qrcode" style="margin-right: 0.5rem;"></i> Obtener Boarding Pass
                        </button>
                    </form>
                </div>
            </div>

            <div class="checkin-card">
                <div class="checkin-card-image-wrapper">
                    <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=800&q=80" alt="Pasajero sonriente" class="checkin-card-image">
                </div>
                <div class="checkin-card-content">
                    <h3 class="process-title">Cambios y cancelaciones</h3>
                    
                    <div class="process-steps">
                        <div class="process-line"></div>
                        
                        <div class="process-step">
                            <div class="process-step-number">1</div>
                            <span class="process-step-text">Selecciona</span>
                        </div>
                        <div class="process-step">
                            <div class="process-step-number">2</div>
                            <span class="process-step-text">Elige fecha</span>
                        </div>
                        <div class="process-step">
                            <div class="process-step-number">3</div>
                            <span class="process-step-text">Confirma</span>
                        </div>
                    </div>

                    <div class="alert-warning">
                        ⚠️ Políticas varían por aerolínea
                    </div>
                </div>
            </div>

            <div class="checkin-card">
                <div class="checkin-card-image-wrapper">
                    <img src="https://images.unsplash.com/photo-1569154941061-e231b4732ef1?auto=format&fit=crop&w=800&q=80" alt="Equipaje" class="checkin-card-image">
                </div>
                <div class="checkin-card-content" style="padding-left: 1.5rem; padding-right: 1.5rem;">
                    <h3 class="process-title" style="margin-bottom: 1.5rem;">Políticas de equipaje</h3>
                    
                    <div class="baggage-table-container">
                        <table class="baggage-table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Económica</th>
                                    <th>Business</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="table-label">Cabina</td>
                                    <td>1 x 10kg</td>
                                    <td>2 x 10kg</td>
                                </tr>
                                <tr>
                                    <td class="table-label">Bodega</td>
                                    <td>1 x 23kg</td>
                                    <td>2 x 32kg</td>
                                </tr>
                                <tr>
                                    <td class="table-label">Extra</td>
                                    <td>S/. 80</td>
                                    <td class="text-emerald-400 font-light">✅ Gratis</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>
