<?php
require_once DIR_SYSTEM . 'vltech/api/Facebook/autoload.php';

use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

class ControllerExtensionModuleVllogin extends Controller
{
    public $fb;

    public function index($setting) {
        $this->language->load('vltech/vllogin');

        $data = array();

        $data['app_id'] = $this->config->get('module_vllogin_appid');
        $data['app_secret'] = $this->config->get('module_vllogin_appsecret');
        $data["login_url"] = $this->getLoginUrl($data['app_id'], $data['app_secret']);

        return $this->load->view('vl/module/vllogin', $data);
    }

    public function getLoginUrl($app_id, $app_secret) {
        if(!session_id()) {
            session_start();
        }

        $fb = new Facebook([
            'app_id' => $app_id,
            'app_secret' => $app_secret,
            'default_graph_version' => 'v2.10',
        ]);

        $helper = $fb->getRedirectLoginHelper();

        $permissions = ['email']; // Optional permissions
        $actual_url = $this->url->link("extension/module/vllogin/fbcallback");
        $loginUrl = $helper->getLoginUrl($actual_url, $permissions);

        return $loginUrl;
    }

    public function fbcallback() {
        if(!session_id()) {
            session_start();
        }

        $fb = new Facebook([
            'app_id' => $this->config->get('module_vllogin_appid'),
            'app_secret' => $this->config->get('module_vllogin_appsecret'),
            'default_graph_version' => 'v2.10',
        ]);

        $helper = $fb->getRedirectLoginHelper();

        try {
            $accessToken = $helper->getAccessToken();
        } catch(FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        if (! isset($accessToken)) {
            if ($helper->getError()) {
                header('HTTP/1.0 401 Unauthorized');
                echo "Error: " . $helper->getError() . "\n";
                echo "Error Code: " . $helper->getErrorCode() . "\n";
                echo "Error Reason: " . $helper->getErrorReason() . "\n";
                echo "Error Description: " . $helper->getErrorDescription() . "\n";
            } else {
                header('HTTP/1.0 400 Bad Request');
                echo 'Bad request';
            }
            exit;
        }

        // Logged in
        echo '<h3>Access Token</h3>';
        var_dump($accessToken->getValue());

        // The OAuth 2.0 client handler helps us manage access tokens
        $oAuth2Client = $fb->getOAuth2Client();

        // Get the access token metadata from /debug_token
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);
        echo '<h3>Metadata</h3>';
        var_dump($tokenMetadata);

        // Validation (these will throw FacebookSDKException's when they fail)
        $tokenMetadata->validateAppId($this->config->get('module_vllogin_appid'));
        // If you know the user ID this access token belongs to, you can validate it here
        //$tokenMetadata->validateUserId('123');
        $tokenMetadata->validateExpiration();

        if (! $accessToken->isLongLived()) {
        // Exchanges a short-lived access token for a long-lived one
        try {
            $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
        } catch (FacebookSDKException $e) {
            echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
            exit;
        }

        echo '<h3>Long-lived</h3>';
        var_dump($accessToken->getValue());
        }

        $_SESSION['fb_access_token'] = (string) $accessToken;

        $fb->setDefaultAccessToken($_SESSION['fb_access_token']);

        // if(isset($_GET['code'])){
        //     header('Location: ./');
        // }

        // Getting user's profile info from Facebook
        $fbUser = null;
        try {
            $graphResponse = $fb->get('/me?fields=name,first_name,last_name,email,link,gender,picture');
            $fbUser = $graphResponse->getGraphUser();
        } catch(FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            session_destroy();
            // Redirect user back to app login page
            header("Location: ./");
            exit;
        } catch(FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        var_dump($fbUser['name']);
    }
}