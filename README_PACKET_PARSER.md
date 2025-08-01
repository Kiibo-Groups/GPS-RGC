# Packet Parser Service - Documentación

## Descripción

El `PacketParserService` ha sido mejorado para detectar automáticamente y procesar ambos tipos de paquetes GPS:
- **Paquetes de registro único** (Command ID 1)
- **Paquetes de múltiples registros** (Command ID 68)

## Funcionalidades Principales

### 1. Detección Automática de Tipo de Paquete

El servicio detecta automáticamente el tipo de paquete basándose en el Command ID:
- **Command ID 1**: Registro único
- **Command ID 68**: Múltiples registros (extrae el más reciente)

### 2. Extracción de Coordenadas en Tiempo Real

#### Método `getRealTimeCoordinates()`
```php
$parser = new PacketParserService(json_encode($packetHex));
$coords = $parser->getRealTimeCoordinates();

// Resultado:
[
    'latitude' => -12.3456789,
    'longitude' => -78.9012345,
    'timestamp' => '2025-01-23T06:59:27+00:00',
    'speed' => 45,
    'altitude' => 120.5,
    'imei' => 861773070038757,
    'packet_type' => 'single_record', // o 'multiple_records'
    'total_records' => 1, // o número de registros
    'status' => 'success'
]
```

#### Método `getCoordinatesString()`
```php
$parser = new PacketParserService(json_encode($packetHex));
$coordsString = $parser->getCoordinatesString();

// Resultado: "-12.3456789,-78.9012345" o "error"
```

## Endpoints API

### 1. `/api/getRealTimeCoordinates`
**Método:** POST  
**Body:** `{"packet": "hex_string"}`

**Respuesta exitosa:**
```json
{
    "status": "success",
    "data": {
        "latitude": -12.3456789,
        "longitude": -78.9012345,
        "timestamp": "2025-01-23T06:59:27+00:00",
        "speed": 45,
        "altitude": 120.5,
        "imei": 861773070038757,
        "packet_type": "single_record",
        "total_records": 1,
        "status": "success"
    },
    "message": "Coordenadas obtenidas exitosamente"
}
```

### 2. `/api/getCoordinatesString`
**Método:** POST  
**Body:** `{"packet": "hex_string"}`

**Respuesta exitosa:**
```json
{
    "status": "success",
    "coordinates": "-12.3456789,-78.9012345",
    "message": "Coordenadas obtenidas"
}
```

## Ejemplos de Uso

### Ejemplo 1: Paquete de Registro Único
```php
$packet1 = "1d00030e8047abc5ba1201030404000518c406040000001e01...";

$parser = new PacketParserService(json_encode($packet1));
$coords = $parser->getRealTimeCoordinates();

echo "Latitud: " . $coords['latitude'];
echo "Longitud: " . $coords['longitude'];
echo "Tipo: " . $coords['packet_type']; // "single_record"
```

### Ejemplo 2: Paquete de Múltiples Registros
```php
$packet2 = "03d300030fc72db776e5440108685c4203000000c43eb53b0f3f6a8b176f7a1c0e003b0700081001990100823b008600008700008800000200002000000300000400000500019501019601001b1300ad0100b03a01a201080083000000890000008b0012001600b602300000001700b9001d32d7001e101a020041002212f30096000518c400685c4212000000c43e6ac00f3fa7061767794a10003d0700081001990100823e008600008700008800000200002000000300000400000500019501019601001b1300ad0100b03d01a201080083000000890000008b000f001600b602300000001700bd001d337b001e101c020041002213f60096000518c400685c4221000000c43e21c50f3fe5da175e7c7413004106000810019901008241008600008700008800000200002000000300000400000500019501019601001b1300ad0100b04001a201080083000000890000008b000f001600b602300000001700bf001d32bd001e101c020041002214f80096000518c400685c4230000000c43de6fb0f4032ac1771819c11003a07000810019901008244008600008700008800000200002000000300000400000500019501019601001b1300ad0100b03a01a201080083000000890000008b000f001600b602300000001700c1001d33d2001e101c020041002216020096000518c400685c423e000000c43dbb090f4083aa174d822812004506000810019901008245008600008700008800000200002000000300000400000500019501019601001b1300ad0100b04501a201080083000000890000008b000e001600b602300000001700c1001d338f001e101b020041002217010096000518c400685c424b000000c43d8ce00f40d6cd1709821e13004b0600081001990100824b008600008700008800000200002000000300000400000500019501019601001b1300ad0100b04b01a201080083000000890000008b000d001600b602300000001700be001d339e001e101b020041002218090096000518c400685c4255000000c43d6cc80f411794170888b81100430700091001990100824b008600008700008800000200002000000300000400000500019501019601001b1300ad0100b04401a201080083000000890000008b000a001600b602300000001700c1001d32f2001e101b020041002218d50096000518c400685c4258000000c43d6cb70f412a9716f5024411003f07000910019901008243008601008700008800000200002000000300000400000500019501019601001b1300ad0100b04001a201080083000000890000008b0003001600b602300000001700c1001d3382001e101c0200410022190b0096000518c400951b";

$parser = new PacketParserService(json_encode($packet2));
$coords = $parser->getRealTimeCoordinates();

echo "Latitud: " . $coords['latitude'];
echo "Longitud: " . $coords['longitude'];
echo "Tipo: " . $coords['packet_type']; // "multiple_records"
echo "Total registros: " . $coords['total_records'];
echo "Índice del más reciente: " . $coords['latest_record_index'];
```

### Ejemplo 3: Comparación de Ambos Tipos
```php
$results = PacketParserService::exampleUsage($packet1, $packet2);

echo "Paquete único: " . json_encode($results['single_record']);
echo "Paquete múltiple: " . json_encode($results['multiple_records']);
```

## Características Técnicas

### Detección Automática
- **Command ID 1**: Procesa un solo registro
- **Command ID 68**: Procesa múltiples registros y extrae el más reciente basándose en el timestamp

### Validación
- Verifica que el paquete sea hexadecimal válido
- Valida la longitud del paquete
- Verifica el CRC del paquete
- Maneja errores de parsing

### Información Adicional
Para paquetes de múltiples registros, también se incluye:
- `all_records`: Array con todos los registros procesados
- `latest_record_index`: Índice del registro más reciente
- `total_records`: Número total de registros en el paquete

## Manejo de Errores

El servicio maneja los siguientes errores:
- Paquetes vacíos o inválidos
- Errores de parsing
- CRC inválido
- Longitud incorrecta
- Command ID no soportado

Todos los errores se devuelven en formato JSON con mensajes descriptivos. 