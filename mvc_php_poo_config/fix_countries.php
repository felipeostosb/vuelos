<?php
require 'config/database.php';
$db = new Database();
$conn = $db->getConnection();

$map = [
    'US' => 'Estados Unidos',
    'USA' => 'Estados Unidos',
    'DE' => 'Alemania',
    'FR' => 'Francia',
    'GB' => 'Reino Unido',
    'ES' => 'España',
    'IT' => 'Italia',
    'JP' => 'Japón',
    'CN' => 'China',
    'BR' => 'Brasil',
    'CA' => 'Canadá',
    'AU' => 'Australia',
    'IN' => 'India',
    'MX' => 'México',
    'AR' => 'Argentina',
    'PE' => 'Perú',
    'CO' => 'Colombia',
    'CL' => 'Chile',
    'KR' => 'Corea del Sur',
    'ID' => 'Indonesia',
    'TH' => 'Tailandia',
    'VN' => 'Vietnam',
    'MY' => 'Malasia',
    'PH' => 'Filipinas',
    'SG' => 'Singapur',
    'TR' => 'Turquía',
    'SA' => 'Arabia Saudita',
    'AE' => 'Emiratos Árabes',
    'ZA' => 'Sudáfrica',
    'EG' => 'Egipto',
    'MA' => 'Marruecos',
    'NG' => 'Nigeria',
    'KE' => 'Kenia',
    'RU' => 'Rusia',
    'NZ' => 'Nueva Zelanda',
    'CU' => 'Cuba',
    'PA' => 'Panamá',
    'DO' => 'República Dominicana',
    'PR' => 'Puerto Rico',
    'CR' => 'Costa Rica'
];

$count = 0;
foreach ($map as $code => $name) {
    $stmt = $conn->prepare("UPDATE aeropuertos SET pais = :name WHERE pais = :code");
    $stmt->execute(['name' => $name, 'code' => $code]);
    $count += $stmt->rowCount();
}
echo "Updated $count rows.\n";
