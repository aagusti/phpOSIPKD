<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class admin extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		if(active_module()!='admin') { 
		  if(!is_super_admin()) {
			    redirect('logout');
			}
		}
		$this->load->model(array('apps_model'));
	}
	
	public function index()
	{
		$data['current'] = 'beranda';
		$data['apps']    = $this->apps_model->get_active_only();
		$this->load->view('vmain', $data);
	}
}
