<?php
namespace test;
include_once __DIR__.'/../vendor/autoload.php';

$client = new \SilangPHP\Rpc\Client();
while(1)
{
    $number = $client->get("Snowflake",'getId',[]);
    if(!isset($tmp[$number]))
    {
        $tmp[$number] = $number;
        echo $number.PHP_EOL;
    }else{
        echo "error:".$number.PHP_EOL;
    }

}
