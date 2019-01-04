<?php
/**
 * Created by PhpStorm.
 * User: yassine
 * Date: 02/01/19
 * Time: 08:52 م
 */

require __DIR__."/vendor/autoload.php";



/**
 * @return Google_Client
 * @throws Google_Exception
 */
function getClient()
{
    $client = new Google_Client();
    $client->setApplicationName('Google Drive API PHP Quickstart');
    $client->setScopes(Google_Service_Drive::DRIVE_METADATA_READONLY);
    $client->setAuthConfig('credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');
    $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php');


    // Load previously authorized token from a file, if it exists.
    // The file token.json stores the user's access and refresh tokens, and is
    // created automatically when the authorization flow completes for the first
    // time.
    $tokenPath = 'token.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }

    // If there is no previous token or it's expired.
    if ($client->isAccessTokenExpired()) {
        // Refresh the token if possible, else fetch a new one.
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            //$authCode = "4/xgAZcJHGDm17qro4ZgUT_JLtjl5ysORl7e9xpEyMd5Hv2OTxQfE3wQw";

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);

            // Check to see if there was an error.
            if (array_key_exists('error', $accessToken)) {
                throw new Exception(join(', ', $accessToken));
            }
        }
        // Save the token to a file.
        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }
        file_put_contents($tokenPath, json_encode($client->getAccessToken()));
    }
    return $client;
}


try{

    // Get the API client and construct the service object.
    $client = getClient();
    $service = new Google_Service_Drive($client);

// Print the names and IDs for up to 10 files.
    $optParams = array(
        'pageSize' => 100,
        'fields' => 'nextPageToken, files(id, name)'
    );
    $results = $service->files->listFiles($optParams);

    if (count($results->getFiles()) == 0) {
        print "No files found.\n";
    } else {
        print "Files:\n";
        foreach ($results->getFiles() as $file) {
            printf("%s (%s)\n", $file->getName(), $file->getId());
        }
    }


}catch (Exception $e){

}
