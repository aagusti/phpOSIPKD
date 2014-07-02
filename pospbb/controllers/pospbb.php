<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class pospbb extends CI_Controller
{
    private $module = 'postransaksi';
    
    function __construct()
    {
        parent::__construct();
        
        if (active_module() != 'pospbb') {
            show_404();
            exit;
        }
        
        $this->load->model('login_model');
        if ($grp = $this->login_model->check_user_app()) {            
            $this->session->set_userdata('groupid'  , $grp->group_id);
            $this->session->set_userdata('groupkd'  , $grp->group_kode);
            $this->session->set_userdata('groupname', $grp->group_nama);
        }
        
        $this->load->model(array(
            'apps_model',
            'login_model',
            'posuser_model'
        ));
        
        if (!$this->posuser_model->set_user()) 
            $this->session->set_flashdata('msg_warning', 'Area Pembayaran Tidak Valid');
            
        //ngakalin user-pbbms link     
        $this->session->set_userdata('user_area', '0000000000');
    }
    
    public function index()
    {
        $data['tpnm']    = isset($this->session->userdata['tpnm']) ? $this->session->userdata['tpnm'] : '';
        $data['current'] = 'beranda';
        $data['apps']    = $this->apps_model->get_active_only();
        $this->load->view('vmain', $data);
    }
    
    public function op()
    {        
        $this->load->model('pbbm_model');
        
        $data['iDisplayLength'] = (isset($_GET['iDisplayLength']) && is_numeric($_GET['iDisplayLength'])) ? $_GET['iDisplayLength'] : 15;
        $data['iDisplayStart']  = (isset($_GET['iDisplayStart']) && is_numeric($_GET['iDisplayStart'])) ? $_GET['iDisplayStart'] : 0;
        $data['iSortCol_0']     = (isset($_GET['iSortCol_0']) && is_numeric($_GET['iSortCol_0'])) ? $_GET['iSortCol_0'] : 0;
        $data['iSortingCols']   = (isset($_GET['iSortingCols']) && is_numeric($_GET['iSortingCols'])) ? $_GET['iSortingCols'] : 1;
        $data['sEcho']          = (isset($_GET['sEcho']) && is_numeric($_GET['sEcho'])) ? $_GET['sEcho'] : 1;
        $data['sSearch']        = (isset($_GET['sSearch'])) ? $_GET['sSearch'] : "";
        $data['sSearch_0']      = (isset($_GET['sSearch_0'])) ? $_GET['sSearch_0'] : "";
        $data['sSearch_1']      = (isset($_GET['sSearch_1'])) ? $_GET['sSearch_1'] : "";
        $data['sSearch_2']      = (isset($_GET['sSearch_2'])) ? $_GET['sSearch_2'] : "";
        $data['sSearch_3']      = (isset($_GET['sSearch_3'])) ? $_GET['sSearch_3'] : "";
        $data['sSearch_4']      = (isset($_GET['sSearch_4'])) ? $_GET['sSearch_4'] : "";
        $data['sSortDir_0']     = (isset($_GET['sSortDir_0'])) ? $_GET['sSortDir_0'] : "asc";
        
        
        $this->load->model('kecModel', 'kec');
        $this->load->model('kelModel', 'kel');
        
        $data['user_kec_kd'] = get_user_kec_kd();
        $data['user_kel_kd'] = get_user_kel_kd();
        
        $tahun             = (isset($_GET['tahun']) ? $_GET['tahun'] : date('Y'));
        $data['pagetitle'] = 'OpenSIPKD';
        $data['title']     = 'PBB Dashboard';
        
        $data['main_content'] = '';
        
        /* Mencari Kode NOP */
        if (isset($_POST['nop_kd'])) {
            $nop_kd = $_POST['nop_kd'];
        } else if (isset($_GET['nop_kd'])) {
            $nop_kd = $_GET['nop_kd'];
        } else {
            $nop_kd = 0;
        }
        $nama_wp = '';
        if (isset($_POST['nama_wp'])) {
            $nama_wp = $_POST['nama_wp'];
        } else if (isset($_GET['nama_wp'])) {
            $nama_wp = $_GET['nama_wp'];
        } else {
            $nama_wp = "";
        }
        
        $data['tahun']   = $tahun;
        $data['nop_kd']  = $nop_kd;
        $data['nama_wp'] = $nama_wp;
        /* Explode NOP untuk mendapatkan Kode Blok, No Urut, dan Kode Jenis Objek Pajak */
        $kec_kd          = 0;
        $kel_kd          = 0;
        $blok_kd         = 0;
        $urut_no         = 0;
        $jns_kd          = 0;
        $nop             = str_replace('.', '', $nop_kd);
        $nop             = str_replace('-', '', $nop);
        
        if ($nop_kd != 0 && strlen($nop) == 18 && $nop_kd!='')  {
            
            $prop_kd = substr($nop, 0, 2);
            $kab_kd  = substr($nop, 2, 2);
            $kec_kd  = substr($nop, 4, 3);
            $kel_kd  = substr($nop, 7, 3);
            $blok_kd = substr($nop, 10, 3);
            $urut_no = substr($nop, 13, 4);
            $jns_kd  = substr($nop, 17, 1);
        }
        
        ## Get data ##
        $this->pbbm_model->setTahun($tahun);
        $this->pbbm_model->setKodeKecamatan($kec_kd);
        $this->pbbm_model->setKodeKelurahan($kel_kd);
        $this->pbbm_model->setKodeBlok($blok_kd);
        $this->pbbm_model->setNoUrut($urut_no);
        $this->pbbm_model->setKodeJenisOP($jns_kd);
        $data_source         = $this->pbbm_model->informasi_objek_pajak($nama_wp);
        $data['data_source'] = $data_source;
        
        $data['tpnm']    = isset($this->session->userdata['tpnm']) ? $this->session->userdata['tpnm'] : '';
        $data['current'] = 'stts';
        $data['apps']    = $this->apps_model->get_active_only();
        $this->load->view('opview', $data);
        
    }
    
    // tambahan menu transaksi dari pbbm ------------------------------------------------------
    
    function load_auth()
    {
        $this->load->library('module_auth', array(
            'module' => $this->module
        ));
    }
    
    public function transaksi()
    {
        $this->load_auth();
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect(active_module_url());
        }
        
        //ob_start("ob_gzhandler");
        $data['iDisplayLength'] = (isset($_GET['iDisplayLength']) && is_numeric($_GET['iDisplayLength'])) ? $_GET['iDisplayLength'] : 15;
        $data['iDisplayStart']  = (isset($_GET['iDisplayStart']) && is_numeric($_GET['iDisplayStart'])) ? $_GET['iDisplayStart'] : 0;
        $data['iSortCol_0']     = (isset($_GET['iSortCol_0']) && is_numeric($_GET['iSortCol_0'])) ? $_GET['iSortCol_0'] : 0;
        $data['iSortingCols']   = (isset($_GET['iSortingCols']) && is_numeric($_GET['iSortingCols'])) ? $_GET['iSortingCols'] : 1;
        $data['sEcho']          = (isset($_GET['sEcho']) && is_numeric($_GET['sEcho'])) ? $_GET['sEcho'] : 1;
        $data['sSearch']        = (isset($_GET['sSearch'])) ? $_GET['sSearch'] : "";
        $data['sSearch_0']      = (isset($_GET['sSearch_0'])) ? $_GET['sSearch_0'] : "";
        $data['sSearch_1']      = (isset($_GET['sSearch_1'])) ? $_GET['sSearch_1'] : "";
        $data['sSearch_2']      = (isset($_GET['sSearch_2'])) ? $_GET['sSearch_2'] : "";
        $data['sSearch_3']      = (isset($_GET['sSearch_3'])) ? $_GET['sSearch_3'] : "";
        $data['sSearch_4']      = (isset($_GET['sSearch_4'])) ? $_GET['sSearch_4'] : "";
        $data['sSortDir_0']     = (isset($_GET['sSortDir_0'])) ? $_GET['sSortDir_0'] : "asc";
        
        $this->load->model('kecModel', 'kec');
        $this->load->model('kelModel', 'kel');
        
        $data['user_kec_kd'] = get_user_kec_kd();
        $data['user_kel_kd'] = get_user_kel_kd();
        
        if (ENVIRONMENT == 'development') {
            $tglawal     = '01-07-2013';
            $tglakhir    = '30-07-2013';
            $tahun_sppt2 = '2013';
            $tahun_sppt1 = '2013';
        } else {
            $tglawal     = date('d-m-Y');
            $tglakhir    = date('d-m-Y');
            $tahun_sppt1 = '1999'; //minta tahun berjalan, kasih dahh
            $tahun_sppt2 = date('Y');
        }
        $kec_kd      = (isset($_GET['kec_kd']) ? $_GET['kec_kd'] : $data['user_kec_kd']);
        $kel_kd      = (isset($_GET['kel_kd']) ? $_GET['kel_kd'] : $data['user_kel_kd']);
        $tahun_sppt1 = (isset($_GET['tahun_sppt1']) ? $_GET['tahun_sppt1'] : $tahun_sppt1);
        $tahun_sppt2 = (isset($_GET['tahun_sppt2']) ? $_GET['tahun_sppt2'] : $tahun_sppt2);
        
        $buku = (isset($_GET['buku']) ? $_GET['buku'] : '15');
        
        if (isset($_GET['tglawal']) && $_GET['tglawal']) {
            $tglawal = $_GET['tglawal'];
        }
        
        if (isset($_GET['tglakhir']) && $_GET['tglakhir']) {
            $tglakhir = $_GET['tglakhir'];
        }
        
        $trantypes         = $this->uri->segment(3, 1);
        $data['buku']      = $buku;
        $data['trantypes'] = $trantypes;
        $data['tglawal']   = $tglawal;
        $data['tglakhir']  = $tglakhir;
        $data['kec_kd']    = $kec_kd;
        $data['kel_kd']    = $kel_kd;
        
        $data['tahun_sppt1'] = $tahun_sppt1;
        $data['tahun_sppt2'] = $tahun_sppt2;
        
        $tp_kd = (isset($_GET['tp_kd']) ? $_GET['tp_kd'] : '');
        $data['tp_kd'] = $tp_kd;
        $data['tp']    = $this->load->model('tp_model')->get_select();
        
        $data['kecamatan']    = $this->kec->getRecord(get_user_kec_kd());
        $data['kelurahan']    = $this->kel->getRecord($kec_kd, get_user_kel_kd());
        $data['pagetitle']    = 'OpenSIPKD';
        $data['title']        = 'Transaksi Pembayaran';
        $data['main_content'] = '';
        
        $data['data_source'] = active_module_url() . "loaddata/transaksi$trantypes?buku=$buku&tahun_sppt1=$tahun_sppt1&tahun_sppt2=$tahun_sppt2&tglawal=$tglawal&tglakhir=$tglakhir&kec_kd=$kec_kd&kel_kd=$kel_kd&tp_kd=$tp_kd";
        
        $data['tpnm']    = isset($this->session->userdata['tpnm']) ? $this->session->userdata['tpnm'] : '';
        
        $data['current'] = 'transaksi';
        $data['apps']    = $this->apps_model->get_active_only();
        $this->load->view('transview', $data);
    }
    
    public function tranmonths()
    {
        $this->load_auth();
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect(active_module_url());
        }
        
        //ob_start("ob_gzhandler");
        $buku                   = (isset($_GET['buku']) ? $_GET['buku'] : '11');
        $data['buku']           = $buku;
        $data['iDisplayLength'] = (isset($_GET['iDisplayLength']) && is_numeric($_GET['iDisplayLength'])) ? $_GET['iDisplayLength'] : 15;
        $data['iDisplayStart']  = (isset($_GET['iDisplayStart']) && is_numeric($_GET['iDisplayStart'])) ? $_GET['iDisplayStart'] : 0;
        $data['iSortCol_0']     = (isset($_GET['iSortCol_0']) && is_numeric($_GET['iSortCol_0'])) ? $_GET['iSortCol_0'] : 0;
        $data['iSortingCols']   = (isset($_GET['iSortingCols']) && is_numeric($_GET['iSortingCols'])) ? $_GET['iSortingCols'] : 1;
        $data['sEcho']          = (isset($_GET['sEcho']) && is_numeric($_GET['sEcho'])) ? $_GET['sEcho'] : 1;
        $data['sSearch']        = (isset($_GET['sSearch'])) ? $_GET['sSearch'] : "";
        $data['sSearch_0']      = (isset($_GET['sSearch_0'])) ? $_GET['sSearch_0'] : "";
        $data['sSearch_1']      = (isset($_GET['sSearch_1'])) ? $_GET['sSearch_1'] : "";
        $data['sSearch_2']      = (isset($_GET['sSearch_2'])) ? $_GET['sSearch_2'] : "";
        $data['sSearch_3']      = (isset($_GET['sSearch_3'])) ? $_GET['sSearch_3'] : "";
        $data['sSearch_4']      = (isset($_GET['sSearch_4'])) ? $_GET['sSearch_4'] : "";
        $data['sSortDir_0']     = (isset($_GET['sSortDir_0'])) ? $_GET['sSortDir_0'] : "asc";
        
        $this->load->model('kecModel', 'kec');
        $this->load->model('kelModel', 'kel');
        
        $data['user_kec_kd'] = get_user_kec_kd();
        $data['user_kel_kd'] = get_user_kel_kd();
        
        $tahun       = (isset($_GET['tahun']) ? $_GET['tahun'] : date('Y'));
        $tahun_sppt1 = (isset($_GET['tahun_sppt1']) ? $_GET['tahun_sppt1'] : date('Y')); //'1999'); //minta tahun berjalan, kasih dahh
        $tahun_sppt2 = (isset($_GET['tahun_sppt2']) ? $_GET['tahun_sppt2'] : date('Y'));
        $buku        = (isset($_GET['buku']) ? $_GET['buku'] : '11');
        $kec_kd      = (isset($_GET['kec_kd']) ? $_GET['kec_kd'] : $data['user_kec_kd']);
        $kel_kd      = (isset($_GET['kel_kd']) ? $_GET['kel_kd'] : $data['user_kel_kd']);
        
        
        if (ENVIRONMENT == 'development' && !$tahun) {
            $tahun       = '2013';
            $tahun_sppt2 = '2013';
        }
        
        //die($tahun_sppt1);
        $data['tahun']       = $tahun;
        $data['tahun_sppt1'] = $tahun_sppt1;
        $data['tahun_sppt2'] = $tahun_sppt2;
        $data['buku']        = $buku;
        $data['kec_kd']      = $kec_kd;
        $data['kel_kd']      = $kel_kd;
        $data['kecamatan']   = $this->kec->getRecord(get_user_kec_kd());
        $data['kelurahan']   = $this->kel->getRecord($kec_kd, get_user_kel_kd());
        
        $tp_kd = (isset($_GET['tp_kd']) ? $_GET['tp_kd'] : '');
        $data['tp_kd'] = $tp_kd;
        $data['tp']    = $this->load->model('tp_model')->get_select();
        
        $data['pagetitle']   = 'OpenSIPKD';
        $data['title']       = 'Transaksi Pembayaran';
        
        $data['main_content'] = '';
        
        $data['data_source'] = active_module_url() . "loaddata/tranmonths?tahun=$tahun&tahun_sppt1=$tahun_sppt1&tahun_sppt2=$tahun_sppt2&buku=$buku&kec_kd=$kec_kd&kel_kd=$kel_kd&tp_kd=$tp_kd";
        
        $data['tpnm']    = isset($this->session->userdata['tpnm']) ? $this->session->userdata['tpnm'] : '';
        
        $data['current'] = 'transaksi';
        $data['apps']    = $this->apps_model->get_active_only();
        $this->load->view('tranmonthsview', $data);
    }
    
    public function tranuser()
    {
        $this->load_auth();
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect(active_module_url());
        }
        
        //ob_start("ob_gzhandler");
        $data['iDisplayLength'] = (isset($_GET['iDisplayLength']) && is_numeric($_GET['iDisplayLength'])) ? $_GET['iDisplayLength'] : 15;
        $data['iDisplayStart']  = (isset($_GET['iDisplayStart']) && is_numeric($_GET['iDisplayStart'])) ? $_GET['iDisplayStart'] : 0;
        $data['iSortCol_0']     = (isset($_GET['iSortCol_0']) && is_numeric($_GET['iSortCol_0'])) ? $_GET['iSortCol_0'] : 0;
        $data['iSortingCols']   = (isset($_GET['iSortingCols']) && is_numeric($_GET['iSortingCols'])) ? $_GET['iSortingCols'] : 1;
        $data['sEcho']          = (isset($_GET['sEcho']) && is_numeric($_GET['sEcho'])) ? $_GET['sEcho'] : 1;
        $data['sSearch']        = (isset($_GET['sSearch'])) ? $_GET['sSearch'] : "";
        $data['sSearch_0']      = (isset($_GET['sSearch_0'])) ? $_GET['sSearch_0'] : "";
        $data['sSearch_1']      = (isset($_GET['sSearch_1'])) ? $_GET['sSearch_1'] : "";
        $data['sSearch_2']      = (isset($_GET['sSearch_2'])) ? $_GET['sSearch_2'] : "";
        $data['sSearch_3']      = (isset($_GET['sSearch_3'])) ? $_GET['sSearch_3'] : "";
        $data['sSearch_4']      = (isset($_GET['sSearch_4'])) ? $_GET['sSearch_4'] : "";
        $data['sSortDir_0']     = (isset($_GET['sSortDir_0'])) ? $_GET['sSortDir_0'] : "asc";
        
        $this->load->model('kecModel', 'kec');
        $this->load->model('kelModel', 'kel');
        
        $data['user_kec_kd'] = get_user_kec_kd();
        $data['user_kel_kd'] = get_user_kel_kd();
        
        if (ENVIRONMENT == 'development') {
            $tglawal     = '01-07-2013';
            $tglakhir    = '30-07-2013';
            $tahun_sppt2 = '2013';
            $tahun_sppt1 = '2013';
        } else {
            $tglawal     = date('d-m-Y');
            $tglakhir    = date('d-m-Y');
            $tahun_sppt1 = '1999'; //minta tahun berjalan, kasih dahh
            $tahun_sppt2 = date('Y');
        }
        $kec_kd      = (isset($_GET['kec_kd']) ? $_GET['kec_kd'] : $data['user_kec_kd']);
        $kel_kd      = (isset($_GET['kel_kd']) ? $_GET['kel_kd'] : $data['user_kel_kd']);
        $tahun_sppt1 = (isset($_GET['tahun_sppt1']) ? $_GET['tahun_sppt1'] : $tahun_sppt1);
        $tahun_sppt2 = (isset($_GET['tahun_sppt2']) ? $_GET['tahun_sppt2'] : $tahun_sppt2);
        
        $buku = (isset($_GET['buku']) ? $_GET['buku'] : '15');
        
        if (isset($_GET['tglawal']) && $_GET['tglawal']) {
            $tglawal = $_GET['tglawal'];
        }
        
        if (isset($_GET['tglakhir']) && $_GET['tglakhir']) {
            $tglakhir = $_GET['tglakhir'];
        }
        
        $trantypes         = $this->uri->segment(3, 1);
        $data['buku']      = $buku;
        $data['trantypes'] = $trantypes;
        $data['tglawal']   = $tglawal;
        $data['tglakhir']  = $tglakhir;
        $data['kec_kd']    = $kec_kd;
        $data['kel_kd']    = $kel_kd;
        
        $data['tahun_sppt1'] = $tahun_sppt1;
        $data['tahun_sppt2'] = $tahun_sppt2;
        
        $user_kd = (isset($_GET['user_kd']) ? $_GET['user_kd'] : '');
        $data['user_kd'] = $user_kd;
        $data['usertbl']    = $this->load->model('users_model')->get_all();
        
        $data['kecamatan']    = $this->kec->getRecord(get_user_kec_kd());
        $data['kelurahan']    = $this->kel->getRecord($kec_kd, get_user_kel_kd());
        $data['pagetitle']    = 'OpenSIPKD';
        $data['title']        = 'Transaksi Pembayaran';
        $data['main_content'] = '';
        
        $data['data_source'] = active_module_url() . "loaddata/tranuser$trantypes?buku=$buku&tahun_sppt1=$tahun_sppt1&tahun_sppt2=$tahun_sppt2&tglawal=$tglawal&tglakhir=$tglakhir&kec_kd=$kec_kd&kel_kd=$kel_kd&user_kd=$user_kd";
        
        $data['tpnm']    = isset($this->session->userdata['tpnm']) ? $this->session->userdata['tpnm'] : '';
        
        $data['current'] = 'transaksi';
        $data['apps']    = $this->apps_model->get_active_only();
        $this->load->view('tranuserv', $data);
    }
    
}
