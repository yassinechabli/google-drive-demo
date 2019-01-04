<?php
require_once '../vendor/autoload.php';

    session_start();
    try{
        $client = new Google_Client();
        $client->setAuthConfigFile('../credentials.json');
        $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/google_drive/src/oauth2callback.php');
        $client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);

        if (! isset($_GET['code'])) {
            $auth_url = $client->createAuthUrl();
            header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
        } else {
            $client->authenticate($_GET['code']);
            $_SESSION['access_token'] = $client->getAccessToken();
            $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/google_drive';
            header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
    }

    }catch (Exception $e){
        var_dump($e->getMessage());
    }