<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/db.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Client;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('sms_queue', false, true, false, false);
echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) {
    //echo ' [x] Received ', $msg->body, "\n";
    echo ' [x] Received ', "\n";
    $tokenfile = fopen("/home/lawre/code/talkzuri/token.txt", "r");
    $token = fgets($tokenfile);
    echo 'token: ' . $token;
    fclose($tokenfile);
    $msg_array = json_decode($msg->body, true);
    $sdp_smses = $msg_array[0];
    $raw_sms = $msg_array[1];
    $time_start = microtime(true);
    $db_instance = DatabaseConnection::getInstance();
    $conn = $db_instance->getConnection();
    foreach ($raw_sms as $sms) {
        $sql = "INSERT INTO sms (cost,price,message_id,message_part,`to`,detail,status,
        status_code,description,channel,package_id,project_id,`message`,`from`,direction,created_at,
        sent_at,campaign_id,tz_id,bulk_id,telco,type,uri)     
        VALUES ( '" . $sms["messagePart"] . "', '" . $sms["credits"] . "', '" . $sms['messageId'] . "', '" . $sms['messagePart'] . "', '" . $sms['msisdn'] . "', '" . $sms['status'] . "', '" . $sms['status'] . "', 
        '" . $sms['statusCode'] . "', '" . $sms['status'] . "', 'sms','" . $sms['packageId'] . "', '" . $sms['project_id'] . "', '" . $sms['message'] . "', '" . $sms['oa'] . "','outbound', '" . $sms['created_at'] . "',  
        '" . $sms['created_at'] . "','2','" . $sms['messageId'] . "', '" . $sms['bulkId'] . "', '" . $sms['telco'] . "',  '" . $sms['type'] . "','" . $sms['link_id'] . "')";

         if ($conn->query($sql) === TRUE) {
           // echo "New msg record created successfully";
        } else {
          //  echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
    /
    $sql = array(); 
foreach( $data as $row ) {
    $sql[] = '("'.mysql_real_escape_string($row['text']).'", '.$row['category_id'].')';
}
mysql_query('INSERT INTO table (text, category) VALUES '.implode(',', $sql));


    echo ('completed db save in:' . (string)(microtime(true) - $time_start). "\n");
    $time_start = microtime(true);
    $timestamp = time();
    $base_uri =  "http://127.0.0.1:8000/localsendsafaricomsms/";
    $SourceAddress = "127.0.0.1";
    $url =  $base_uri . 'public/CMS/bulksms';
    //make request
    $client = new Client();
    $body = (string)json_encode([
        "timeStamp" => $timestamp,
        "dataSet" => $sdp_smses
    ]);
    $token = "";
    $headers =  [
        'Content-Type' => 'application/json', 'Accept' => 'application/json', 'X-Authorization' => 'Bearer ' . $token, 'SourceAddress' =>  $SourceAddress
    ];
    $request = new Request('POST', $url, $headers, $body);
    $promise = $client->sendAsync($request)->then(function ($response) {
        echo $response->getBody();
    });
    $promise->wait();
    echo " [x http request] Done in:" . (string)(microtime(true) - $time_start) ."\n";
    $msg->ack();
};

$channel->basic_qos(null, 1, null);
$channel->basic_consume('sms_queue', '', false, false, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();

