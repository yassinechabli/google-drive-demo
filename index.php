<?php

require_once __DIR__.'/vendor/autoload.php';
    session_start();
    try{


        $client = new Google_Client();
        $client->setAuthConfig('credentials.json');
        $client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);

        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $client->setAccessToken($_SESSION['access_token']);
            $drive = new Google_Service_Drive($client);
            $files = $drive->files->listFiles();
            echo json_encode($files);
        } else {
            $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/google_drive/src/oauth2callback.php';
            header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
        }

    }catch (Exception $e){
        var_dump($e->getMessage());
    }
