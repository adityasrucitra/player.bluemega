<?php
namespace App\Libraries;

Class TelegramBot
{
    protected $token;
    protected $chatId;

    function __construct($token= '', $chatId = '')
    {
        $this->token = $token;
        $this->chatId = $chatId;
    }

    function sendMessage($message = '')
    {
        $apiUrl = "https://api.telegram.org/bot{$this->token}/sendMessage";

        $data = [
            'chat_id' => $this->chatId,
            'text' => $message,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $responseData = json_decode($response, true);

        $response = [
            'status' => true,
            'message' => $message,
            'errors' => null
        ];
        if (!$responseData['ok']) {
            $response['errors'] = $responseData['description'];
        } 

        return $response;
    }

    /**
     * .
     */
    function getUsername($userId = null)
    {
        // $apiUrl = "https://api.telegram.org/bot{$this->token}/sendMessage";
        $apiUrl = "https://api.telegram.org/bot{$this->token}/getChatMember";

        $data = [
            'chat_id' => $this->chatId,
            'user_id' => $userId,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $responseData = json_decode($response, true);

        $response = [
            'status' => false,
            'username' => null,
            'errors' => null
        ];
        if (!$responseData['ok']) {
            $response['errors'] = $responseData['description'];
        } 
        $response['status'] = true;
        $user = $responseData['result']['user'];
        $response['username'] = isset($user['username']) ? $user['username'] : null;

        return $response;
    }

}