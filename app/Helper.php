<?php

namespace App;

use Illuminate\Support\Facades\Storage;
use Phattarachai\LaravelMobileDetect\Agent;

class Helper
{
  public static function envUpdate($key, $value, $comma = false)
  {
    $path = base_path('.env');
    $value = trim($value);
    $env = $comma ? '"' . env($key) . '"' : env($key);

    if (file_exists($path)) {
      file_put_contents($path, str_replace(
        $key . '=' . $env,
        $key . '=' . $value,
        file_get_contents($path)
      ));
    }
  }


  /**
   * Obtener la última posición GPS de los registros.
   */
  public static function getLastPosition(array $parsedPacket): ?array
  {
    if (!isset($parsedPacket['records']) || empty($parsedPacket['records'])) {
      return null;
    }

    $lastRecord = end($parsedPacket['records']);

    return [
      'lat'       => $lastRecord['latitude'],
      'lng'       => $lastRecord['longitude'],
      'speed'     => $lastRecord['speed'],
      'altitude'  => $lastRecord['altitude'],
      'timestamp' => $lastRecord['timestamp'],
    ];
  }

  /**
   * Obtener historial de coordenadas GPS desde los registros.
   */
  public static function getCoordinatesPath(array $parsedPacket): array
  {
    $coordinates = [];

    foreach ($parsedPacket['records'] as $record) {
      $coordinates[] = [
        'lat'       => $record['latitude'],
        'lng'       => $record['longitude'],
        'timestamp' => $record['timestamp'],
        'speed'     => $record['speed'],
      ];
    }

    return $coordinates;
  }
}
