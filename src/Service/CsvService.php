<?php
namespace App\Service;

class CsvService
{
    public static string $csvRoot = __DIR__ . '/../DataFixtures/';

    public function readCsv($fileName): array
    {
        $headers = [];
        $outputData = [];
        $row = 0;
        if (($handle = fopen(self::$csvRoot.$fileName, 'rb')) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
                if ($row === 0) {
                    $headers = $data;
                }
                else {
                    $outputData[$row-1] = [];
                    foreach (array_keys($data) as $c) {
                        switch($headers[$c]) {
                            case 'issueNumbers':
                                $outputData[$row - 1][$headers[$c]] = explode(',', $data[$c]);
                                break;
                            default:
                                $outputData[$row - 1][$headers[$c]] = $data[$c];
                        }
                    }
                }
                $row++;
            }
            fclose($handle);
        }

        return $outputData;

    }
}
