<main class="py-12 bg-gray-50 min-h-[80vh] flex items-center justify-center">
    <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto bg-white rounded-3xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="bg-green-500 p-8 text-center text-white">
                <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm">
                    <i class="fa-solid fa-check text-4xl"></i>
                </div>
                <h1 class="text-3xl font-bold mb-2">¡Reserva Confirmada!</h1>
                <p class="text-green-100">Tu vuelo ha sido reservado exitosamente y el pago procesado.</p>
            </div>

            <!-- Content -->
            <div class="p-8">
                <div class="text-center mb-8">
                    <p class="text-gray-500 text-sm font-semibold uppercase tracking-wider mb-2">Código de Reserva (PNR)</p>
                    <div class="inline-block bg-gray-100 border-2 border-dashed border-gray-300 px-8 py-3 rounded-xl">
                        <span class="text-4xl font-mono font-bold text-[#0A192F] tracking-widest"><?php echo htmlspecialchars($_GET['pnr'] ?? 'XXXXXX'); ?></span>
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-100 rounded-xl p-5 mb-8 flex gap-4 items-start">
                    <i class="fa-solid fa-circle-info text-blue-500 mt-1"></i>
                    <div>
                        <h4 class="font-bold text-blue-900 mb-1">Guarda este código</h4>
                        <p class="text-blue-800 text-sm">Necesitarás tu código PNR para realizar el Check-in online 24 horas antes de tu vuelo.</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="?action=panel" class="btn btn--primary bg-[#0070F3] hover:bg-[#0051CC] text-white font-bold py-3 px-8 rounded-xl transition-colors text-center shadow-lg">
                            Ver Mis Viajes
                        </a>
                    <?php endif; ?>
                    <a href="?action=home" class="btn border border-gray-300 hover:bg-gray-50 text-gray-700 font-bold py-3 px-8 rounded-xl transition-colors text-center">
                        Volver al Inicio
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>
