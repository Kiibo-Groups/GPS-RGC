<?php

namespace App\Services;

use DateTime;

class AVLPacketParserService
{
    private string $packet;
    private int $index = 0;
    private array $request_data = [];

    public function __construct(string $packet)
    {
        $this->packet = $packet;
    }

    public function parse(): array
    {
        $input = json_decode($this->packet);
        $input = str_replace(' ', '', $input);
        $input = strtoupper($input);

        if (($validation = $this->validateInput($input)) !== 1) {
            return ['error' => $validation];
        }

        $length = intval(substr($input, $this->index, 4), 16);
        $this->index += 4;
        $expectedLength = strlen($input) / 2 - 4;

        if ($expectedLength === $length) {
            $this->request_data['length'] = $length;
        } else {
            $this->request_data['error'] = "Longitud incorrecta: Esperada $length, Recibida $expectedLength";
        }

        $crc = substr($input, $length * 2 + 4, 4);
        $calculatedCRC = $this->getCrc16(substr($input, 4, $length * 2), $length);
        $this->request_data['crc'] = $crc;
        $this->request_data['crc_status'] = intval($crc, 16) === $calculatedCRC ? 'CRC check passed' : 'CRC check failed';

        $this->request_data['imei'] = intval(substr($input, $this->index, 16), 16);
        $this->index += 16;

        $commandID = intval(substr($input, $this->index, 2), 16);
        $this->index += 2;
        $this->request_data['command_id'] = $commandID;

        if (in_array($commandID, [1, 68])) {
            $this->parseRecords($commandID, $input);
        } else {
            $this->request_data['error'] = "Parsing no implementado para Command ID $commandID";
        }

        return $this->request_data;
    }

    private function parseRecords(int $cmdID, string $input): void
    {
        $this->index += 2; // flag
        $this->index += 2; // registros

        $timestamp = new DateTime('@' . (intval(substr($input, $this->index, 8), 16)));
        $this->index += 8;
        $this->request_data['timestamp'] = $timestamp->format(DateTime::ATOM);

        $this->index += 2; // extensión

        if ($cmdID === 68) {
            $this->index += 2; // record extension (opcional)
        }

        $this->request_data['priority'] = intval(substr($input, $this->index, 2), 16);
        $this->index += 2;

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

        $this->request_data['event_io'] = intval(substr($input, $this->index, $cmdID === 68 ? 4 : 2), 16);
        $this->index += ($cmdID === 68 ? 4 : 2);

        $this->request_data['status'] = 'OK';
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
}