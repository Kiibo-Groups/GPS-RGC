<?php

namespace App;
 
use Illuminate\Support\Facades\Storage;
use Phattarachai\LaravelMobileDetect\Agent;

class Helper
{  
    public static function envUpdate($key, $value, $comma = false)
  	{
        $path = base_path('.env');
        $value = trim($value);
        $env = $comma ? '"'.env($key).'"' : env($key);

        if (file_exists($path)) {
            file_put_contents($path, str_replace(
            $key . '=' . $env,
            $key . '=' . $value,
            file_get_contents($path)
        ));
      }
    }

}