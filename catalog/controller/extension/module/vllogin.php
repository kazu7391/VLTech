<?php
class ControllerExtensionModuleVllogin extends Controller
{
    public function index() {
        $this->language->load('vltech/vllogin');

        $data = array();

        if ($this->customer->isLogged()) {
			$this->response->redirect($this->url->link('account/account', '', true));
		}

        if(!empty($this->config->get('module_vllogin_fb_appid')) && !empty($this->config->get('module_vllogin_fb_appsecret'))) {
            $this->load->model('vltech/api/facebook_login');
            $data["fb_login_url"] = $this->model_vltech_api_facebook_login->getLoginUrl();
        }
        
        return $this->load->view('vl/module/vllogin', $data);
    }
}