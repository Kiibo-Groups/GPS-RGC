<?php

namespace App\Services;

use App\Helper;
use Illuminate\Support\Facades\Log;
use DateTime;


class PacketParserService
{
    private string $packet;
    private int $index = 0;
    private array $request_data = [];

    public function __construct(string $packet)
    {
        $this->packet = $packet;
    }

    public function parse()
    {
        $data = $this->packet;
        $offset = 0;
        $length = unpack("n", substr($data, $offset, 2))[1];
        $offset += 2;

        $imei = bin2hex(substr($data, $offset, 8));
        $offset += 8;

        $commandId = ord($data[$offset]);
        $offset += 1;

        $recordsLeft = ord($data[$offset]);
        $offset += 1;

        $recordCount = ord($data[$offset]);
        $offset += 1;

        $records = [];

        for ($i = 0; $i < $recordCount; $i++) {
            $record = [];

            $timestamp = unpack('N', substr($data, $offset, 4))[1];
            $offset += 4;

            $timestampExtension = ord($data[$offset]); // timestamp extension byte
            $offset += 1;

            $record['timestamp'] = $timestamp;
            $record['timestamp_extension'] = $timestampExtension;

            $record['priority'] = ord($data[$offset]);
            $offset += 1;

            $record['longitude'] = unpack('l', substr($data, $offset, 4))[1] / 10000000;
            $offset += 4;

            $record['latitude'] = unpack('l', substr($data, $offset, 4))[1] / 10000000;
            $offset += 4;

            $record['altitude'] = unpack('n', substr($data, $offset, 2))[1];
            $offset += 2;

            $record['angle'] = unpack('n', substr($data, $offset, 2))[1];
            $offset += 2;

            $record['satellites'] = ord($data[$offset]);
            $offset += 1;

            $record['speed'] = unpack('n', substr($data, $offset, 2))[1] / 10;
            $offset += 2;

            $record['hdop'] = ord($data[$offset]);
            $offset += 1;

            $record['event_id'] = ord($data[$offset]);
            $offset += 1;

            // IO Elements
            $record['io_elements'] = [];

            foreach ([1, 2, 4, 8] as $ioSize) {
                $ioCount = ord($data[$offset]);
                $offset += 1;

                for ($j = 0; $j < $ioCount; $j++) {
                    $id = ord($data[$offset]);
                    $offset += 1;
                    $value = substr($data, $offset, $ioSize);
                    $offset += $ioSize;

                    $record['io_elements'][$id] = bin2hex($value); // opcional: puedes hacer unpack si conoces tipos
                }
            }

            $records[] = $record;
        }

        $crc = unpack('n', substr($data, $offset, 2))[1];

        return [
            'imei' => $imei,
            'command_id' => $commandId,
            'records_left' => $recordsLeft,
            'record_count' => $recordCount,
            'records' => $records,
            'crc' => $crc,
        ];
    }

    /**
     * Obtiene las coordenadas GPS en tiempo real
     * @return array Array con latitud, longitud y timestamp
     */
    public function getRealTimeCoordinates(): array
    {
        $parsedData = $this->parse();
        
        if (isset($parsedData['error'])) {
            return ['error' => $parsedData['error']];
        }

        return [
            'latitude' => $parsedData['latitude'] ?? null,
            'longitude' => $parsedData['longitude'] ?? null,
            'timestamp' => $parsedData['timestamp'] ?? null,
            'speed' => $parsedData['speed'] ?? null,
            'altitude' => $parsedData['altitude'] ?? null,
            'imei' => $parsedData['imei'] ?? null,
            'packet_type' => $parsedData['packet_type'] ?? null,
            'total_records' => $parsedData['total_records'] ?? 1,
            'status' => 'success'
        ];
    }

    /**
     * Obtiene solo latitud y longitud como string para uso rápido
     * @return string Formato: "lat,lon" o "error"
     */
    public function getCoordinatesString(): string
    {
        $coords = $this->getRealTimeCoordinates();
        
        if (isset($coords['error'])) {
            return 'error';
        }

        if ($coords['latitude'] !== null && $coords['longitude'] !== null) {
            return $coords['latitude'] . ',' . $coords['longitude'];
        }

        return 'error';
    }

    /**
     * Ejemplo de uso para ambos tipos de paquetes
     * @param string $packet1 Paquete de registro único
     * @param string $packet2 Paquete de múltiples registros
     * @return array Resultados de ambos paquetes
     */
    public static function exampleUsage(string $packet1, string $packet2): array
    {
        $results = [];
        
        // Procesar paquete de registro único
        try {
            $parser1 = new self(json_encode($packet1));
            $coords1 = $parser1->getRealTimeCoordinates();
            $results['single_record'] = $coords1;
        } catch (\Exception $e) {
            $results['single_record'] = ['error' => $e->getMessage()];
        }
        
        // Procesar paquete de múltiples registros
        try {
            $parser2 = new self(json_encode($packet2));
            $coords2 = $parser2->getRealTimeCoordinates();
            $results['multiple_records'] = $coords2;
        } catch (\Exception $e) {
            $results['multiple_records'] = ['error' => $e->getMessage()];
        }
        
        return $results;
    }

    private function parseRecords(int $cmdID, string $input): void
    {
        $this->index += 2; // flag
        $this->index += 2; // registros

        // Si es Command ID 68 (múltiples registros), procesar el último registro (más reciente)
        if ($cmdID === 68) {
            $this->parseMultipleRecords($input);
        } else {
            // Command ID 1 (registro único)
            $this->parseSingleRecord($input);
        }
    }

    private function parseSingleRecord(string $input): void
    {
        $timestamp = new DateTime('@' . (intval(substr($input, $this->index, 8), 16)));
        $this->index += 8;
        $this->request_data['timestamp'] = $timestamp->format(DateTime::ATOM);

        $this->index += 2; // extensión

        $this->request_data['priority'] = intval(substr($input, $this->index, 2), 16);
        $this->index += 2;

        // Extraer latitud y longitud en tiempo real
        $this->request_data['longitude'] = $this->parseSignedInt(substr($input, $this->index, 8)) / 10000000;
        $this->index += 8;

        $this->request_data['latitude'] = $this->parseSignedInt(substr($input, $this->index, 8)) / 10000000;
        $this->index += 8;

        $this->request_data['altitude'] = $this->parseSignedInt(substr($input, $this->index, 4)) / 10;
        $this->index += 4;

        $this->request_data['angle'] = intval(substr($input, $this->index, 4), 16) / 100;
        $this->index += 4;

        $this->request_data['satellites'] = intval(substr($input, $this->index, 2), 16);
        $this->index += 2;

        $this->request_data['speed'] = intval(substr($input, $this->index, 4), 16);
        $this->index += 4;

        $this->request_data['hdop'] = intval(substr($input, $this->index, 2), 16) / 10;
        $this->index += 2;

        $this->request_data['event_io'] = intval(substr($input, $this->index, 2), 16);
        $this->index += 2;

        $this->request_data['status'] = 'OK';
        $this->request_data['packet_type'] = 'single_record';
    }

    private function parseMultipleRecords(string $input): void
    {
        // Contar cuántos registros hay
        $numRecords = intval(substr($input, $this->index - 2, 2), 16);
        $this->request_data['total_records'] = $numRecords;
        
        $records = [];
        $latestRecord = null;
        $latestTimestamp = 0;

        // Procesar todos los registros para encontrar el más reciente
        for ($i = 0; $i < $numRecords; $i++) {
            $recordStartIndex = $this->index;
            
            $timestamp = intval(substr($input, $this->index, 8), 16);
            $this->index += 8;
            
            $this->index += 2; // extensión
            $this->index += 2; // record extension (opcional)
            
            $priority = intval(substr($input, $this->index, 2), 16);
            $this->index += 2;

            $longitude = $this->parseSignedInt(substr($input, $this->index, 8)) / 10000000;
            $this->index += 8;

            $latitude = $this->parseSignedInt(substr($input, $this->index, 8)) / 10000000;
            $this->index += 8;

            $altitude = $this->parseSignedInt(substr($input, $this->index, 4)) / 10;
            $this->index += 4;

            $angle = intval(substr($input, $this->index, 4), 16) / 100;
            $this->index += 4;

            $satellites = intval(substr($input, $this->index, 2), 16);
            $this->index += 2;

            $speed = intval(substr($input, $this->index, 4), 16);
            $this->index += 4;

            $hdop = intval(substr($input, $this->index, 2), 16) / 10;
            $this->index += 2;

            $eventIo = intval(substr($input, $this->index, 4), 16);
            $this->index += 4;

            $record = [
                'timestamp' => $timestamp,
                'datetime' => gmdate("Y-m-d H:i:s", $timestamp),
                'priority' => $priority,
                'longitude' => $longitude,
                'latitude' => $latitude,
                'altitude' => $altitude,
                'angle' => $angle,
                'satellites' => $satellites,
                'speed' => $speed,
                'hdop' => $hdop,
                'event_io' => $eventIo,
                'record_index' => $i
            ];

            $records[] = $record;

            // Mantener el registro más reciente
            if ($timestamp > $latestTimestamp) {
                $latestTimestamp = $timestamp;
                $latestRecord = $record;
            }
        }

        // Usar el registro más reciente como datos principales
        if ($latestRecord) {
            $this->request_data['timestamp'] = gmdate(DateTime::ATOM, $latestRecord['timestamp']);
            $this->request_data['priority'] = $latestRecord['priority'];
            $this->request_data['longitude'] = $latestRecord['longitude'];
            $this->request_data['latitude'] = $latestRecord['latitude'];
            $this->request_data['altitude'] = $latestRecord['altitude'];
            $this->request_data['angle'] = $latestRecord['angle'];
            $this->request_data['satellites'] = $latestRecord['satellites'];
            $this->request_data['speed'] = $latestRecord['speed'];
            $this->request_data['hdop'] = $latestRecord['hdop'];
            $this->request_data['event_io'] = $latestRecord['event_io'];
            $this->request_data['all_records'] = $records;
            $this->request_data['latest_record_index'] = $latestRecord['record_index'];
        }

        $this->request_data['status'] = 'OK';
        $this->request_data['packet_type'] = 'multiple_records';
    }

    private function validateInput(string $inputString): int|string
    {
        if (strlen($inputString) === 0) return "Cadena vacía.";
        if (strlen($inputString) > 2048) return "Cadena demasiado larga.";
        if (!preg_match('/^[0-9A-F]+$/', $inputString)) return "Caracteres no hexadecimales.";
        if (strlen($inputString) % 2 !== 0) return "Longitud impar.";
        if (strlen($inputString) < 26) return "Cadena demasiado corta.";
        return 1;
    }

    private function parseSignedInt(string $hex): int
    {
        $val = intval($hex, 16);
        $max = 1 << (strlen($hex) * 4);
        return $val < ($max / 2) ? $val : $val - $max;
    }

    private function getCrc16(string $data, int $length): int
    {
        $buffer = [];
        for ($i = 0; $i < $length; $i++) {
            $buffer[$i] = intval(substr($data, $i * 2, 2), 16);
        }

        $poly = 0x8408;
        $crc = 0;
        foreach ($buffer as $b) {
            $crc ^= $b;
            for ($i = 0; $i < 8; $i++) {
                $crc = ($crc >> 1) ^ (($crc & 1) ? $poly : 0);
            }
        }

        return $crc;
    }


    function hexToBinaryString(string $hex): string
    {
        return hex2bin($hex);
    }

    function parseRuptelaRecordHeader($hex)
    {
        $binary = hex2bin($hex);
        $offset = 0;

        $timestamp = unpack('N', substr($binary, $offset, 4))[1];
        $offset += 4;

        $timestampExtension = unpack('C', substr($binary, $offset, 1))[1];
        $offset += 1;

        $priority = unpack('C', substr($binary, $offset, 1))[1];
        $offset += 1;

        $longitudeRaw = unpack('l', substr($binary, $offset, 4))[1];
        $longitude = $longitudeRaw / 10000000;
        $offset += 4;

        $latitudeRaw = unpack('l', substr($binary, $offset, 4))[1];
        $latitude = $latitudeRaw / 10000000;
        $offset += 4;

        $altitudeRaw = unpack('n', substr($binary, $offset, 2))[1];
        $altitude = $altitudeRaw / 10;
        $offset += 2;

        $angleRaw = unpack('n', substr($binary, $offset, 2))[1];
        $angle = $angleRaw / 100;
        $offset += 2;

        $satellites = unpack('C', substr($binary, $offset, 1))[1];
        $offset += 1;

        $speed = unpack('n', substr($binary, $offset, 2))[1];
        $offset += 2;

        $hdop = unpack('C', substr($binary, $offset, 1))[1];
        $offset += 1;

        $eventId = unpack('C', substr($binary, $offset, 1))[1];
        $offset += 1;

        return [
            'timestamp' => $timestamp,
            'datetime' => gmdate("Y-m-d H:i:s", $timestamp),
            'timestamp_extension' => $timestampExtension,
            'priority' => $priority,
            'longitude' => $longitude,
            'latitude' => $latitude,
            'altitude_m' => $altitude,
            'angle_deg' => $angle,
            'satellites' => $satellites,
            'speed_kph' => $speed,
            'hdop' => $hdop,
            'event_id' => $eventId,
        ];
    }
}
