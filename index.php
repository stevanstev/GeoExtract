<?php
    // @Author Stevanstev
    $covertToDecimal = function($latitude, $longitude) {
        //Formula = degree + (min/60) + (sec/3600)
        $decLatitude = round($latitude[0] + ($latitude[1]/60) + ($latitude[2]/3600), 6);
        $decLongitude = round($longitude[0] + ($longitude[1]/60) + ($longitude[2]/3600), 6);
        $decResult = array(
            "latitude" => $decLatitude,
            "longitude" => $decLongitude
        );

        return $decResult;
    };

    $replaceSpecialChar = function($str) {
        $cleanStr = preg_replace("/\'/", "", $str);
        $cleanStr = preg_replace("/\"/", "", $cleanStr);
        $cleanStr = preg_replace("/(deg)/", "", $cleanStr);

        return $cleanStr;
    };

    $removeWhiteSpace = function($str) {
        $str = explode(" ", $str);
        $result = [];

        foreach ($str as $key => $value) {
            if ($value != "") {
                array_push($result, $value);
            }
        }

        return $result;
    };

    # Get File name
    try {
        $browser = $argv[1];
        $fileName = $argv[2];
    } catch(Exception $e) {
        echo "There was an error $e";
    }

    # Save Latitude and Longitude with image name to databases
    # GET FROM MAP API
    // $GPSLatitude = "-6.261623";
    // $GPSLongitude = "106.621955";

    #Embed the latitude and longitude to media
    // $embedScript = "exiftool -GPSLatitude=$GPSLatitude -GPSLongitude=$GPSLongitude $fileName";
    // shell_exec($embedScript);

    #Extract existing Latitude and Longitude
    $extractScript = "exiftool $fileName | grep GPS";
    $result = shell_exec($extractScript);
    $explodeResult = explode(PHP_EOL, $result);
    $position = [];

    foreach ($explodeResult as $key => $value) {
        if (strpos($value, "Latitude") || strpos($value, "Longitude")) {
            $extractNumber = explode(":", $value);
            array_push($position, $extractNumber[1]);
        }
    }

    $latitude = $removeWhiteSpace($replaceSpecialChar($position[0]));

    $longitude = $removeWhiteSpace($replaceSpecialChar($position[1]));

    $gps = $covertToDecimal($latitude, $longitude);
    $status = shell_exec("$browser https://www.google.com/maps/?q=-".$gps['latitude'].",".$gps['longitude']);

    return true;
