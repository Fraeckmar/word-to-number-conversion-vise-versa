<?php

namespace App\Http\Controllers;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class ConvertionController extends Controller
{
    public function convert(Request $request)
    {
        $result = $this->getConversion($request->input);
        $result['usd'] = '';
        if ($result['value']) {
            $paramValue = is_numeric($request->input) ? $request->input : (is_numeric($result['value']) ? $result['value'] : 0);
            $client = new Client();
            $headers = ['apikey' => env('API_LAYER_KEY')];
            $uriRequest = "https://api.apilayer.com/exchangerates_data/convert?to=USD&from=PHP&amount={$paramValue}";
            $res = $client->request('GET', $uriRequest, [
                'headers' => $headers
            ]);
            $data = json_decode($res->getBody()->getContents());
            $result['usd'] = !empty($data) ? round($data->result, 2) : '';
        }       
        
        return view('welcome', $result);
    }

    function getConversion($input)
    {
        $result = [
            'value' => '',
            'error' => ''
        ];
        if (empty($input)) {
            return $result;
        }
        try {
            $output = [];
            $cleanInput = preg_replace('/[^A-Za-z0-9 ]/', '', $input);
            if (is_numeric(str_replace(' ', '', $cleanInput))) {
                $cleanNumber = str_replace(' ', '', $cleanInput);
                $numberParts = explode(',', number_format($cleanNumber));
                $revNumberParts = array_reverse($numberParts);
                $nthPowerNumbers = [];
                foreach ($revNumberParts as $idx => $numbers) {
                    $sub = $idx != 0 && strlen($numbers) == 3 ? 'hundred' : '';
                    $nthPowerNumbers[$numbers] = [
                        'power' => $this->getNthPower($idx),
                        'sub' => $sub
                    ];
                }

                foreach (array_reverse($nthPowerNumbers, true) as $numbers => $nth) {
                    $output[] = $this->getPowerNumbersToWords($numbers, $nth['power'], $nth['sub']);
                }
                $result['value'] = implode(' ', $output);
            } else {
                $result['value'] = $this->getWordToNumberConversion($input);
            }
        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }
        return $result;
    }

    function getNthPower($nth)
    {
        $nthPowers = [
            0 => 'hundred',
            1 => 'thousand',
            2 => 'million',
            3 => 'billioin'
        ];
        if (array_key_exists($nth, $nthPowers)) {
            return $nthPowers[$nth];
        }
        return '';
    }

    function getPowerNumbersToWords($numbers, $power, $sub)
    {
        if (empty($numbers)) {
            return '';
        }
        $numberWords = [];
        extract($this->getNumToWordValues()); // $ones, $tens
        $revNumbersArr = str_split(strrev($numbers), 2);

        foreach ($revNumbersArr as $idx => $number) {
            $number = strrev($number);
            if (count($revNumbersArr) > 1) {
                if ($idx == 1) {  // 1st digit in $numbers
                    if (!empty($sub)) {
                        array_unshift($numberWords, $sub);
                    }     
                    array_unshift($numberWords, $ones[$number]);                    
                } else {
                    if (empty($sub)) {
                        $numberWords[] = $power;
                    }
                    $numberWords[] = $this->getTenthNumberWords($number);
                    if (!empty($sub)) {
                        $numberWords[] = $power;
                    }
                    
                }                
            } else {
                $numberWords[] = $this->getTenthNumberWords($number);
                $numberWords[] = $power;
            }
        }
        return implode(' ', $numberWords);
    }

    function getTenthNumberWords($number)
    {
        extract($this->getNumToWordValues()); // $ones, $tens
        if ($number > 19) {
            $splitNumber = str_split($number);
            $concatNumber = $tens[$splitNumber[0]];
            if (array_key_exists($splitNumber[1], $ones) && $ones[$splitNumber[1]]) {
                $concatNumber .= '-'.$ones[$splitNumber[1]];
            }
            return $concatNumber;
        } else {
            return $ones[intval($number)];
        }
    }

    function getNumToWordValues()
    {
        return [
            'ones' => [
                1 => 'one', 
                2 => 'two', 
                3 => 'three', 
                4 => 'four', 
                5 => 'five', 
                6 => 'six', 
                7 => 'seven', 
                8 => 'eight', 
                9 => 'nine', 
                10 => 'ten', 
                11 => 'eleven', 
                12 => 'twelve', 
                13 => 'thirteen', 
                14 => 'fourteen', 
                15 => 'fifteen', 
                16 => 'sixteen', 
                17 => 'seventeen', 
                18 => 'eighteen', 
                19 => 'nineteen'
            ],
            'tens' => [
                1 => 'ten',
                2 => 'twenty', 
                3 => 'thirty', 
                4 => 'fourty', 
                5 => 'fifty', 
                6 => 'sixty', 
                7 => 'seventy', 
                8 => 'eighty', 
                9 => 'ninety'
            ]
        ];
    }

    function getWordToNumberConversion($input)
    {
        $numValues = $this->getNumToWordValues();
        $ones = array_filter($numValues['ones'], function($key){
            return $key < 10;
        }, ARRAY_FILTER_USE_KEY);
        $ones = array_flip($ones);
        $specials = array_filter($numValues['ones'], function($key){
            return $key > 9;
        }, ARRAY_FILTER_USE_KEY);
        $specials = array_flip($specials);
        $tens = [
            'twenty' => 20, 
            'thirty' => 30,
            'fourty' => 40,
            'fifty' => 50,
            'sixty' => 60,
            'seventy' => 70,
            'eighty' => 80,
            'ninety' => 90
        ];
        $hundred = [
            'hundred' => 100
        ];
        $power = [
            'thousand' => 1000,
            'million' => 1000000
        ];
        
        $inputArr = array_filter(explode(' ', str_replace('hundred', '', $input)));
        if (count($inputArr) < 3) {
            $inputArr = array_filter(explode(' ', $input));
        }

        $equation = '';
        foreach ($inputArr as $word) {
            if (array_key_exists($word, $specials)) {
                $equation .= $specials[$word];
            }
            if (array_key_exists($word, $ones)) {
                $equation .= $ones[$word];
            }
            if (array_key_exists($word, $tens)) {
                $equation .= $tens[$word];
            }
            if (array_key_exists($word, $hundred)) {
                $equation .= '*'.$hundred[$word].'+';
            }
            if (strpos($word, '-') !== false) {
                $numbersArr = explode('-', $word);
                $wordValue = 0;
                foreach ($numbersArr as $_word) {
                    if (array_key_exists($_word, $ones)) {
                        $wordValue += $ones[$_word];
                    }
                    if (array_key_exists($_word, $tens)) {
                        $wordValue += $tens[$_word];
                    }
                }
                if ($wordValue) {
                    $equation .= $wordValue;
                }
            }
            if (array_key_exists($word, $power)) {
                $equation .= '*'.$power[$word].'+';
            }
        }
        $equationArr = array_filter(explode('+', $equation));
        $equation = implode(')+(', $equationArr);
        $numberValue = eval("return ({$equation});");
        return $numberValue;
    }
}
