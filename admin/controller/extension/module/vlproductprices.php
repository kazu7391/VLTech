<?php
class ControllerExtensionModuleVlproductprices extends Controller
{
    private $error = array();

    public function install() {
        $config = array(
            'module_vlproductprices' => 1
        );
        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting('module_vlproductprices', $config);
        $this->setupData();
    }

    public function index() {
        $this->load->language('extension/module/vlproductprices');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_vlproductprices', $this->request->post);

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
            'href' => $this->url->link('extension/module/vlproductprices', 'user_token=' . $this->session->data['user_token'], true)
        );

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['action'] = $this->url->link('extension/module/vlproductprices', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        if (isset($this->request->post['module_vlproductprices_status'])) {
            $data['module_vlproductprices_status'] = $this->request->post['module_vlproductprices_status'];
        } else {
            $data['module_vlproductprices_status'] = $this->config->get('module_vlproductprices_status');
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('vl/module/vlproductprices', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/vlproductprices')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    private function setupData() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "vlproductprices` (
                `vlproductprices_id` int(11) NOT NULL AUTO_INCREMENT,
                `product_id` int(11) NOT NULL,
                `type` varchar(255) NOT NULL,
                `price` decimal(15,4) NOT NULL DEFAULT '0',
                `link` text,
            PRIMARY KEY (`vlproductprices_id`)
        ) DEFAULT COLLATE=utf8_general_ci;");
    }
}
