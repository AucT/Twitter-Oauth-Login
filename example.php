<?php
/* Example of using Abraham Twitter Library for social login (retrieving user profile data)
 * Based on http://stackoverflow.com/a/28695591/1767461 by zoomi
 * This example is made for myself, if I loose my code
*/
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

//Edit the following config variables
$consumer_key = 'consumer_key';
$consumer_secret = 'consumer_secret';
//callback url. In this example it is using current url
$callback = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];



if (isset($_SESSION['oauth_token'])) {
    $oauth_token = $_SESSION['oauth_token'];
    unset($_SESSION['oauth_token']);

    $connection = new Abraham\TwitterOAuth\TwitterOAuth($consumer_key, $consumer_secret);
    //necessary to get access token other wise u will not have permision to get user info
    $params = array("oauth_verifier" => $_GET['oauth_verifier'], "oauth_token" => $_GET['oauth_token']);
    $access_token = $connection->oauth("oauth/access_token", $params);
    //now again create new instance using updated return oauth_token and oauth_token_secret because old one expired if u dont u this u will also get token expired error
    $connection = new Abraham\TwitterOAuth\TwitterOAuth($consumer_key, $consumer_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);
    $content = $connection->get("account/verify_credentials");
    //Printing the profile data
    print_r($content);
} else {
    //this code will return your valid url which u can use in iframe src to popup or can directly view the page as its happening in this example
    $connection = new Abraham\TwitterOAuth\TwitterOAuth($consumer_key, $consumer_secret);
    $temporary_credentials = $connection->oauth('oauth/request_token', array("oauth_callback" => $callback));
    $_SESSION['oauth_token'] = $temporary_credentials['oauth_token'];
    $_SESSION['oauth_token_secret'] = $temporary_credentials['oauth_token_secret'];
    $url = $connection->url("oauth/authorize", array("oauth_token" => $temporary_credentials['oauth_token']));
    // REDIRECTING TO THE URL
    header('Location: ' . $url);
}
