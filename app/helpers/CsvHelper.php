<?php
namespace DmServer;

class CsvHelper
{
    public static function readCsv($fileName) {
        $headers = [];
        $outputData = [];
        $row = 0;
        if (($handle = fopen($fileName, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
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