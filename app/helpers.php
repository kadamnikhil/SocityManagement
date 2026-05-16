<?php

function ends_with($s, $t)
{
    return \Illuminate\Support\Str::endsWith($s, $t);
}

function str_singular($s)
{
    return \Illuminate\Support\Str::singular($s);
}

function snake_case($s)
{
    return \Illuminate\Support\Str::snake($s);
}

function str_plural($s)
{
    return \Illuminate\Support\Str::plural($s);
}

function toIndianDateTime($datetime)
{
    return \Carbon\Carbon::parse($datetime)->format('d-m-Y h:i A');
}

function getSystemRoles(){
    $permission_seeder = new \Database\Seeders\PermissionSeeder;
    $roles = $permission_seeder->roles;
    $systemRoles = [];
    foreach($roles as $role => $permissions){
        $systemRoles[] = $role;
    }
    return $systemRoles;
}

function getCountingNumber($model, $prefix , $field_name, $year = true){
    $modelClass = "\App\Models\\" . $model;
    // Assuming you have an 'number_field' column in your database table

    $latestNumber = $modelClass::max($field_name);
    if($latestNumber === null) {
        // No records in the database yet, start with 1
        $lastNumberPart = 1;
    } else {
        // Extract the last part of the latest number and increment it
        $parts = explode('-', $latestNumber);
        $lastNumberPart = (int) end($parts);
        $lastNumberPart++; // Increment the last number part
    }
    $currentYear = date('Y');
    $currentMonth = date('n');
    $financialYearStart = ($currentMonth >= 4) ? substr($currentYear, -2) : substr(($currentYear - 1), -2);
    $financialYearEnd = ($currentMonth >= 4) ? substr(($currentYear + 1), -2) : substr($currentYear, -2);
    $number = $prefix . '-' . $financialYearStart . '-' . $financialYearEnd . '-' . str_pad($lastNumberPart, 4, '0', STR_PAD_LEFT);
    if(!$year){
        $number = $prefix . '-' . str_pad($lastNumberPart, 4, '0', STR_PAD_LEFT);
    }
    return $number;
}


function desktop()
{
    $detect = new Mobile_Detect;
    if ($detect->isMobile()) {
        return false;
    } else {
        return true;
    }
}

function mobile()
{
    $detect = new Mobile_Detect;
    if ($detect->isMobile()) {
        return true;
    } else {
        return false;
    }
}

function sendSms($numbers, $message)
{

    $fields = array(
        "variables_values" => $message,
        "route" => "otp",
        "numbers" => $numbers,
    );
    //dd($fields);
    try {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.fast2sms.com/dev/bulkV2",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($fields),
            CURLOPT_HTTPHEADER => array(
                "authorization: EFGpDvhS408wVlIciBTrZ6oayW5jHu1tNsqKYkMXJ9eP2ARUnmiGJeKsOPrfkpUZ0nyugI8wSdlF64BC",
                "accept: */*",
                "cache-control: no-cache",
                "content-type: application/json"
            ),
        ));
        $response = curl_exec($curl);
        // dd( $response );
        $err = curl_error($curl);
        curl_close($curl);
    } catch (\Exception $e) {
        return false;
    }
    return true;
}

function getDiscountedPercentage($originalPrice, $discountedPrice)
{
    if ($originalPrice == 0) {
        return '0%';
    }
    $percentage = (($originalPrice - $discountedPrice) / $originalPrice) * 100;
    $formattedPercentage = round($percentage, 0); // Round to 0 decimal places
    return $formattedPercentage . '%';
}

function inRupee($num, $symbol=true, $pdf=false) {
    $nums = explode('.',$num);
    $num = $nums[0];

    $minus = false;
    if (substr($num, 0, 1) === '-') {
        $minus = true;
        $num = substr($num, strpos($num, "-") + 1);
    }

    $explrestunits = "" ;
    if(strlen($num)>3) {

        $lastthree = substr($num, strlen($num)-3, strlen($num));

        $restunits = substr($num, 0, strlen($num)-3); // extracts the last three digits
        $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
        $expunit = str_split($restunits, 2);
        for($i=0; $i<sizeof($expunit); $i++) {
            // creates each of the 2's group and adds a comma to the end
            if($i==0) {
                $explrestunits .= (int)$expunit[$i].","; // if is first value , convert into integer
            } else {
                $explrestunits .= $expunit[$i].",";
            }
        }
        $thecash = $explrestunits.$lastthree;
    } else {
        $thecash = $num;
    }

    if($minus){
        $thecash = "-".$thecash;
    }

    if(isset($nums[1]) && $nums[1] > 0){
        $thecash = $thecash.".".$nums[1];
    }else{
        $thecash = $thecash;
    }

    if($symbol){
        $thecash = '₹ '.$thecash.'/-';

        return $thecash;


    }elseif($pdf){

        // return "Rs. ".$thecash.'/-';
        return $thecash.'/-';

    }else{

        return html_entity_decode('₹ '.$thecash.'/-');
    }

}
function getDomainUrl(){
    return $url = env("APP_URL", "https://ready.technicul.com");
}
