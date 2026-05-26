<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Http;

class FcmService
{
    public static function send($token, $title, $body)
    {
        // path service account
        $serviceAccountPath = storage_path('app/firebase/firebase.json');

        // scope Firebase Cloud Messaging
        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];

        // ambil access token Google
        $credentials = new ServiceAccountCredentials(
            $scopes,
            $serviceAccountPath
        );

        $accessToken = $credentials->fetchAuthToken()['access_token'];

        // ambil project ID dari file JSON
        $serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);
        $projectId = $serviceAccount['project_id'];

        // endpoint FCM v1
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        // payload
        $payload = [
            "message" => [
                "token" => $token,
                "notification" => [
                    "title" => $title,
                    "body" => $body,
                ],
                "android" => [
                    "priority" => "high"
                ],
                "apns" => [
                    "headers" => [
                        "apns-priority" => "10"
                    ]
                ]
            ]
        ];

        // kirim request
        $response = Http::withToken($accessToken)
            ->post($url, $payload);

        return $response->json();
    }
}