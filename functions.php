<?php

/**
 * Given a date string, returns the day of the week.
 *
 * @param string $date the date string
 * @return string the day of the week (e.g. 'Do', 'Lu', 'Ma', etc.)
 */
function getDayOfWeek($date) {
    $days = ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'];
    return $days[date('w', strtotime($date))];
}

/**
 * Given a month number, returns its name in Spanish.
 *
 * @param int $monthNumber the month number (1-12)
 * @return string the month name (e.g. 'Enero', 'Febrero', etc.)
 * @throws InvalidArgumentException if the month number is invalid
 */
function getMonthName($monthNumber) {
    $months = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];

    return $months[$monthNumber] ?? 'Mes inválido'; // Devuelve el nombre del mes o un mensaje de error
}


/**
 * Returns today's date in GMT-3 timezone.
 *
 * The date is returned in MySQL format: YYYY-MM-DD.
 *
 * @return string the current date in GMT-3 timezone
 * 
 * Example echo getTodayGMTMinus3();
 */
function getTodayGMTMinus3() {
    $timezone = new DateTimeZone('America/Argentina/Buenos_Aires'); // GMT-3
    $date = new DateTime('now', $timezone);
    return $date->format('Y-m-d'); // MySQL format: YYYY-MM-DD
}

/**
 * Converts a date string from YYYY-MM-DD to DD/MM/YYYY.
 *
 * Returns false if the input date string is invalid.
 *
 * @param string $date the date string in YYYY-MM-DD format
 * @return string|false the converted date string in DD/MM/YYYY format, or false if invalid
 * Ejemplo de uso
 * $originalDate = "2025-03-10";
 * $convertedDate = convertDateToDMY($originalDate);
 * echo $convertedDate; // Salida: 10/03/2025
 */

function convertDateToDMY($date) {
    // Verificar si la fecha está en el formato correcto
    $dateObject = DateTime::createFromFormat('Y-m-d', $date);
    
    // Si la conversión es exitosa, devolver en formato DD/MM/YYYY
    return $dateObject ? $dateObject->format('d/m/Y') : false;
}

?>
