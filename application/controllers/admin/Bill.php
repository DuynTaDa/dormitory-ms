<?php

class Bill extends CI_Controller {

	protected $interval = 7;
	protected $elec_vat = 1/10;
	protected $wat_vat = 1/10;
	protected $bvmt = 5/100;

	public function __construct() {
		parent::__construct();
		$this->load->model('bill_model');
		$this->load->model('admin_model');
		$this->load->model('room_model');
		$this->load->model('term_model');
		$this->load->model('price_model');
	}

	public function elecBill() {
		$bill_type = 2;
		$account = $this->session->userdata('admin');
		if($account == null)
		{
			redirect('login');
		}
		$admin = $this->admin_model->getAccountByEmail($account['email']);
		$assignment = $this->admin_model->getBuildingByManager($admin['admin_id']);
		if ($assignment != null) 
		{
			$building_id = $assignment['building_id'];
			$rooms = $this->room_model->getRoomsByBuilding($building_id);
			$bills = $this->bill_model->getAll($building_id, $bill_type);
			$cmd = $this->input->post('cmd');
			if ($cmd != null) {
				$room_id = $this->input->post('room_id');
				$date = $this->input->post('month');
				$month = explode('-', $date)[1]; 
				$term = $this->term_model->getCurrentTerm();
				$index = $this->input->post('index');
				$lastBill = $this->getLastBill($room_id, $bill_type, $month, $term);
				if ($lastBill == null) {
					$used = $index;
				}
				$used = $index - $lastBill['index'];
				$priceList = $this->price_model->getPriceList($bill_type);
				$totalPay = $this->getElecTotalPay($used, $priceList);
				$totalPay += $totalPay * $this->elec_vat;
				$bill_params = array(
					'bill_type' => $bill_type,
					'room_id' => $room_id,
					'month' => $month,
					'term_id' => $term['term_id'],
					'index' => $index,
					'used' => $used,
					'total_pay' => ceil($totalPay)
				);
				$bill_id = $this->bill_model->addBill($bill_params);
				$this->addDeadline($bill_id, $date);
				redirect('electricity-bill');
			}
			$layoutParams = array(
				'bills' => $bills,
				'rooms' => $rooms
			);
			$content = $this->load->view('admin/elec_bill', $layoutParams, true);
		}else 
		{
			$this->session->set_flashdata('error', 'Have no building assigned.');
			$content = $this->load->view('admin/water_bill', '',true);
		}

		$data = array();
		$data['customCss'] = array('assets/css/settings.css');
		$data['parent_id'] = 8;
		$data['sub_id'] = 81;
		$data['group'] = $admin['position_id'] == 1 || $admin['position_id'] == 2 ? 1 : 2;
		$data['content'] = $content;
		$this->load->view('admin_main_layout', $data);
	}

	public function waterBill() {
		$bill_type = 1;
		$account = $this->session->userdata('admin');
		if($account == null)
		{
			redirect('login');
		}
		$admin = $this->admin_model->getAccountByEmail($account['email']);
		$assignment = $this->admin_model->getBuildingByManager($admin['admin_id']);
		if ($assignment != null) 
		{
			$building_id = $assignment['building_id'];
			$rooms = $this->bill_model->getRooms($building_id);
			$bills = $this->bill_model->getAll($building_id, $bill_type);
			$cmd = $this->input->post('cmd');
			if ($cmd != null) {
				$room_id = $this->input->post('room_id');
				$date = $this->input->post('month');
				$month = explode('-', $date)[1]; 
				$term = $this->term_model->getCurrentTerm();
				$index = $this->input->post('index');
				$lastBill = $this->getLastBill($room_id, $bill_type, $month, $term);
				if ($lastBill == null) {
					$used = $index;
				}
				$used = $index - $lastBill['index'];
				$priceList = $this->price_model->getPriceList($bill_type);
				$totalPay = $this->getWatTotalPay($used, $priceList);
				$totalPay += $totalPay * $this->wat_vat + $totalPay * $this->bvmt;
				$bill_params = array(
					'bill_type' => $bill_type,
					'room_id' => $room_id,
					'month' => $month,
					'term_id' => $term['term_id'],
					'index' => $index,
					'used' => $used,
					'total_pay' => ceil($totalPay)
				);
				$bill_id = $this->bill_model->addBill($bill_params);
				$this->addDeadline($bill_id, $date);
				redirect('water-bill');
			}
			$layoutParams = array(
				'bills' => $bills,
				'rooms' => $rooms
			);
			$content = $this->load->view('admin/water_bill', $layoutParams, true);
		}else 
		{
			$this->session->set_flashdata('error', 'Have no building assigned.');
			$content = $this->load->view('admin/water_bill', '',true);
		}

		$data = array();
		$data['customCss'] = array('assets/css/settings.css');
		$data['parent_id'] = 8;
		$data['sub_id'] = 82;
		$data['group'] = $admin['position_id'] == 1 || $admin['position_id'] == 2 ? 1 : 2;
		$data['content'] = $content;
		$this->load->view('admin_main_layout', $data);
	}

	public function roomBill() {
		$account = $this->session->userdata('admin');
		if($account == null)
		{
			redirect('login');
		}
		$admin = $this->admin_model->getAccountByEmail($account['email']);
		$assignment = $this->admin_model->getBuildingByManager($admin['admin_id']);
		if($assignment != null )
		{
			$building_id = $assignment['building_id'];
			$rooms = $this->room_model->getRoomsByBuilding($building_id);
			$bills = $this->bill_model->getRoomBill();
			$cmd = $this->input->post('cmd');
			if ($cmd != null) {
				$room_id = $this->input->post('room_id');
				$date = $this->input->post('date');
				$term = $this->term_model->getCurrentTerm();
				$params = array(
					'term_id' => $term['term_id'],
					'room_id' => $room_id,
					'deadline' => strtotime($date),
				);
				$this->bill_model->addRoomBill($params);
				redirect('room-bill');
			}
			$layoutParams = array(
				'bills' => $bills,
				'rooms' => $rooms
			);
			$content = $this->load->view('admin/room_bill', $layoutParams, true);
		}else 
		{
			$this->session->set_flashdata('error', 'Have no building assigned.');
			$content = $this->load->view('admin/room_bill', '',true);
		}

		$data = array();
		$data['customCss'] = array('assets/css/settings.css');
		$data['parent_id'] = 8;
		$data['sub_id'] = 83;
		$data['group'] = $admin['position_id'] == 1 || $admin['position_id'] == 2 ? 1 : 2;
		$data['content'] = $content;
		$this->load->view('admin_main_layout', $data);
	}

	public function getLastBill($room_id, $bill_type, $month, $term) {
		if ($month == 1) {
			$param = array(
				'bill_type' => $bill_type,
				'room_id' => $room_id,
				'month' => 12,
				'term_id' => $term['term_id']
			);
		}else if ($month == 9) {
			$param = array(
				'bill_type' => $bill_type,
				'room_id' => $room_id,
				'month' => 6,
				'term_id' => $term['term_id']
			);
		}else {
			$param = array(
				'bill_type' => $bill_type,
				'room_id' => $room_id,
				'month' => $month - 1,
				'term_id' => $term['term_id']
			);
		}
		$lastBill = $this->bill_model->getBill($param);
		return $lastBill;
	}

	public function getElecTotalPay($used, $priceList) {
		$total = 0;
		if ($used >= 50) {
			$total += 50 * $priceList[0]['price'];
			$used -= 50;
			if ($used >= 50) {
				$total += 50 * $priceList[1]['price'];
				$used -= 50;
				if ($used >= 100) {
					$total += 100 * $priceList[2]['price'];
					$used -= 100;
					if ($used >= 100) {
						$total += 100 * $priceList[3]['price'];
						$used -= 100;
						if ($used >= 100) {
							$total += 100 * $priceList[4]['price'];
							$used -= 100;
							$total += $used * $priceList[5]['price'];
						}else {
							$total += $used * $priceList[4]['price'];
						}
					}else {
						$total += $used * $priceList[3]['price'];
					}
				}else {
					$total += $used * $priceList[2]['price'];
				}
			}else {
				$total += $used * $priceList[1]['price'];
			}
		}else {
			$total += $used * $priceList[0]['price'];
		}
		return $total;
	}

	public function getWatTotalPay($used, $priceList) {
		$total = 0;
		if($used >= 10) {
			$total += 10 * $priceList[0]['price'];
			$used -= 10;
			if($used >= 10) {
				$total += 10 * $priceList[1]['price'];
				$used -= 10;
				if($used >= 10) {
					$total += 10 * $priceList[2]['price'];
					$used -= 10;
					$total += $used * $priceList[3]['price'];
				}else {
					$total += $used * $priceList[2]['price'];
				}
			}else {
				$total += $used * $priceList[1]['price'];
			}
		}else {
			$total += $used * $priceList[0]['price'];
		}
		return $total;
	}

	public function addDeadline($bill_id, $date) {
		$deadline = strtotime(date("Y-m-d", strtotime($date)) . "+" . $this->interval ." days");
		$pay_params = array(
			'bill_id' => $bill_id,
			'deadline' => $deadline,
			'paid' => '',
			'status' => 0
		);
		$this->bill_model->addBillPay($pay_params);
	}

	public function paid($bill_id) {
		$params = array(
			'paid' => time(),
			'status' => 1
		);
		$this->bill_model->updatePaid($params, $bill_id);
		redirect('electricity-bill');
	}

	public function paid1($bill_id) {
		$params = array(
			'paid' => time(),
			'status' => 1
		);
		$this->bill_model->updatePaid($params, $bill_id);
		redirect('water-bill');
	}

	public function disable($bill_id) {
		$this->bill_model->disable($bill_id);
		redirect('electricity-bill');
	}

	public function disable1($bill_id) {
		$this->bill_model->disable($bill_id);
		redirect('water-bill');
	}

	public function paid2($id) {
		$params = array(
			'paid' => time(),
			'status' => 1
		);
		$this->bill_model->updatePaidRoom($params, $id);
		redirect('room-bill');
	}

	public function disable2($id) {
		$this->bill_model->disableRoomPay($id);
		redirect('room-bill');
	}
}