<?php
error_reporting(E_ERROR);
function request($url, $data = null, $headers = null)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    if($data):
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    endif;
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if($headers):
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // curl_setopt($ch, CURLOPT_HEADER, 1);
    endif;

    curl_setopt($ch, CURLOPT_ENCODING, "GZIP");
    return curl_exec($ch);
}
function getstr($str, $exp1, $exp2)
{
    $a = explode($exp1, $str)[1];
    return explode($exp2, $a)[0];
}


echo "Link Ref : ";
$link = trim(fgets(STDIN));

$ref = getstr($link, 'https://shop.freeboxglobal.com/app/cash?code=','&id');
$inopID = getstr($link, '&id=',' ');


$headers = array();
$headers[] = "Accept: application/json, text/plain, */*";
$headers[] = "Content-Type: application/json";
$headers[] = "Lang: id";
$headers[] = "User-Agent: FreeBoxApp/2 CFNetwork/1402.0.8 Darwin/22.2.0";
$headers[] = "Accept-Language: en-GB,en-US;q=0.9,en;q=0.8";
$headers[] = "Accept-Encoding: gzip, deflate";
$getOpID = request($url = 'https://api.freeboxglobal.com/operation/participant/getOperationInitiateInfo', $data = '{"operationInitiateId":"'.$inopID.'"}', $headers);
if(strpos($getOpID, 'operationId":"')!==false)
{
    $opID = getstr($getOpID, 'operationId":"','"');
}
else
{
    echo "Something error in LINK\n";
}


echo "No HP : ";
$nohp = trim(fgets(STDIN));
$url = "https://api.freeboxglobal.com/freeboxMember/app/captcha/getCaptcha";
$headers = array();
$headers[] = "Accept: application/json, text/plain, */*";
$headers[] = "Content-Type: application/json";
$headers[] = "Lang: id";
$headers[] = "User-Agent: FreeBoxApp/2 CFNetwork/1402.0.8 Darwin/22.2.0";
$headers[] = "Accept-Language: en-GB,en-US;q=0.9,en;q=0.8";
$headers[] = "Accept-Encoding: gzip, deflate";
$data = '{"phone":"'.$nohp.'"}';
$getOTP = request($url, $data, $headers);
if(strpos($getOTP, '"message":"success"')!==false)
{

}
else
{
    echo "Error\n";
    exit();
}

otp:
echo "OTP : ";
$otp = trim(fgets(STDIN));
$url = "https://api.freeboxglobal.com/freeboxMember/app/login";
$data = '{"phone":"'.$nohp.'","captcha":"'.$otp.'","type":1,"inviteCode":"'.$ref.'","source":10}';
$login = request($url, $data, $headers);
if(strpos($login, '"message":"success"')!==false)
{
    $token = getstr($login, '"token":"','"');
}
else
{
    echo "Error\n";
    echo "$login\n";
    goto otp;
}


$url = "https://api.freeboxglobal.com/operation/participant/assist";
$headers[] = "Accept: application/json, text/plain, */*";
$headers[] = "Content-Type: application/json";
$headers[] = "Lang: id";
$headers[] = "Token: $token";
$headers[] = "User-Agent: FreeBoxApp/2 CFNetwork/1402.0.8 Darwin/22.2.0";
$headers[] = "Accept-Language: en-GB,en-US;q=0.9,en;q=0.8";
$headers[] = "Accept-Encoding: gzip, deflate";
$data = '{"operationId":"'.$opID.'","operationInitiateId":"'.$inopID.'"}';
$assist = request($url, $data, $headers);
if(strpos($assist, '"message":"success"')!==false)
{
    echo "Success Submit Ref\n";
}
else
{
    echo "Error submit ref\n";
}

