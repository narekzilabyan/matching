<?php

namespace App\Service;

use PhpOffice\PhpSpreadsheet\Reader\Csv;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MatchingService
{
    /**
     * Function for get matching list
     *
     * @param UploadedFile $csvFile
     * @return array
     */
    public function getMatchingList(UploadedFile $csvFile): array
    {
        $parsedData = $this->parseDataFromCsv($csvFile);
        return $this->calculateMatching($parsedData);
    }

    /**
     * Function for parse data from uploaded csv file
     *
     * @param UploadedFile $csvFile
     * @return array
     */
    private function parseDataFromCsv(UploadedFile $csvFile): array
    {
        $reader = new Csv();
        $reader->setDelimiter(';');
        $reader->setEnclosure('');
        $reader->setSheetIndex(0);

        $spreadsheet = $reader->load($csvFile);

        return $spreadsheet->getActiveSheet()->toArray();
    }

    /**
     * Function for calculate data based on parsed data from csv file
     *
     * @param $parsedData
     * @return array
     */
    private function calculateMatching($parsedData): array
    {
        $dataForCheckMatches = $this->dataForCheckMatches($parsedData);

        $countParsedData = count($parsedData);
        $result = [];
        //array for use in sorting employees list by matching
        $matchingArray = [];

        //Loop on all employees list
        for ($i = 0; $i < $countParsedData; $i++) {
            $result[$i]['MatchingController'] = 0;

            //Loop on one employee characteristics for matching percents calculation
            foreach ($parsedData[$i] as $key => $value) {
                if ($key === 'Name' || $key === 'Email'){
                    $result[$i][$key] = $value;
                } elseif ($key === 'Age') {
                    for ($j = $value - 5 ; $j <= $value + 5; $j++) {
                        if (isset($dataForCheckMatches[$key][$j])){
                            //Check in order not to protect the employee's own age
                            if ($j == $value) {
                                $result[$i]['MatchingController'] += $this->getPercentByCharacteristic($key, $dataForCheckMatches[$key][$j] - 1);
                                continue;
                            }

                            $result[$i]['MatchingController'] += $this->getPercentByCharacteristic($key, $dataForCheckMatches[$key][$j]);
                        }
                    }
                } else {
                    $result[$i]['MatchingController'] += $this->getPercentByCharacteristic($key, $dataForCheckMatches[$key][$value]);
                }
            }
            //calculate average for employee
            $result[$i]['MatchingController'] = ceil($result[$i]['MatchingController']/($countParsedData-1));
            $matchingArray[] = $result[$i]['MatchingController'];
        }

        array_multisort($matchingArray, SORT_DESC, $result);

        return $result;
    }

    /**
     * Function for change parsed data to associative massive and calculate count of matches by one condition
     *
     * @param $parsedData
     * @return array
     */
    private function dataForCheckMatches(&$parsedData): array
    {
        $dataForCheckMatches = [];
        $header = array_shift($parsedData);

        foreach ($parsedData as &$data) {
            $data = array_combine($header, $data);
            foreach ($data as $key => $value) {
                if ($key == 'Name' || $key == 'Email') {
                    continue;
                }
                if (!isset($dataForCheckMatches[$key][$value])){
                    $dataForCheckMatches[$key][$value] = 1;
                } else {
                    $dataForCheckMatches[$key][$value]++;
                }
            }
        }
        unset($data);
        return $dataForCheckMatches;
    }

    /**
     * Function for calculating the percentage of coincidence for one characteristic
     *
     * @param string $characteristic
     * @param int $matchingCount
     * @return int
     */
    private function getPercentByCharacteristic(string $characteristic, int $matchingCount): int
    {
        switch ($characteristic) {
            case 'Division':
                return 30 * ($matchingCount -1);
            case 'Age':
                return 30 * $matchingCount;
            case 'Timezone':
                return 40 * ($matchingCount -1);
            default:
                return 0;
        }
    }
}
