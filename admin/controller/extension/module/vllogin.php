<?php 
class ControllerExtensionModuleVllogin extends Controller {
    private $error = array();
    
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

        $data['action'] = $this->url->link('extension/module/vllogin', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        if (isset($this->request->post['module_vllogin_status'])) {
            $data['module_vllogin_status'] = $this->request->post['module_vllogin_status'];
        } else {
            $data['module_vllogin_status'] = $this->config->get('module_vllogin_status');
        }

        if (isset($this->request->post['module_vllogin_appid'])) {
            $data['module_vllogin_appid'] = $this->request->post['module_vllogin_appid'];
        } else {
            $data['module_vllogin_appid'] = $this->config->get('module_vllogin_appid');
        }

        if (isset($this->request->post['module_vllogin_appsecret'])) {
            $data['module_vllogin_appsecret'] = $this->request->post['module_vllogin_appsecret'];
        } else {
            $data['module_vllogin_appsecret'] = $this->config->get('module_vllogin_appsecret');
        }

        $this->document->addStyle('view/stylesheet/vl/themeadmin.css');

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