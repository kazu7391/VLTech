<?php 
class ControllerExtensionModuleVllogin extends Controller {
    private $error = array();

    public $socials = array(
        array(
            "id" => "facebook",
            "name" => "Facebook",
        ),

        array(
            "id" => "google",
            "name" => "Google"
        ),

        array(
            "id" => "twitter",
            "name" => "Twitter"
        ),

        array(
            "id" => "instagram",
            "name" => "Instagram"
        ),

        array(
            "id" => "linkedin",
            "name" => "Linkedin"
        ),

        array(
            "id" => "whatsapp",
            "name" => "WhatsApp"
        ),

        array(
            "id" => "pinterest",
            "name" => "Pinterest"
        ),

        array(
            "id" => "tumblr",
            "name" => "Tumblr"
        ),

        array(
            "id" => "snapchat",
            "name" => "Snapchat"
        ),
    );
    
    public function index() {
        $this->load->language('extension/module/vllogin');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_vllogin', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/vllogin', 'user_token=' . $this->session->data['user_token'], true)
        );

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['socials'] = $this->socials;

        $data['action'] = $this->url->link('extension/module/vllogin', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        if (isset($this->request->post['module_vllogin_status'])) {
            $data['module_vllogin_status'] = $this->request->post['module_vllogin_status'];
        } else {
            $data['module_vllogin_status'] = $this->config->get('module_vllogin_status');
        }

        if (isset($this->request->post['module_vllogin_socials'])) {
            $data['module_vllogin_socials'] = $this->request->post['module_vllogin_socials'];
        } else {
            $data['module_vllogin_socials'] = $this->config->get('module_vllogin_socials');
        }

        if (isset($this->request->post['module_vllogin_fb_appid'])) {
            $data['module_vllogin_fb_appid'] = $this->request->post['module_vllogin_fb_appid'];
        } else {
            $data['module_vllogin_fb_appid'] = $this->config->get('module_vllogin_fb_appid');
        }

        if (isset($this->request->post['module_vllogin_fb_appsecret'])) {
            $data['module_vllogin_fb_appsecret'] = $this->request->post['module_vllogin_fb_appsecret'];
        } else {
            $data['module_vllogin_fb_appsecret'] = $this->config->get('module_vllogin_fb_appsecret');
        }

        $this->document->addStyle('view/stylesheet/vl/themeadmin.css');
        $this->document->addStyle('view/stylesheet/vl/advancelogin.css');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('vl/module/vllogin', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/vllogin')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}