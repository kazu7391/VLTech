<?php
class ControllerVltechAdvancemenu extends Controller
{
    private $error = array();

    public function menuList() {
        $this->load->language('extension/module/vlmenu');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('vltech/advancemenu');

        $this->getlist();
    }

    public function add() {
        $this->load->language('extension/module/vlmenu');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('vltech/advancemenu');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_vltech_advancemenu->addMenu($this->request->post);

            $post_data = $this->request->post;

            $top_position_data = $post_data['top_item_position'];

            foreach ($top_position_data as $menu_item_id => $position) {
                $this->model_vltech_advancemenu->editTopItemPosition($position, $menu_item_id);
            }

            if(isset($post_data['sub_item_position2'])) {
                $sub2_position_data = $post_data['sub_item_position2'];

                foreach ($sub2_position_data as $sub_item_id => $position) {
                    $this->model_vltech_advancemenu->editSubItemPosition($position, $sub_item_id);
                }
            }

            if(isset($post_data['sub_item_position3'])) {
                $sub3_position_data = $post_data['sub_item_position3'];

                foreach ($sub3_position_data as $sub_item_id => $position) {
                    $this->model_vltech_advancemenu->editSubItemPosition($position, $sub_item_id);
                }
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('vltech/advancemenu/menuList', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('extension/module/vlmenu');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('vltech/advancemenu');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $post_data = $this->request->post;

            $this->model_vltech_advancemenu->editMenu($this->request->get['menu_id'], $this->request->post);

            $top_position_data = $post_data['top_item_position'];

            foreach ($top_position_data as $menu_item_id => $position) {
                $this->model_vltech_advancemenu->editTopItemPosition($position, $menu_item_id);
            }

            if(isset($post_data['sub_item_position2'])) {
                $sub2_position_data = $post_data['sub_item_position2'];

                foreach ($sub2_position_data as $sub_item_id => $position) {
                    $this->model_vltech_advancemenu->editSubItemPosition($position, $sub_item_id);
                }
            }

            if(isset($post_data['sub_item_position3'])) {
                $sub3_position_data = $post_data['sub_item_position3'];

                foreach ($sub3_position_data as $sub_item_id => $position) {
                    $this->model_vltech_advancemenu->editSubItemPosition($position, $sub_item_id);
                }
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('vltech/advancemenu/menuList', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('extension/module/vlmenu');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('vltech/advancemenu');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $menu_id) {
                $this->model_vltech_advancemenu->deleteMenu($menu_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('vltech/advancemenu/menuList', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getlist();
    }

    public function getlist() {
        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
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

        if (!isset($this->request->get['module_id'])) {
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/module/vlmenu', 'user_token=' . $this->session->data['user_token'], true)
            );

            $data['back_to_module'] = $this->url->link('extension/module/vlmenu', 'user_token=' . $this->session->data['user_token'], true);
        } else {
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/module/vlmenu', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
            );

            $data['back_to_module'] = $this->url->link('extension/module/vlmenu', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
        }

        $data['add'] = $this->url->link('vltech/advancemenu/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['delete'] = $this->url->link('vltech/advancemenu/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);


        $filter_data = array(
            'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'           => $this->config->get('config_limit_admin')
        );

        $data['menu_list'] = array();

        $menus_total = $this->model_vltech_advancemenu->getMenuCount();

        $menus = $this->model_vltech_advancemenu->getMenuList($filter_data);

        if($menus) {
            foreach ($menus as $menu) {
                $data['menu_list'][] = array(
                    'menu_id'       => $menu['menu_id'],
                    'status'        => $menu['status'],
                    'name'          => $menu['name'],
                    'status_text'   => $menu['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                    'url'           => $this->url->link('vltech/advancemenu/edit', 'user_token=' . $this->session->data['user_token'] . '&menu_id=' . $menu['menu_id'] . $url, true)
                );
            }
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $pagination = new Pagination();
        $pagination->total = $menus_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('vltech/advancemenu/menuList', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($menus_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($menus_total - $this->config->get('config_limit_admin'))) ? $menus_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $menus_total, ceil($menus_total / $this->config->get('config_limit_admin')));

        $this->document->addStyle('view/stylesheet/vl/themeadmin.css');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('vl/advancemenu/list', $data));
    }

    public function getForm() {
        $data['text_form'] = !isset($this->request->get['menu_id']) ? $this->language->get('text_add') : $this->language->get('text_edit_menu');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = '';
        }

        $url = '';

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
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

        if (!isset($this->request->get['module_id'])) {
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/module/vlmenu', 'user_token=' . $this->session->data['user_token'], true)
            );
        } else {
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/module/vlmenu', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
            );
        }

        if (!isset($this->request->get['menu_id'])) {
            $data['action'] = $this->url->link('vltech/advancemenu/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('vltech/advancemenu/edit', 'user_token=' . $this->session->data['user_token'] . '&menu_id=' . $this->request->get['menu_id'] . $url, true);
        }

        if (!isset($this->request->get['menu_id'])) {
            $this->model_vltech_advancemenu->deleteTopItemByMenu('0');
            $data['cancel'] = $this->url->link('vltech/advancemenu/menuList', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['cancel'] = $this->url->link('vltech/advancemenu/menuList', 'user_token=' . $this->session->data['user_token'] . $url, true);
        }

        if (isset($this->request->get['menu_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $menu_info = $this->model_vltech_advancemenu->getMenu($this->request->get['menu_id']);
        }

        $data['user_token'] = $this->session->data['user_token'];

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif (!empty($menu_info)) {
            $data['name'] = $menu_info['name'];
        } else {
            $data['name'] = '';
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($menu_info)) {
            $data['status'] = $menu_info['status'];
        } else {
            $data['status'] = 1;
        }

        if (isset($this->request->post['menu_type'])) {
            $data['menu_type'] = $this->request->post['menu_type'];
        } elseif (!empty($menu_info)) {
            $data['menu_type'] = $menu_info['menu_type'];
        } else {
            $data['menu_type'] = 'horizontal';
        }

        $data['top_items'] = array();
        if (isset($this->request->get['menu_id'])) {
            $results = $this->model_vltech_advancemenu->getTopItems($this->request->get['menu_id']);

            foreach ($results as $result) {
                $sub_items_lv2 = $this->model_vltech_advancemenu->getSubItems($result['menu_item_id'], 2);

                $sub_items2 = array();

                if($sub_items_lv2) {
                    foreach ($sub_items_lv2 as $item) {
                        $sub_items_lv3 = $this->model_vltech_advancemenu->getSubItems($item['sub_menu_item_id'], 3);

                        $sub_items3 = array();

                        if($sub_items_lv3) {
                            foreach ($sub_items_lv3 as $s_item) {
                                $sub_items3[] = array(
                                    'item_id'   => $s_item['sub_menu_item_id'],
                                    'name'      => $s_item['name'],
                                    'position'  => $s_item['position'],
                                    'url'           => $this->url->link('vltech/advancemenu/editSubItem', 'user_token=' . $this->session->data['user_token'] . '&sub_menu_item_id=' . $s_item['sub_menu_item_id'] . '&menu_id=' . $this->request->get['menu_id'], true),
                                    'del_url'       => $this->url->link('vltech/advancemenu/deleteSubItem', 'user_token=' . $this->session->data['user_token'] . '&sub_menu_item_id=' . $s_item['sub_menu_item_id'] . '&menu_id=' . $this->request->get['menu_id'], true)
                                );
                            }
                        }

                        $sub_items2[] = array(
                            'sub_items'     => $sub_items3,
                            'item_id'   => $item['sub_menu_item_id'],
                            'name'      => $item['name'],
                            'position'  => $item['position'],
                            'url'           => $this->url->link('vltech/advancemenu/editSubItem', 'user_token=' . $this->session->data['user_token'] . '&sub_menu_item_id=' . $item['sub_menu_item_id'] . '&menu_id=' . $this->request->get['menu_id'], true),
                            'del_url'       => $this->url->link('vltech/advancemenu/deleteSubItem', 'user_token=' . $this->session->data['user_token'] . '&sub_menu_item_id=' . $item['sub_menu_item_id'] . '&menu_id=' . $this->request->get['menu_id'], true)
                        );
                    }
                }

                $data['top_items'][] = array(
                    'sub_items'     => $sub_items2,
                    'name'          => $result['name'],
                    'menu_item_id'  => $result['menu_item_id'],
                    'position'      => $result['position'],
                    'url'           => $this->url->link('vltech/advancemenu/editTopItem', 'user_token=' . $this->session->data['user_token'] . '&menu_item_id=' . $result['menu_item_id'] . '&menu_id=' . $this->request->get['menu_id'], true),
                    'del_url'       => $this->url->link('vltech/advancemenu/deleteTopItem', 'user_token=' . $this->session->data['user_token'] . '&menu_item_id=' . $result['menu_item_id'] . '&menu_id=' . $this->request->get['menu_id'], true)
                );
            }
        }

        if (isset($this->request->get['menu_id'])) {
            $data['top_items_form_url'] = $this->url->link('vltech/advancemenu/addTopItem', 'user_token=' . $this->session->data['user_token'] . '&menu_id=' . $this->request->get['menu_id'], true);
            $data['sub_item_add_form_url'] = $this->url->link('vltech/advancemenu/addSubItem', 'user_token=' . $this->session->data['user_token'] . '&menu_id=' . $this->request->get['menu_id'], true);
            $data['sub_item_edit_form_url'] = $this->url->link('vltech/advancemenu/editSubItem', 'user_token=' . $this->session->data['user_token'] . '&menu_id=' . $this->request->get['menu_id'], true);
            $data['multiple_del_url'] = $this->url->link('vltech/advancemenu/deleteMultipleItems', 'user_token=' . $this->session->data['user_token'] . '&menu_id=' . $this->request->get['menu_id'], true);
        } else {
            $data['top_items_form_url'] = $this->url->link('vltech/advancemenu/addTopItem', 'user_token=' . $this->session->data['user_token'], true);
            $data['sub_item_add_form_url'] = $this->url->link('vltech/advancemenu/addSubItem', 'user_token=' . $this->session->data['user_token'], true);
            $data['sub_item_edit_form_url'] = $this->url->link('vltech/advancemenu/editSubItem', 'user_token=' . $this->session->data['user_token'], true);
            $data['multiple_del_url'] = $this->url->link('vltech/advancemenu/deleteMultipleItems', 'user_token=' . $this->session->data['user_token'], true);
        }

        $data['get_top_items_url'] = $this->url->link('vltech/advancemenu/getTopItemsByAjax', 'user_token=' . $this->session->data['user_token'], true);

        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
            $data['loader_image'] = HTTPS_CATALOG . 'image/vltech/ajax-loader.gif';
        } else {
            $data['loader_image'] = HTTP_CATALOG . 'image/vltech/ajax-loader.gif';
        }

        $this->document->addScript('view/javascript/vl/color/jscolor.min.js');
        $this->document->addScript('view/javascript/vl/advancemenu.js');
        $this->document->addScript('view/javascript/jquery/jquery-ui/jquery-ui.min.js');
        $this->document->addStyle('view/javascript/jquery/jquery-ui/jquery-ui.min.css');
        $this->document->addStyle('view/stylesheet/vl/advancemenu.css');
        $this->document->addStyle('view/stylesheet/vl/themeadmin.css');
        $this->document->addScript('view/javascript/vl/switch-toggle/js/bootstrap-toggle.min.js');
        $this->document->addStyle('view/javascript/vl/switch-toggle/css/bootstrap-toggle.min.css');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('vl/advancemenu/form', $data));
    }

    public function getTopItemsByAjax() {
        $this->load->language('extension/module/vlmenu');

        $this->load->model('vltech/advancemenu');

        $json = array();

        $menu_id = $this->request->get['menu_id'];

        $results = $this->model_vltech_advancemenu->getTopItems($menu_id);

        $json['top_items'] = false;

        if($results) {
            $json['top_items'] = array();

            foreach ($results as $result) {
                $sub_items_lv2 = $this->model_vltech_advancemenu->getSubItems($result['menu_item_id'], 2);

                $sub_items2 = false;

                if($sub_items_lv2) {
                    foreach ($sub_items_lv2 as $item) {
                        $sub_items_lv3 = $this->model_vltech_advancemenu->getSubItems($item['sub_menu_item_id'], 3);

                        $sub_items3 = false;

                        if($sub_items_lv3) {
                            foreach ($sub_items_lv3 as $s_item) {
                                $sub_items3[] = array(
                                    'item_id'   => $s_item['sub_menu_item_id'],
                                    'name'      => $s_item['name'],
                                    'position'  => $s_item['position'],
                                    'url'           => $this->url->link('vltech/advancemenu/editSubItem', 'user_token=' . $this->session->data['user_token'] . '&sub_menu_item_id=' . $s_item['sub_menu_item_id'] . '&menu_id=' . $this->request->get['menu_id'], true),
                                    'del_url'       => $this->url->link('vltech/advancemenu/deleteSubItem', 'user_token=' . $this->session->data['user_token'] . '&sub_menu_item_id=' . $s_item['sub_menu_item_id'] . '&menu_id=' . $this->request->get['menu_id'], true)
                                );
                            }
                        }

                        $sub_items2[] = array(
                            'sub_items'     => $sub_items3,
                            'item_id'   => $item['sub_menu_item_id'],
                            'name'      => $item['name'],
                            'position'  => $item['position'],
                            'url'           => $this->url->link('vltech/advancemenu/editSubItem', 'user_token=' . $this->session->data['user_token'] . '&sub_menu_item_id=' . $item['sub_menu_item_id'] . '&menu_id=' . $menu_id, true),
                            'del_url'       => $this->url->link('vltech/advancemenu/deleteSubItem', 'user_token=' . $this->session->data['user_token'] . '&sub_menu_item_id=' . $item['sub_menu_item_id'] . '&menu_id=' . $menu_id, true)
                        );
                    }
                }

                $json['top_items'][] = array(
                    'sub_items'     => $sub_items2,
                    'menu_item_id'  => $result['menu_item_id'],
                    'name'          => $result['name'],
                    'position'      => $result['position'],
                    'url'           => $this->url->link('vltech/advancemenu/editTopItem', 'user_token=' . $this->session->data['user_token'] . '&menu_item_id=' . $result['menu_item_id'] . '&menu_id=' . $menu_id, true),
                    'del_url'       => $this->url->link('vltech/advancemenu/deleteTopItem', 'user_token=' . $this->session->data['user_token'] . '&menu_item_id=' . $result['menu_item_id'] . '&menu_id=' . $menu_id, true)
                );
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function addTopItem() {
        $this->load->language('extension/module/vlmenu');

        $this->load->model('vltech/advancemenu');

        $json = array();

        if (isset($this->request->get['menu_id'])) {
            $menu_id = $this->request->get['menu_id'];
        } else {
            $menu_id = 0;
        }

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateTopItemForm()) {
            $post_data = $this->request->post;

            $post_data['menu_id'] = $menu_id;

            if(isset($this->request->post['disable_title'])) {
                $post_data['has_title'] = 0;
            } else {
                $post_data['has_title'] = 1;
            }

            if(isset($this->request->post['disable_link'])) {
                $post_data['has_link'] = 0;
            } else {
                $post_data['has_link'] = 1;
            }

            if(isset($this->request->post['icon'])) {
                $post_data['icon'] = $this->request->post['icon'];
            } else {
                $post_data['icon'] = "";
            }

            if(isset($this->request->post['widget'])) {
                $post_data['sub_menu_content'] = $this->request->post['widget'];
            } else {
                $post_data['sub_menu_content'] = array();
            }

            $menu_top_item_id = $this->model_vltech_advancemenu->addTopItem($post_data);

            if(!$json) {
                $json['submit'] = true;
                $json['last_menu_top_item'] = $menu_top_item_id;
            }

            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
        } else {
            $this->getTopItemForm($menu_id);
        }
    }

    public function editTopItem() {
        $this->load->language('extension/module/vlmenu');

        $this->load->model('vltech/advancemenu');

        $json = array();

        if (isset($this->request->get['menu_id'])) {
            $menu_id = $this->request->get['menu_id'];
        } else {
            $menu_id = 0;
        }

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateTopItemForm()) {
            $post_data = $this->request->post;

            $post_data['menu_id'] = $menu_id;

            if(isset($this->request->post['disable_title'])) {
                $post_data['has_title'] = 0;
            } else {
                $post_data['has_title'] = 1;
            }

            if(isset($this->request->post['disable_link'])) {
                $post_data['has_link'] = 0;
            } else {
                $post_data['has_link'] = 1;
            }

            if(isset($this->request->post['icon'])) {
                $post_data['icon'] = $this->request->post['icon'];
            } else {
                $post_data['icon'] = "";
            }

            if(isset($this->request->post['widget'])) {
                $post_data['sub_menu_content'] = $this->request->post['widget'];
            } else {
                $post_data['sub_menu_content'] = array();
            }

            $this->model_vltech_advancemenu->editTopItem($post_data, $this->request->get['menu_item_id']);

            if(!$json) {
                $json['submit'] = true;
            }

            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
        } else {
            $this->getTopItemForm($menu_id);
        }
    }

    public function deleteTopItem() {
        $this->load->language('extension/module/vlmenu');

        $this->load->model('vltech/advancemenu');

        if (isset($this->request->get['menu_id'])) {
            $menu_id = $this->request->get['menu_id'];
        } else {
            $menu_id = 0;
        }

        $json = array();

        $json['menu_id'] = $menu_id;

        if (isset($this->request->get['menu_item_id'])) {
            $this->model_vltech_advancemenu->deleteTopItem($this->request->get['menu_item_id']);

            $json['result'] = true;
        } else {
            $json['result'] = false;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function deleteMultipleItems() {
        $this->load->language('extension/module/vlmenu');

        $this->load->model('vltech/advancemenu');

        if (isset($this->request->get['menu_id'])) {
            $menu_id = $this->request->get['menu_id'];
        } else {
            $menu_id = 0;
        }

        $json = array();

        $json['menu_id'] = $menu_id;

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $post_data = $this->request->post;

            if(isset($post_data['top_items'])) {
                $top_items = $post_data['top_items'];

                foreach ($top_items as $top_item_id) {
                    $this->model_vltech_advancemenu->deleteTopItem($top_item_id);
                }
            }

            if(isset($post_data['sub_items'])) {
                $sub_items = $post_data['sub_items'];

                foreach ($sub_items as $sub_item_id) {
                    $this->model_vltech_advancemenu->deleteSubItem($sub_item_id);
                }
            }

            $json['result'] = true;
        } else {
            $json['result'] = false;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getTopItemForm($menu_id) {
        $json = array();

        $data = array();

        $data['menu_id'] = $menu_id;

        $data['user_token'] = $this->session->data['user_token'];

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = '';
        }

        if (isset($this->request->get['menu_item_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $menu_item_info = $this->model_vltech_advancemenu->getTopItemById($this->request->get['menu_item_id']);
        }

        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif (!empty($menu_item_info)) {
            $data['name'] = $menu_item_info['name'];
        } else {
            $data['name'] = '';
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($menu_item_info)) {
            $data['status'] = $menu_item_info['status'];
        } else {
            $data['status'] = 0;
        }

        $this->load->model('localisation/language');

        $languages = $this->model_localisation_language->getLanguages();

        foreach ($languages as $language){
            if ($language['status']) {
                $data['languages'][] = array(
                    'name'  => $language['name'],
                    'language_id' => $language['language_id'],
                    'image' => $language['image'],
                    'code' => $language['code']
                );
            }
        }

        $lang_code = $this->config->get('config_admin_language');

        $lang = $this->model_vltech_advancemenu->getLanguageByCode($lang_code);

        $data['lang_id'] = $lang['language_id'];

        $data['links'] = $this->getLinks();

        if(isset($this->request->get['menu_item_id'])) {
            $menu_item_description = $this->model_vltech_advancemenu->getTopItemDescriptionById($this->request->get['menu_item_id']);
        } else {
            $menu_item_description = array();
        }

        if (isset($this->request->post['title'])) {
            $data['title'] = $this->request->post['title'];
        } elseif (!empty($menu_item_info) && $menu_item_description) {
            $data['title'] = $menu_item_description;
        } else {
            $data['title'] = array();
        }

        if (isset($this->request->post['disable_title'])) {
            $data['disable_title'] = true;
        } elseif (!empty($menu_item_info)) {
            $data['disable_title'] = !$menu_item_info['has_title'];
        } else {
            $data['disable_title'] = false;
        }

        if (isset($this->request->post['has_child'])) {
            $data['has_child'] = 1;
        } elseif (!empty($menu_item_info)) {
            $data['has_child'] = $menu_item_info['has_child'] ? 1 : 0;
        } else {
            $data['has_child'] = 0;
        }

        if (isset($this->request->post['link'])) {
            $data['link'] = $this->request->post['link'];
        } elseif (!empty($menu_item_info)) {
            $data['link'] = $menu_item_info['link'];
        } else {
            $data['link'] = '';
        }

        if (isset($this->request->post['disable_link'])) {
            $data['disable_link'] = true;
        } elseif (!empty($menu_item_info)) {
            $data['disable_link'] = !$menu_item_info['has_link'];
        } else {
            $data['disable_link'] = false;
        }

        if (isset($this->request->post['sub_menu_type'])) {
            $data['sub_menu_type'] = $this->request->post['sub_menu_type'];
        } elseif (!empty($menu_item_info)) {
            $data['sub_menu_type'] = $menu_item_info['sub_menu_type'];
        } else {
            $data['sub_menu_type'] = 'mega';
        }

        if (isset($this->request->post['position'])) {
            $data['position'] = $this->request->post['position'];
        } elseif (!empty($menu_item_info)) {
            $data['position'] = $menu_item_info['position'];
        } else {
            $data['position'] = 0;
        }

        if (isset($this->request->post['item_align'])) {
            $data['item_align'] = $this->request->post['item_align'];
        } elseif (!empty($menu_item_info)) {
            $data['item_align'] = $menu_item_info['item_align'];
        } else {
            $data['item_align'] = 'left';
        }

        if (isset($this->request->post['sub_menu_content_type'])) {
            $data['sub_menu_content_type'] = $this->request->post['sub_menu_content_type'];
        } elseif (!empty($menu_item_info)) {
            $data['sub_menu_content_type'] = $menu_item_info['sub_menu_content_type'];
        } else {
            $data['sub_menu_content_type'] = '';
        }

        if (isset($this->request->post['sub_menu_content_columns'])) {
            $data['sub_menu_content_columns'] = $this->request->post['sub_menu_content_columns'];
        } elseif (!empty($menu_item_info)) {
            $data['sub_menu_content_columns'] = $menu_item_info['sub_menu_content_columns'];
        } else {
            $data['sub_menu_content_columns'] = 0;
        }

        if (isset($this->request->post['sub_menu_content_width'])) {
            $data['sub_menu_content_width'] = $this->request->post['sub_menu_content_width'];
        } elseif (!empty($menu_item_info)) {
            $data['sub_menu_content_width'] = $menu_item_info['sub_menu_content_width'];
        } else {
            $data['sub_menu_content_width'] = '';
        }

        if (isset($this->request->post['category_id'])) {
            $data['category_id'] = $this->request->post['category_id'];
        } elseif (!empty($menu_item_info)) {
            $data['category_id'] = $menu_item_info['category_id'];
        } else {
            $data['category_id'] = 0;
        }

        if (isset($this->request->post['widget'])) {
            $data['widget'] = $this->request->post['widget'];
        } elseif (!empty($menu_item_info)) {
            $data['widget'] = json_decode($menu_item_info['sub_menu_content'], true);
        } else {
            $data['widget'] = array();
        }

        if (isset($this->request->post['icon'])) {
            $data['icon'] = $this->request->post['icon'];
        } elseif (!empty($menu_item_info)) {
            $data['icon'] = $menu_item_info['icon'];
        } else {
            $data['icon'] = '';
        }

        $this->load->model('tool/image');

        if (isset($this->request->post['icon']) && is_file(DIR_IMAGE . $this->request->post['icon'])) {
            $data['thumb'] = $this->model_tool_image->resize($this->request->post['icon'], 50, 50);
        } elseif (!empty($menu_item_info) && is_file(DIR_IMAGE . $menu_item_info['icon'])) {
            $data['thumb'] = $this->model_tool_image->resize($menu_item_info['icon'], 50, 50);
        } else {
            $data['thumb'] = $this->model_tool_image->resize('no_image.png', 50, 50);
        }

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 50, 50);

        // Categories
        $this->load->model('catalog/category');

        $categories = $this->model_vltech_advancemenu->getTopCategories();

        $data['categories'] = array();

        foreach ($categories as $category_id) {
            $category_info = $this->model_catalog_category->getCategory($category_id);

            if ($category_info) {
                $data['categories'][] = array(
                    'category_id' => $category_info['category_id'],
                    'name'        => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
                );
            }
        }

        $data['append_categories_link'] = $this->url->link('vltech/advancemenu/appendChildCategories', 'user_token=' . $this->session->data['user_token'], true);

        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
            $data['loader_image'] = HTTPS_CATALOG . 'image/vltech/ajax-loader.gif';
        } else {
            $data['loader_image'] = HTTP_CATALOG . 'image/vltech/ajax-loader.gif';
        }

        if(!isset($this->request->get['menu_item_id'])) {
            $data['action'] = $this->url->link('vltech/advancemenu/addTopItem', 'user_token=' . $this->session->data['user_token'] . '&menu_id=' . $menu_id, true);
        } else {
            $data['action'] = $this->url->link('vltech/advancemenu/editTopItem', 'user_token=' . $this->session->data['user_token'] . '&menu_item_id=' . $this->request->get['menu_item_id'] . '&menu_id=' . $menu_id, true);
        }

        if(!$json) {
            $json['html'] = $this->load->view('vl/advancemenu/top_item_form', $data);
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function addSubItem() {
        $this->load->language('extension/module/vlmenu');

        $this->load->model('vltech/advancemenu');

        if (isset($this->request->get['menu_id'])) {
            $menu_id = $this->request->get['menu_id'];
        } else {
            $menu_id = 0;
        }

        $json = array();

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateSubItemForm()) {
            $post_data = $this->request->post;

            $this->model_vltech_advancemenu->addSubItem($post_data);

            if(!$json) {
                $json['submit'] = true;
            }

            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
        } else {
            $this->getSubItemForm($menu_id);
        }
    }

    public function editSubItem() {
        $this->load->language('extension/module/vlmenu');

        $this->load->model('vltech/advancemenu');

        if (isset($this->request->get['menu_id'])) {
            $menu_id = $this->request->get['menu_id'];
        } else {
            $menu_id = 0;
        }

        $json = array();

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateSubItemForm()) {
            $post_data = $this->request->post;

            $this->model_vltech_advancemenu->editSubItem($post_data, $this->request->get['sub_item_id']);

            if(!$json) {
                $json['submit'] = true;
            }

            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
        } else {
            $this->getSubItemForm($menu_id);
        }
    }

    public function deleteSubItem() {
        $this->load->language('extension/module/vlmenu');

        $this->load->model('vltech/advancemenu');

        if (isset($this->request->get['menu_id'])) {
            $menu_id = $this->request->get['menu_id'];
        } else {
            $menu_id = 0;
        }

        $json = array();

        $json['menu_id'] = $menu_id;

        if (isset($this->request->get['sub_menu_item_id'])) {
            $this->model_vltech_advancemenu->deleteSubItem($this->request->get['sub_menu_item_id']);

            $json['result'] = true;
        } else {
            $json['result'] = false;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getSubItemForm($menu_id) {
        $json = array();

        $data = array();

        $data['menu_id'] = $menu_id;

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = '';
        }

        if (isset($this->request->get['sub_item_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $sub_menu_item_info = $this->model_vltech_advancemenu->getSubItemById($this->request->get['sub_item_id']);
        }

        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif (!empty($sub_menu_item_info)) {
            $data['name'] = $sub_menu_item_info['name'];
        } else {
            $data['name'] = '';
        }

        if(isset($this->request->get['parent_id'])) {
            $data['parent_menu_item_id'] = $this->request->get['parent_id'];
        } elseif (isset($this->request->post['name'])) {
            $data['parent_menu_item_id'] = $this->request->post['parent_menu_item_id'];
        } elseif (!empty($sub_menu_item_info)) {
            $data['parent_menu_item_id'] = $sub_menu_item_info['parent_menu_item_id'];
        } else {
            $data['parent_menu_item_id'] = 0;
        }

        if(isset($this->request->get['level'])) {
            $data['level'] = $this->request->get['level'];
        } elseif (isset($this->request->post['level'])) {
            $data['level'] = $this->request->post['level'];
        } elseif (!empty($sub_menu_item_info)) {
            $data['level'] = $sub_menu_item_info['level'];
        } else {
            $data['level'] = 0;
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($sub_menu_item_info)) {
            $data['status'] = $sub_menu_item_info['status'];
        } else {
            $data['status'] = 1;
        }

        if (isset($this->request->post['position'])) {
            $data['position'] = $this->request->post['position'];
        } elseif (!empty($sub_menu_item_info)) {
            $data['position'] = $sub_menu_item_info['position'];
        } else {
            $data['position'] = 0;
        }

        if (isset($this->request->post['link'])) {
            $data['link'] = $this->request->post['link'];
        } elseif (!empty($sub_menu_item_info)) {
            $data['link'] = $sub_menu_item_info['link'];
        } else {
            $data['link'] = '';
        }

        $this->load->model('localisation/language');

        $languages = $this->model_localisation_language->getLanguages();

        foreach ($languages as $language){
            if ($language['status']) {
                $data['languages'][] = array(
                    'name'  => $language['name'],
                    'language_id' => $language['language_id'],
                    'image' => $language['image'],
                    'code' => $language['code']
                );
            }
        }

        $data['links'] = $this->getLinks();

        if(isset($this->request->get['sub_item_id'])) {
            $menu_item_description = $this->model_vltech_advancemenu->getSubItemDescriptionById($this->request->get['sub_item_id']);
        } else {
            $menu_item_description = array();
        }

        if (isset($this->request->post['title'])) {
            $data['title'] = $this->request->post['title'];
        } elseif (!empty($sub_menu_item_info) && $menu_item_description) {
            $data['title'] = $menu_item_description;
        } else {
            $data['title'] = array();
        }

        $data['user_token'] = $this->session->data['user_token'];

        if(!isset($this->request->get['sub_item_id'])) {
            $data['action'] = $this->url->link('vltech/advancemenu/addSubItem', 'user_token=' . $this->session->data['user_token'] . '&parent_id=' . $this->request->get['parent_id'] . '&level=' . $this->request->get['level'], true);
        } else {
            $data['action'] = $this->url->link('vltech/advancemenu/editSubItem', 'user_token=' . $this->session->data['user_token'] . '&sub_item_id=' . $this->request->get['sub_item_id'], true);
        }

        if(!$json) {
            $json['html'] = $this->load->view('vl/advancemenu/sub_item_form', $data);
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function appendChildCategories() {
        $this->load->model('vltech/advancemenu');

        $json = array();

        $category_id = $this->request->get['category_id'];

        $json['category_link'] = 'index.php?route=product/category&path=' . $category_id;

        $childCategories = $this->model_vltech_advancemenu->getCategories($category_id);

        if($childCategories) {
            foreach ($childCategories as $child_category) {
                $json['child_categories'][] = array(
                    'category_id' => $child_category['category_id'],
                    'name'        => $child_category['name']
                );
            }
        } else {
            $json['child_categories'] = false;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    protected function validateTopItemForm() {
        if (!$this->user->hasPermission('modify', 'vltech/advancemenu')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_item_name');
        }

        return !$this->error;
    }

    protected function validateSubItemForm() {
        if (!$this->user->hasPermission('modify', 'vltech/advancemenu')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_item_name');
        }

        return !$this->error;
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'vltech/advancemenu')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_menu_name');
        }

        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'vltech/advancemenu')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    protected function validateCopy() {
        if (!$this->user->hasPermission('modify', 'vltech/advancemenu')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    protected function array_delete($array, $element) {
        return (is_array($element)) ? array_values(array_diff($array, $element)) : array_values(array_diff($array, array($element)));
    }

    public function autoCompleteCategory() {
        $json = array();

        if (isset($this->request->get['filter_name'])) {
            $this->load->model('vltech/advancemenu');

            $filter_data = array(
                'filter_name' => $this->request->get['filter_name'],
                'sort'        => 'name',
                'order'       => 'ASC',
                'start'       => 0,
                'limit'       => 5
            );

            $results = $this->model_vltech_advancemenu->getAllCategories($filter_data);

            foreach ($results as $result) {
                $json[] = array(
                    'category_id' => $result['category_id'],
                    'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
                );
            }
        }

        $sort_order = array();

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['name'];
        }

        array_multisort($sort_order, SORT_ASC, $json);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getLanguageData() {
        $this->load->model('localisation/language');

        $languages = $this->model_localisation_language->getLanguages();

        $json = array();

        foreach ($languages as $language){
            if ($language['status']) {
                $json['languages'][] = array(
                    'name'  => $language['name'],
                    'language_id' => $language['language_id'],
                    'image' => $language['image'],
                    'code' => $language['code']
                );
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getLinks() {
        $links = array();

        if (!empty($_SERVER['HTTPS'])) {
            // SSL connection
            $common_url = str_replace('http://', 'https://', HTTP_CATALOG);
        } else {
            $common_url = HTTP_CATALOG;
        }

        $links[] = array(
            'label' => 'Home Page',
            'url'   => $common_url
        );

        $links[] = array(
            'label' => 'Product Manufacturer',
            'url'   => 'index.php?route=product/manufacturer'
        );

        $links[] = array(
            'label' => 'Product Special',
            'url'   => 'index.php?route=product/special'
        );

        $links[] = array(
            'label' => 'Contact Us',
            'url'   => 'index.php?route=information/contact'
        );

        $filter_data = array(
            'start' => 0,
            'limit' => 100
        );

        $this->load->model('catalog/information');

        $informations = $this->model_catalog_information->getInformations($filter_data);

        foreach ($informations as $result) {
            $links[] = array(
                'label'          => $result['title'],
                'url'            => 'index.php?route=information/information&information_id=' . $result['information_id']
            );
        }

        $links[] = array(
            'label' => 'Site Map',
            'url'   => 'index.php?route=information/sitemap'
        );

        return $links;
    }
}
