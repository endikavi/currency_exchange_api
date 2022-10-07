<?php

//check if a function named "apilayerCall" exists
if (!function_exists('apilayerCall')) {
    //if not, create it
    function apilayerCall($url)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_HTTPHEADER => array(
            "Content-Type: text/plain",
            "apikey: " . env('APILAYER_KEY', "fN7NJ0gi8mEO2iPINiRIlWTWq80Xi3Nc") //get the key from the .env file
        ),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET"
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}

//check if a function named "apilayerConvert" exists
if (!function_exists('apilayerConvert')) {
    //if not, create it
    function apilayerConvert($to, $from, $amount = 1)
    {
        
        $response = apilayerCall("https://api.apilayer.com/exchangerates_data/convert?to=$to&from=$from&amount=$amount");

        $data = json_decode($response, true);

        if($data['success']){
            return $data['result'];
        }

    }
}