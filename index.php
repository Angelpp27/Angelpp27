<?php
// Obtener IP del visitante
function obtenerIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    else return $_SERVER['REMOTE_ADDR'];
}

// Detectar sistema operativo (aproximado)
function detectarSO($userAgent) {
    if (preg_match('/linux/i', $userAgent)) return 'Linux';
    elseif (preg_match('/macintosh|mac os x/i', $userAgent)) return 'Mac OS';
    elseif (preg_match('/windows|win32/i', $userAgent)) return 'Windows';
    elseif (preg_match('/android/i', $userAgent)) return 'Android';
    elseif (preg_match('/iphone|ipad/i', $userAgent)) return 'iOS';
    else return 'Desconocido';
}

// Obtener datos del visitante
$ip = obtenerIP();
$userAgent = $_SERVER['HTTP_USER_AGENT'];
$sistemaOperativo = detectarSO($userAgent);

// Obtener geolocalización desde API externa (ip-api.com)
$geoData = @json_decode(file_get_contents("http://ip-api.com/json/$ip?fields=status,country,regionName,city,zip,lat,lon,isp,org,as,query"), true);

// Preparar información
$datos = [
    'Fecha' => date('Y-m-d H:i:s'),
    'IP' => $ip,
    'País' => $geoData['country'] ?? 'Desconocido',
    'Región' => $geoData['regionName'] ?? 'Desconocido',
    'Ciudad' => $geoData['city'] ?? 'Desconocido',
    'Código Postal' => $geoData['zip'] ?? 'Desconocido',
    'Latitud' => $geoData['lat'] ?? 'Desconocido',
    'Longitud' => $geoData['lon'] ?? 'Desconocido',
    'ISP' => $geoData['isp'] ?? 'Desconocido',
    'Organización' => $geoData['org'] ?? 'Desconocido',
    'AS' => $geoData['as'] ?? 'Desconocido',
    'Sistema Operativo' => $sistemaOperativo,
    'Navegador' => $userAgent
];

// Guardar en archivo
$registro = implode(' | ', $datos) . "\n";
file_put_contents('ips.txt', $registro, FILE_APPEND);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Información de tu IP</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f0f0f0; }
        h1 { color: #333; }
        table { background: #fff; border-collapse: collapse; width: 100%; max-width: 600px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        th, td { padding: 10px; border: 1px solid #ddd; }
        th { background: #eee; text-align: left; }
    </style>
</head>
<body>
    <h1>Datos de tu conexión</h1>
    <table>
        <?php foreach ($datos as $clave => $valor): ?>
            <tr>
                <th><?php echo htmlspecialchars($clave); ?></th>
                <td><?php echo htmlspecialchars($valor); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
