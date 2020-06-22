<?php
class ControllerExtensionModuleVlshortdescription extends Controller {
    private $error = array();

    public function install() {
        $config = array(
            'module_vlshortdescription_status' => 1,
            'module_vlshortdescription_length' => 20
        );
        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting('module_vlshortdescription', $config);
    }

    public function index() {
        $this->load->language('extension/module/vlshortdescription');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');
        
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_vlshortdescription', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }

        $data = array();

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
            'href' => $this->url->link('extension/module/vlshortdescription', 'user_token=' . $this->session->data['user_token'], true)
        );

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['length'])) {
            $data['error_length'] = $this->error['length'];
        } else {
            $data['error_length'] = '';
        }

        $data['action'] = $this->url->link('extension/module/vlshortdescription', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        if (isset($this->request->post['module_vlshortdescription_status'])) {
            $data['module_vlshortdescription_status'] = $this->request->post['module_vlshortdescription_status'];
        } else {
            $data['module_vlshortdescription_status'] = $this->config->get('module_vlshortdescription_status');
        }

        if (isset($this->request->post['module_vlshortdescription_length'])) {
            $data['module_vlshortdescription_length'] = $this->request->post['module_vlshortdescription_length'];
        } else {
            $data['module_vlshortdescription_length'] = $this->config->get('module_vlshortdescription_length');
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('vl/module/vlshortdescription', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/vlshortdescription')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['module_vlshortdescription_length']) {
			$this->error['length'] = $this->language->get('error_length');
		}

        return !$this->error;
    }
}