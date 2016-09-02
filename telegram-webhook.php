<?php
include("token.php");

function request_url($method)
{
	global $token;
	return "https://api.telegram.org/bot" . $token . "/". $method;
}

function send_reply($chatid, $msgid, $text)
{
    $data = array(
        'chat_id' => $chatid,
        'text'  => $text,
        'reply_to_message_id' => $msgid

    );
    // use key 'http' even if you send the request to https://...
    $options = array(
    	'http' => array(
        	'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        	'method'  => 'POST',
        	'content' => http_build_query($data),
    	),
    );
    $context  = stream_context_create($options);
    $result = file_get_contents(request_url('sendMessage'), false, $context);
}

function create_response($text)
{
   return "definisi " . $text;
}

function process_message($message)
{
    $updateid = $message["update_id"];
    $message_data = $message["message"];
    if (isset($message_data["text"])) {
	$chatid = $message_data["chat"]["id"];
        $message_id = $message_data["message_id"];
        $text = $message_data["text"];
        $response = create_response($text);
        send_reply($chatid, $message_id, $response);
    }
    return $updateid;
}

$entityBody = file_get_contents('php://input');
$message = json_decode($entityBody, true);
process_message($message);

?>