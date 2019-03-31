<?php
namespace App\Helper;

class CsvHelper
{
    public static $csvRoot=__DIR__.'/../DataFixtures/';

    public static function readCsv($fileName): array
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
                            case 'issuenumbers':
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
