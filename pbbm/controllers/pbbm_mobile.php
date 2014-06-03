<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class pbbm_mobile extends CI_Controller {
    private $module = 'pbbm_mobile';

    function __construct() {
        parent::__construct();
        /*
        if (!is_login()) {
            $this->load->model('login_model');
        
            $id = $this->login_model->get_appid($this->module);
            if ($id) {
                $this->session->set_userdata('active_module', $this->module);
                $this->session->set_userdata('app_id', $id->id);
            }   
        }
        */
        
        /*
        if(active_module()!=$this->module) { 
            show_404();
            exit;
        }
        */
        
        $this->load->model(array('apps_model', 'pbb_mobile_model'));
    }

    public function index() {
        $thn                    = (isset($_GET['tahun']) && is_numeric($_GET['tahun'])) ? $_GET['tahun'] : 0;
        $tmpthn                 = (isset($_GET['tahun'])) ? $_GET['tahun'] : '';
        $nop                    = (isset($_GET['nop'])) ? $_GET['nop'] : '';
        $data['carinop']        = $nop;
        $data['caritahun']      = $tmpthn;
        $data['result']         = $this->pbb_mobile_model->informasi_objek_pajak($nop, $thn);
        $data['countresult']    = ($data['result']) ? count($data['result']) : 0;
        $data['faction']        = active_module_url();
        $data['current']        = 'home';
        $data['found']          = true;
        if (ENVIRONMENT=='development'){
            $data['apps']       = $this->apps_model->get_active_only();
        }
        if ((isset($_GET['tahun']) || isset($_GET['nop'])) && $data['countresult'] == 0) {
            $data['found']      = false;
        }
        $this->load->view('vmain_mobile', $data);
    }
}