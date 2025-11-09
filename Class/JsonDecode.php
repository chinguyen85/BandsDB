<?php
declare(strict_types=1);

// ---------------------------------------------------------
// TODO 1: READ & DECODE THE JSON FILE
// ---------------------------------------------------------
/**
 * GOAL:
 * - Read `bands_full.json` from the project root.
 * - Decode it to an associative array.
 * - Validate the shape and handle errors gracefully.
 *
 * HINTS:
 * - Verify the file exists before reading.
 * - Use a readable error message for each failure mode:
 *   - file not found
 *   - read failure
 *   - invalid JSON
 *   - missing "bands" key or wrong type
 *
 * ACCEPTANCE:
 * - You end up with a variable like $data where $data['bands'] is an array of band entries.
 *
 * WRITE YOUR CODE BELOW:
 */

final class JsonDecode {
    public static function readAndValidate(string $path): array
    {
        if (!is_file($path)) {
            http_response_code(500);
            exit("Error: file not found");
        }

        $json = file_get_contents($path);
        if ($json === false) {
            http_response_code(500);
            exit("Error: file read failure");
        }

        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($data)) {
            http_response_code(500);
            exit("Error: invalid JSON");
        }

        if (!isset($data['bands'])) {
            http_response_code(500);
            exit("Error: missing 'bands' key");
        }

        if (!is_array($data['bands'])) {
            http_response_code(500);
            exit("Error: 'bands' is not an array");
        }

        return $data;
    }
}
