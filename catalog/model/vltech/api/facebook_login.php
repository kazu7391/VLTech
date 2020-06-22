<?php
require_once DIR_SYSTEM . 'vltech/api/Facebook/autoload.php';

use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

class ModelVltechApiFacebookLogin extends Model
{
    private $app_id = "";
    private $app_secret = "";
    private $fb = null;

    private function getFBAppData() {
        if(!session_id()) {
           session_start();
        }

        $this->app_id = $this->config->get('module_vllogin_fb_appid');
        $this->app_secret = $this->config->get('module_vllogin_fb_appsecret');
        $this->fb = new Facebook([
            'app_id' => $this->app_id,
            'app_secret' => $this->app_secret,
            'default_graph_version' => 'v2.10',
        ]);
    }

    public function getLoginUrl() {
        $this->getFBAppData();
        $helper = $this->fb->getRedirectLoginHelper();

        $permissions = ['email']; // Optional permissions
        $actual_url = $this->url->link('vltech/advancelogin/facebook_callback');
        $login_url = $helper->getLoginUrl($actual_url, $permissions);

        return $login_url;
    }

    public function getFBUserData() {
        $this->getFBCallback();

        // Getting user's profile info from Facebook
        $fb_user = null;
        try {
            $graphResponse = $this->fb->get('/me?fields=name,first_name,last_name,email,link,gender,picture');
            $fb_user = $graphResponse->getGraphUser();
        } catch(FacebookResponseException $e) {
            session_destroy();
            $this->response->redirect($this->url->link('vltech/advancelogin/error', '&error=' . 'Graph returned an error: ' . $e->getMessage()));   
            exit;
        } catch(FacebookSDKException $e) {
            $this->response->redirect($this->url->link('vltech/advancelogin/error', '&error=' . 'Facebook SDK returned an error: ' . $e->getMessage()));    
            exit;
        }

        return $fb_user;
    }

    protected function getFBCallback() {
        $this->getFBAppData();
        // $helper = $this->fb->getRedirectLoginHelper();

        $accessToken = $this->getFBAccessToken();        

        $_SESSION['fb_access_token'] = (string) $accessToken;

        $this->fb->setDefaultAccessToken($_SESSION['fb_access_token']);
    }

    protected function getFBAccessToken() {
        $helper = $this->fb->getRedirectLoginHelper();

        try {
            $accessToken = $helper->getAccessToken();
        } catch(FacebookResponseException $e) {
            // When Graph returns an error
            $this->response->redirect($this->url->link('vltech/advancelogin/error', '&error=' . 'Graph returned an error: ' . $e->getMessage()));   
            exit;
        } catch(FacebookSDKException $e) {
            // When validation fails or other local issues
            $this->response->redirect($this->url->link('vltech/advancelogin/error', '&error=' . 'Facebook SDK returned an error: ' . $e->getMessage()));    
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

        if ($this->validateAccessToken($accessToken) && ! $accessToken->isLongLived()) {
            // Exchanges a short-lived access token for a long-lived one
            try {
                $accessToken = $this->getFBOAuth2Client()->getLongLivedAccessToken($accessToken);
            } catch (FacebookSDKException $e) {
                $this->response->redirect($this->url->link('vltech/advancelogin/error', '&error=' . '<p>Error getting long-lived access token: ' . $e->getMessage() . '</p>\n\n'));    
                exit;
            }
        }

        return $accessToken;
    }

    protected function getFBOAuth2Client() {
        // The OAuth 2.0 client handler helps us manage access tokens
        $oAuth2Client = $this->fb->getOAuth2Client();

        return $oAuth2Client;
    }

    protected function validateAccessToken($accessToken) {
        $oAuth2Client = $this->getFBOAuth2Client();

        // Get the access token metadata from /debug_token
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);

        // Validation (these will throw FacebookSDKException's when they fail)
        $tokenMetadata->validateAppId($this->app_id);
        // If you know the user ID this access token belongs to, you can validate it here
        //$tokenMetadata->validateUserId('123');
        $tokenMetadata->validateExpiration();

        return true;
    }
}