<main class="bg-gray-50 pb-20">
    
    <section class="bg-[#0080FF] text-white py-16">
        <div class="max-w-[1560px] mx-auto px-12 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Destinos populares</h1>
            <p class="text-lg text-blue-100">Descubre los favoritos de nuestros viajeros</p>
        </div>
    </section>

    <section class="max-w-[1560px] mx-auto px-12 mt-12">
        <?php
            require_once 'config/database.php';
            $db = new Database();
            $conn = $db->getConnection();
            $aeropuertos = [];
            
            if ($conn) {
                $stmt = $conn->query("SELECT * FROM aeropuertos ORDER BY pais, ciudad");
                $aeropuertos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
            function getContinente($codigo) {
                $codigo = strtoupper(trim($codigo));
                $norte = ['US','CA','MX','BM','GL','PR'];
                $latino = ['AR','BO','BR','CL','CO','EC','PE','PY','UY','VE','CR','CU','DO','SV','GT','HN','NI','PA','BS','JM','HT','BZ'];
                $europa = ['ES','FR','IT','DE','GB','UK','PT','NL','BE','CH','AT','SE','NO','DK','FI','IE','GR','PL','RU','UA','CZ','RO','HU','BG','HR','RS','SK','SI','EE','LV','LT'];
                $asia = ['CN','JP','IN','KR','ID','TH','VN','MY','PH','SG','TR','SA','AE','IL','IR','IQ','PK','BD','QA','KW','OM','BH','JO','LB','SY','YE','AF','KZ','UZ'];
                $africa = ['ZA','EG','NG','KE','MA','DZ','ET','GH','TZ','UG','SN','CI','CM','AO','MZ','ZW','ZM'];
                $oceania = ['AU','NZ','FJ','PG','PF','NC'];
                
                if (in_array($codigo, $latino)) return 'América Latina y el Caribe';
                if (in_array($codigo, $norte)) return 'América del Norte';
                if (in_array($codigo, $europa)) return 'Europa';
                if (in_array($codigo, $asia)) return 'Asia y Medio Oriente';
                if (in_array($codigo, $africa)) return 'África';
                if (in_array($codigo, $oceania)) return 'Oceanía';
                
                return 'Resto del Mundo';
            }
            
            // Agrupar por continente y luego por país
            $agrupados = [];
            foreach ($aeropuertos as $aeropuerto) {
                $pais = $aeropuerto['pais'];
                $continente = getContinente($pais);
                
                if (!isset($agrupados[$continente])) {
                    $agrupados[$continente] = [];
                }
                if (!isset($agrupados[$continente][$pais])) {
                    $agrupados[$continente][$pais] = [];
                }
                $agrupados[$continente][$pais][] = $aeropuerto;
            }
            
            // Ordenamos los continentes alfabéticamente
            ksort($agrupados);
        ?>
        
        <?php
            // Mapeo básico de códigos ISO a nombres de países en español para los más comunes
            $nombres_paises = [
                'US' => 'Estados Unidos', 'CA' => 'Canadá', 'MX' => 'México', 'BM' => 'Bermudas', 'GL' => 'Groenlandia', 'PR' => 'Puerto Rico',
                'AR' => 'Argentina', 'BO' => 'Bolivia', 'BR' => 'Brasil', 'CL' => 'Chile', 'CO' => 'Colombia', 'EC' => 'Ecuador', 'PE' => 'Perú', 'PY' => 'Paraguay', 'UY' => 'Uruguay', 'VE' => 'Venezuela', 'CR' => 'Costa Rica', 'CU' => 'Cuba', 'DO' => 'República Dominicana', 'SV' => 'El Salvador', 'GT' => 'Guatemala', 'HN' => 'Honduras', 'NI' => 'Nicaragua', 'PA' => 'Panamá', 'BS' => 'Bahamas', 'JM' => 'Jamaica', 'HT' => 'Haití', 'BZ' => 'Belice',
                'ES' => 'España', 'FR' => 'Francia', 'IT' => 'Italia', 'DE' => 'Alemania', 'GB' => 'Reino Unido', 'UK' => 'Reino Unido', 'PT' => 'Portugal', 'NL' => 'Países Bajos', 'BE' => 'Bélgica', 'CH' => 'Suiza', 'AT' => 'Austria', 'SE' => 'Suecia', 'NO' => 'Noruega', 'DK' => 'Dinamarca', 'FI' => 'Finlandia', 'IE' => 'Irlanda', 'GR' => 'Grecia', 'PL' => 'Polonia', 'RU' => 'Rusia', 'UA' => 'Ucrania', 'CZ' => 'República Checa', 'RO' => 'Rumanía', 'HU' => 'Hungría', 'BG' => 'Bulgaria', 'HR' => 'Croacia', 'RS' => 'Serbia', 'SK' => 'Eslovaquia', 'SI' => 'Eslovenia', 'EE' => 'Estonia', 'LV' => 'Letonia', 'LT' => 'Lituania',
                'CN' => 'China', 'JP' => 'Japón', 'IN' => 'India', 'KR' => 'Corea del Sur', 'ID' => 'Indonesia', 'TH' => 'Tailandia', 'VN' => 'Vietnam', 'MY' => 'Malasia', 'PH' => 'Filipinas', 'SG' => 'Singapur', 'TR' => 'Turquía', 'SA' => 'Arabia Saudita', 'AE' => 'Emiratos Árabes', 'IL' => 'Israel', 'IR' => 'Irán', 'IQ' => 'Irak', 'PK' => 'Pakistán', 'BD' => 'Bangladés', 'QA' => 'Catar', 'KW' => 'Kuwait', 'OM' => 'Omán', 'BH' => 'Baréin', 'JO' => 'Jordania', 'LB' => 'Líbano', 'SY' => 'Siria', 'YE' => 'Yemen', 'AF' => 'Afganistán', 'KZ' => 'Kazajistán', 'UZ' => 'Uzbekistán',
                'ZA' => 'Sudáfrica', 'EG' => 'Egipto', 'NG' => 'Nigeria', 'KE' => 'Kenia', 'MA' => 'Marruecos', 'DZ' => 'Argelia', 'ET' => 'Etiopía', 'GH' => 'Ghana', 'TZ' => 'Tanzania', 'UG' => 'Uganda', 'SN' => 'Senegal', 'CI' => 'Costa de Marfil', 'CM' => 'Camerún', 'AO' => 'Angola', 'MZ' => 'Mozambique', 'ZW' => 'Zimbabue', 'ZM' => 'Zambia',
                'AU' => 'Australia', 'NZ' => 'Nueva Zelanda', 'FJ' => 'Fiyi', 'PG' => 'Papúa Nueva Guinea', 'PF' => 'Polinesia Francesa', 'NC' => 'Nueva Caledonia'
            ];

            $continente_ids = [
                'América Latina y el Caribe' => 'latam',
                'América del Norte' => 'na',
                'Europa' => 'eu',
                'Asia y Medio Oriente' => 'asia',
                'África' => 'africa',
                'Oceanía' => 'oceania',
                'Resto del Mundo' => 'resto'
            ];
        ?>
        
        <div class="space-y-6">
        <?php foreach ($agrupados as $continente => $paises): 
            $cont_id = $continente_ids[$continente] ?? 'cont_' . md5($continente);
        ?>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <!-- Botón Desplegable del Continente -->
                <button onclick="document.getElementById('<?php echo $cont_id; ?>').classList.toggle('hidden'); this.querySelector('.flecha').classList.toggle('rotate-180');" 
                        class="w-full px-8 py-6 text-left flex justify-between items-center hover:bg-blue-50 transition-colors focus:outline-none cursor-pointer">
                    <h2 class="text-2xl font-bold text-[#0A2540] flex items-center gap-4">
                        <i class="fa-solid fa-earth-americas text-[#0080FF] text-3xl"></i> 
                        <?php echo htmlspecialchars($continente); ?>
                        <span class="text-sm font-normal text-gray-500 bg-gray-100 px-3 py-1 rounded-full ml-4">
                            <?php echo count($paises); ?> países
                        </span>
                    </h2>
                    <i class="fa-solid fa-chevron-down text-gray-400 text-xl transition-transform duration-300 flecha"></i>
                </button>

                <!-- Contenedor de Países (Oculto por defecto) -->
                <div id="<?php echo $cont_id; ?>" class="hidden px-8 pb-8 border-t border-gray-100 bg-gray-50/50">
                    <?php 
                        // Ordenar países alfabéticamente por código o nombre real
                        ksort($paises);
                        foreach ($paises as $codigo_pais => $aeropuertos_pais): 
                            $nombre_pais = $nombres_paises[$codigo_pais] ?? $codigo_pais;
                    ?>
                        <!-- Encabezado de País -->
                        <div class="mb-6 mt-8 pl-4 border-l-4 border-blue-400">
                            <h3 class="text-xl font-bold text-gray-800 flex items-center gap-3">
                                <i class="fa-solid fa-flag text-blue-500 text-lg"></i> 
                                <?php echo htmlspecialchars($nombre_pais); ?> 
                                <?php if($nombre_pais !== $codigo_pais): ?>
                                    <span class="text-xs text-gray-400 font-normal">(<?php echo htmlspecialchars($codigo_pais); ?>)</span>
                                <?php endif; ?>
                            </h3>
                        </div>

                        <!-- Grid de Tarjetas (Aeropuertos) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8 pl-4">
                            <?php foreach ($aeropuertos_pais as $aeropuerto): ?>
                            <div class="bg-white rounded-2xl shadow-sm overflow-hidden tarjeta-animada group border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all duration-300">
                                <div class="h-32 overflow-hidden bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center">
                                    <i class="fa-solid fa-plane-arrival text-4xl text-blue-300 group-hover:scale-110 group-hover:text-blue-500 transition-all duration-500"></i>
                                </div>
                                <div class="p-5 text-center">
                                    <h3 class="text-lg font-bold text-[#0A2540] mb-1 line-clamp-1" title="<?php echo htmlspecialchars($aeropuerto['ciudad']); ?>">
                                        <?php echo htmlspecialchars($aeropuerto['ciudad']); ?>
                                    </h3>
                                    <p class="text-gray-500 mb-4 text-xs font-medium bg-gray-100 inline-block px-3 py-1.5 rounded-full border border-gray-200">
                                        IATA: <span class="text-[#0080FF] font-bold"><?php echo htmlspecialchars($aeropuerto['codigo_iata']); ?></span>
                                    </p>
                                    <a href="?action=buscar&destino=<?php echo urlencode($aeropuerto['ciudad']); ?>" class="w-full inline-block bg-[#0A2540] hover:bg-[#0080FF] text-white py-2.5 rounded-xl font-medium transition-colors shadow-sm text-sm">
                                        <i class="fa-solid fa-magnifying-glass mr-2"></i>Buscar vuelos
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </section>

</main>
