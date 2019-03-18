<?php

namespace App\helpers;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

    class Util 
    {
        public static function convertDateToCarbon($date_str) 
        {
            $dateData = explode('/', $date_str);
            $month = $dateData[0];
            $year = $dateData[1];
            return Carbon::createFromDate($year, $month, 1);
        }
    }