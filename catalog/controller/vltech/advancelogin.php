<?php
class ControllerVltechAdvancelogin extends Controller
{
	private $error = array();

	// Social Callback
	public function facebook_callback() {
		$this->load->language('account/login');
		$this->load->language('account/register');

		$this->load->model('vltech/api/facebook_login');
		$this->load->model('account/customer');

		$fb_user = $this->model_vltech_api_facebook_login->getFBUserData();

		// Check user existed or not
		// Existed -> Login || Not Existed -> Register
		if ($this->model_account_customer->getTotalCustomersByEmail($fb_user['email'])) {
			if(!empty($fb_user['email']) && $this->validateLogin($fb_user)) {
				$this->login($fb_user);	
			} else {
				$this->response->redirect($this->url->link('vltech/advancelogin/error', '&error=' . $this->error['warning']));
			}
		} else {
			$new_user_data = array(
				'firstname' => $fb_user['first_name'],
				'lastname'  => $fb_user['last_name'],
				'email'		=> $fb_user['email'],
				'telephone' => '0987654321',
				'customer_group_id' => $this->config->get('config_customer_group_id'),
				'password' => $fb_user['id']
			);

			$this->register($new_user_data);
			// var_dump(($fb_user));
		}
	}

	public function register($data = array()) {
		$this->load->language('account/register');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment-with-locales.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

		$this->load->model('account/customer');


		// if (isset($this->request->post['telephone'])) {
		// 	$data['telephone'] = $this->request->post['telephone'];
		// } else {
		// 	$data['telephone'] = '';
		// }

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateRegister()) {
			$customer_id = $this->model_account_customer->addCustomer($this->request->post);

			// Clear any previous login attempts for unregistered accounts.
			$this->model_account_customer->deleteLoginAttempts($this->request->post['email']);

			$this->customer->login($this->request->post['email'], $this->request->post['password']);

			unset($this->session->data['guest']);

			$this->response->redirect($this->url->link('account/success'));
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_register'),
			'href' => "#"
		);

		$data['text_account_already'] = sprintf($this->language->get('text_account_already'), $this->url->link('account/login', '', true));

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['telephone'])) {
			$data['error_telephone'] = $this->error['telephone'];
		} else {
			$data['error_telephone'] = '';
		}

		if (isset($this->error['custom_field'])) {
			$data['error_custom_field'] = $this->error['custom_field'];
		} else {
			$data['error_custom_field'] = array();
		}

		if (isset($this->error['confirm'])) {
			$data['error_confirm'] = $this->error['confirm'];
		} else {
			$data['error_confirm'] = '';
		}

		$data['action'] = $this->url->link('vltech/advance/register', '', true);

		$data['customer_groups'] = array();

		if (is_array($this->config->get('config_customer_group_display'))) {
			$this->load->model('account/customer_group');

			$customer_groups = $this->model_account_customer_group->getCustomerGroups();

			foreach ($customer_groups as $customer_group) {
				if (in_array($customer_group['customer_group_id'], $this->config->get('config_customer_group_display'))) {
					$data['customer_groups'][] = $customer_group;
				}
			}
		}

		if (isset($this->request->post['customer_group_id'])) {
			$data['customer_group_id'] = $this->request->post['customer_group_id'];
		} else {
			$data['customer_group_id'] = $this->config->get('config_customer_group_id');
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/register', $data));
	}

    public function login($data) {
        // Unset guest
		unset($this->session->data['guest']);

		// Default Shipping Address
		$this->load->model('account/address');

		if ($this->config->get('config_tax_customer') == 'payment') {
			$this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
		}

		if ($this->config->get('config_tax_customer') == 'shipping') {
			$this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
		}

		// Wishlist
		if (isset($this->session->data['wishlist']) && is_array($this->session->data['wishlist'])) {
			$this->load->model('account/wishlist');

			foreach ($this->session->data['wishlist'] as $key => $product_id) {
				$this->model_account_wishlist->addWishlist($product_id);

				unset($this->session->data['wishlist'][$key]);
			}
		}

		// Added strpos check to pass McAfee PCI compliance test (http://forum.opencart.com/viewtopic.php?f=10&t=12043&p=151494#p151295)
		if (isset($this->request->post['redirect']) && $this->request->post['redirect'] != $this->url->link('account/logout', '', true) && (strpos($this->request->post['redirect'], $this->config->get('config_url')) !== false || strpos($this->request->post['redirect'], $this->config->get('config_ssl')) !== false)) {
			$this->response->redirect(str_replace('&amp;', '&', $this->request->post['redirect']));
		} else {
			$this->response->redirect($this->url->link('account/account', '', true));
		}
	}

	public function error() {
		$err_msg = $this->request->get['error'];

		$data = array();

		$data['error_msg'] = $err_msg;

		$this->response->setOutput($this->load->view('vl/advancelogin/error', $data));
	}

	protected function validateRegister() {

	}
	
	protected function validateLogin($data) {
		// Check how many login attempts have been made.
		$login_info = $this->model_account_customer->getLoginAttempts($data['email']);

		if ($login_info && ($login_info['total'] >= $this->config->get('config_login_attempts')) && strtotime('-1 hour') < strtotime($login_info['date_modified'])) {
			$this->error['warning'] = $this->language->get('error_attempts');
		}

		// Check if customer has been approved.
		$customer_info = $this->model_account_customer->getCustomerByEmail($data['email']);

		if ($customer_info && !$customer_info['status']) {
			$this->error['warning'] = $this->language->get('error_approved');
		}

		if (!$this->error) {
			if (!$this->customer->login($data['email'], $data['id'])) { // Use FB ID for password
				$this->error['warning'] = $this->language->get('error_login');

				$this->model_account_customer->addLoginAttempt($data['email']);
			} else {
				$this->model_account_customer->deleteLoginAttempts($data['email']);
			}
		}

		return !$this->error;
	}

	protected function checkUser($data) {
		// Check user is existed -> Login
		

		// check user chua ton tai -> register
	}
}