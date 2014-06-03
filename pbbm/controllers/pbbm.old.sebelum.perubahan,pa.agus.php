<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class pbbm extends CI_Controller
{
    private $module = 'pbbmt';
    
    function __construct()
    {
        parent::__construct();
        
        if (active_module() != 'pbbm') {
            show_404();
            exit;
        }
        
        $this->load->model('login_model');
        if ($grp = $this->login_model->check_user_app()) {
            $this->session->set_userdata('groupid', $grp->group_id);
            $this->session->set_userdata('groupkd', $grp->group_kode);
            $this->session->set_userdata('groupname', $grp->group_nama);
        }
        
        if ($this->uri->segment(3) == 'tranmonths' || $this->uri->segment(3) == 'transaksi' || $this->uri->segment(3) == 'transaksi')
            $this->module = 'pbbmt';
        if ($this->uri->segment(3) == 'realisasi' || $this->uri->segment(3) == 'lb' || $this->uri->segment(3) == 'kb')
            $this->module = 'pbbmr';
        if ($this->uri->segment(3) == 'piutang')
            $this->module = 'pbbmp';
        if ($this->uri->segment(3) == 'op')
            $this->module = 'pbbmop';
        
        
        $this->load->model(array(
            'apps_model',
            'login_model',
            'pbbm_model'
        ));
        $this->pbbm_model->set_userarea();
    }
    
    function load_auth()
    {
        $this->load->library('module_auth', array(
            'module' => $this->module
        ));
    }
    
    public function index()
    {
        $data['current'] = 'home';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['tahun']   = date('Y');
        $this->load->view('vmain', $data);
    }
    
    public function get_realisasi() {
        $data['apps']    = $this->apps_model->get_active_only();
        
        $r                   = $this->pbbm_model->realisasi_dashboard();
        $data['amt_daily']   = number_format($r->amt_daily, 0, ',', '.');
        $data['amt_weekly']  = number_format($r->amt_weekly, 0, ',', '.');
        $data['amt_monthly'] = number_format($r->amt_monthly, 0, ',', '.');
        $data['amt_yearly']  = number_format($r->amt_yearly, 0, ',', '.');
        echo json_encode($data);
    }
    
    public function dashboard()
    {
        $this->load->model('pbbEisModel', 'pbb');
        $data['pagetitle'] = 'PBB-BPHTB';
        
        $data['title']     = 'PBB Dashboard';
        $data['subtitle']  = 'Jumlah Transaksi';
        $data['subtitle2'] = 'Nominal Transaksi';
        
        /* Today PBB */
        $today                = $this->pbb->today_pbb();
        $data['today_amount'] = number_format($today["amount_transaksi"], 0, ',', '.');
        $data['today_trans']  = number_format($today["jumlah_transaksi"], 0, ',', '.');
        $data['today_cap']    = $this->pbb->today_cap;
        
        /* Week PBB */
        $week                = $this->pbb->week_pbb();
        $data['week_amount'] = number_format($week["amount_transaksi"], 0, ',', '.');
        $data['week_trans']  = number_format($week["jumlah_transaksi"], 0, ',', '.');
        $data['week_cap']    = $this->pbb->week_cap;
        
        /* Month PBB */
        $month                = $this->pbb->month_pbb();
        $data['month_amount'] = number_format($month["amount_transaksi"], 0, ',', '.');
        $data['month_trans']  = number_format($month["jumlah_transaksi"], 0, ',', '.');
        $data['month_cap']    = $this->pbb->month_cap;
        
        /* Year PBB */
        $year                = $this->pbb->year_pbb();
        $data['year_amount'] = number_format($year["amount_transaksi"], 0, ',', '.');
        $data['year_trans']  = number_format($year["jumlah_transaksi"], 0, ',', '.');
        $data['year_cap']    = $this->pbb->year_cap;
        
        
        $data['current'] = 'dashboard';
        $data['apps']    = $this->apps_model->get_active_only();
        $this->load->view('dashboardview', $data);
        
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
            $tahun_sppt1 = date('Y'); //'1999'; //minta tahun berjalan, kasih dahh
            $tahun_sppt2 = date('Y');
        }
        $kec_kd      = (isset($_GET['kec_kd']) ? $_GET['kec_kd'] : $data['user_kec_kd']);
        $kel_kd      = (isset($_GET['kel_kd']) ? $_GET['kel_kd'] : $data['user_kel_kd']);
        $tahun_sppt1 = (isset($_GET['tahun_sppt1']) ? $_GET['tahun_sppt1'] : $tahun_sppt1);
        $tahun_sppt2 = (isset($_GET['tahun_sppt2']) ? $_GET['tahun_sppt2'] : $tahun_sppt2);
        
        $buku = (isset($_GET['buku']) ? $_GET['buku'] : '11');
        
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
        
        
        $data['kecamatan']    = $this->kec->getRecord(get_user_kec_kd());
        $data['kelurahan']    = $this->kel->getRecord($kec_kd, get_user_kel_kd());
        $data['pagetitle']    = 'OpenSIPKD';
        $data['title']        = 'Transaksi Pembayaran';
        $data['main_content'] = '';
        
        $data['data_source'] = active_module_url() . "loaddata/transaksi$trantypes?buku=$buku&tahun_sppt1=$tahun_sppt1&tahun_sppt2=$tahun_sppt2&tglawal=$tglawal&tglakhir=$tglakhir&kec_kd=$kec_kd&kel_kd=$kel_kd";
        
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
        $data['pagetitle']   = 'OpenSIPKD';
        $data['title']       = 'Transaksi Pembayaran';
        
        $data['main_content'] = '';
        
        $data['data_source'] = active_module_url() . "loaddata/tranmonths?tahun=$tahun&tahun_sppt1=$tahun_sppt1&tahun_sppt2=$tahun_sppt2&buku=$buku&kec_kd=$kec_kd&kel_kd=$kel_kd";
        
        $data['current'] = 'transaksi';
        $data['apps']    = $this->apps_model->get_active_only();
        $this->load->view('tranmonthsview', $data);
    }
    
    public function realisasi()
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
        
        $kec_kd = (isset($_GET['kec_kd']) ? $_GET['kec_kd'] : $data['user_kec_kd']);
        $kel_kd = (isset($_GET['kel_kd']) ? $_GET['kel_kd'] : $data['user_kel_kd']);
        
        if (ENVIRONMENT == 'development') {
            $tahun    = '2012';
            $tglawal  = '30-08-2012';
            $tglakhir = '30-08-2012';
        } else {
            $tahun    = date('Y');
            $tglawal  = date('d-m-Y');
            $tglakhir = date('d-m-Y');
        }
        
        $tahun    = (isset($_GET['tahun']) && $_GET['tahun'] ? $_GET['tahun'] : $tahun);
        $tglawal  = (isset($_GET['tglawal']) && $_GET['tglawal'] ? $_GET['tglawal'] : $tglawal);
        $tglakhir = (isset($_GET['tglakhir']) && $_GET['tglakhir'] ? $_GET['tglakhir'] : $tglakhir);
        $buku     = (isset($_GET['buku']) ? $_GET['buku'] : '11');
        
        $data['buku']      = $buku;
        $data['tahun']     = $tahun;
        $data['tglawal']   = $tglawal;
        $data['tglakhir']  = $tglakhir;
        $data['kec_kd']    = $kec_kd;
        $data['kel_kd']    = $kel_kd;
        $data['kecamatan'] = $this->kec->getRecord(get_user_kec_kd());
        $data['kelurahan'] = $this->kel->getRecord($kec_kd, get_user_kel_kd());
        
        
        $data['pagetitle'] = 'OpenSIPKD';
        $data['title']     = 'Realisasi';
        
        $data['main_content'] = '';
        
        $data['data_source'] = active_module_url() . "loaddata/realisasi?tahun=$tahun&tglawal=$tglawal&tglakhir=$tglakhir&kec_kd=$kec_kd&kel_kd=$kel_kd&buku=$buku";
        
        $data['current'] = 'realisasi';
        $data['apps']    = $this->apps_model->get_active_only();
        $this->load->view('realisasiview', $data);
    }
    
    public function lb()
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
        
        $kec_kd = (isset($_GET['kec_kd']) ? $_GET['kec_kd'] : $data['user_kec_kd']);
        $kel_kd = (isset($_GET['kel_kd']) ? $_GET['kel_kd'] : $data['user_kel_kd']);
        
        if (ENVIRONMENT == 'development') {
            $tahun    = '2012';
            $tglawal  = '30-08-2012';
            $tglakhir = '30-08-2012';
        } else {
            $tahun    = date('Y');
            $tglawal  = date('d-m-Y');
            $tglakhir = date('d-m-Y');
        }
        
        $tahun    = (isset($_GET['tahun']) && $_GET['tahun'] ? $_GET['tahun'] : $tahun);
        $tglawal  = (isset($_GET['tglawal']) && $_GET['tglawal'] ? $_GET['tglawal'] : $tglawal);
        $tglakhir = (isset($_GET['tglakhir']) && $_GET['tglakhir'] ? $_GET['tglakhir'] : $tglakhir);
        $buku     = (isset($_GET['buku']) ? $_GET['buku'] : '11');
        
        $data['buku']      = $buku;
        $data['tahun']     = $tahun;
        $data['tglawal']   = $tglawal;
        $data['tglakhir']  = $tglakhir;
        $data['kec_kd']    = $kec_kd;
        $data['kel_kd']    = $kel_kd;
        $data['kecamatan'] = $this->kec->getRecord(get_user_kec_kd());
        $data['kelurahan'] = $this->kel->getRecord($kec_kd, get_user_kel_kd());
        
        
        $data['pagetitle'] = 'OpenSIPKD';
        $data['title']     = 'Realisasi';
        
        $data['main_content'] = '';
        
        $data['data_source'] = active_module_url() . "loaddata/lb?tahun=$tahun&kec_kd=$kec_kd&kel_kd=$kel_kd";
        
        $data['current'] = 'realisasi';
        $data['apps']    = $this->apps_model->get_active_only();
        $this->load->view('lbview', $data);
    }
    
    public function kb()
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
        
        $kec_kd = (isset($_GET['kec_kd']) ? $_GET['kec_kd'] : $data['user_kec_kd']);
        $kel_kd = (isset($_GET['kel_kd']) ? $_GET['kel_kd'] : $data['user_kel_kd']);
        
        if (ENVIRONMENT == 'development') {
            $tahun    = '2012';
            $tglawal  = '30-08-2012';
            $tglakhir = '30-08-2012';
        } else {
            $tahun    = date('Y');
            $tglawal  = date('d-m-Y');
            $tglakhir = date('d-m-Y');
        }
        
        $tahun    = (isset($_GET['tahun']) && $_GET['tahun'] ? $_GET['tahun'] : $tahun);
        $tglawal  = (isset($_GET['tglawal']) && $_GET['tglawal'] ? $_GET['tglawal'] : $tglawal);
        $tglakhir = (isset($_GET['tglakhir']) && $_GET['tglakhir'] ? $_GET['tglakhir'] : $tglakhir);
        $buku     = (isset($_GET['buku']) ? $_GET['buku'] : '11');
        
        $data['buku']      = $buku;
        $data['tahun']     = $tahun;
        $data['tglawal']   = $tglawal;
        $data['tglakhir']  = $tglakhir;
        $data['kec_kd']    = $kec_kd;
        $data['kel_kd']    = $kel_kd;
        $data['kecamatan'] = $this->kec->getRecord(get_user_kec_kd());
        $data['kelurahan'] = $this->kel->getRecord($kec_kd, get_user_kel_kd());
        
        
        $data['pagetitle'] = 'OpenSIPKD';
        $data['title']     = 'Realisasi';
        
        $data['main_content'] = '';
        
        $data['data_source'] = active_module_url() . "loaddata/kb?tahun=$tahun&kec_kd=$kec_kd&kel_kd=$kel_kd";
        
        $data['current'] = 'realisasi';
        $data['apps']    = $this->apps_model->get_active_only();
        $this->load->view('kbview', $data);
    }
    
    
    public function piutang()
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
        
        $kec_kd = (isset($_GET['kec_kd']) ? $_GET['kec_kd'] : $data['user_kec_kd']);
        $kel_kd = (isset($_GET['kel_kd']) ? $_GET['kel_kd'] : $data['user_kel_kd']);
        if (ENVIRONMENT == 'development') {
            $tahun = '2012';
        } else {
            $tahun = date('Y');
        }
        $tahun  = (isset($_GET['tahun']) ? $_GET['tahun'] : $tahun);
        $tahun2 = (isset($_GET['tahun2']) ? $_GET['tahun2'] : $tahun);
        
        $buku = (isset($_GET['buku']) ? $_GET['buku'] : '11');
        
        $data['buku']   = $buku;
        $data['kec_kd'] = $kec_kd;
        $data['kel_kd'] = $kel_kd;
        
        $data['kecamatan'] = $this->kec->getRecord(get_user_kec_kd());
        $data['kelurahan'] = $this->kel->getRecord($kec_kd, get_user_kel_kd());
        
        $data['pagetitle'] = 'PBB-Monitoring';
        $data['title']     = 'Piutang';
        
        $data['main_content'] = '';
        
        
        $data['tahun']       = $tahun;
        $data['tahun2']      = $tahun2;
        $data['data_source'] = active_module_url() . "loaddata/piutang?tahun=$tahun&tahun2=$tahun2&buku=$buku&kec_kd=$kec_kd&kel_kd=$kel_kd";
        
        $data['current'] = 'piutang';
        $data['apps']    = $this->apps_model->get_active_only();
        $this->load->view('piutangview', $data);
        
    }
    
    public function op()
    {
        $this->load_auth();
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect(active_module_url());
        }
        
        //ob_start("ob_gzhandler");
        
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
        
        if ($nop_kd != 0 && strlen($nop) == 18) {
            
            $prop_kd = substr($nop, 0, 2);
            $kab_kd  = substr($nop, 2, 2);
            $kec_kd  = substr($nop, 4, 3);
            $kel_kd  = substr($nop, 7, 3);
            $blok_kd = substr($nop, 10, 3);
            $urut_no = substr($nop, 13, 4);
            $jns_kd  = substr($nop, 17, 1);
            
            /* Cek kode kecamatan */
            if (get_user_kec_kd() != '000') {
                if (get_user_kec_kd() != $kec_kd) {
                    $kec_kd = 999;
                }
            }
            
            /* Cek kode kelurahan */
            if (get_user_kel_kd() != '000') {
                if (get_user_kel_kd() != $kel_kd) {
                    $kel_kd = 999;
                }
            }
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
        
        $data['current'] = 'op';
        $data['apps']    = $this->apps_model->get_active_only();
        $this->load->view('opview', $data);
        
    }
    
    /**
     * Used for  : Mencari data subjek pajak berdasarkan keyword NOP
     */
    function search_subjek_pajak_by_nop()
    {
        $this->load->model('viewModel');
        $keyword = $_REQUEST['keyword'];
        $result  = $this->viewModel->list_subjek_pajak_by_nop($keyword);
        
        $arr = array();
        if (isset($result)) {
            foreach ($result as $key => $val) {
                $arr[$key] = array(
                    "nop" => $val['nop'],
                    "nm_wp" => $val['nm_wp'],
                    "alamat" => ($val['jalan_wp'] . ", RT/RW " . $val['rt_wp'] . "/" . $val['rw_wp'] . ", Kelurahan " . $val['kelurahan_wp'] . ", " . $val['kota_wp'])
                );
            }
        }
        echo json_encode($arr);
    }
    
    /**
     * Used for  : Mencari data subjek pajak berdasarkan keyword NOP
     */
    function search_subjek_pajak_by_nama()
    {
        $this->load->model('viewModel');
        $keyword = $_REQUEST['keyword'];
        $result  = $this->viewModel->list_subjek_pajak_by_nama(strtoupper($keyword));
        
        $arr = array();
        if (isset($result)) {
            foreach ($result as $key => $val) {
                $arr[$key] = array(
                    "nop" => $val['nop'],
                    "nm_wp" => $val['nm_wp'],
                    "alamat" => ($val['jalan_wp'] . ", RT/RW " . $val['rt_wp'] . "/" . $val['rw_wp'] . ", Kelurahan " . $val['kelurahan_wp'] . ", " . $val['kota_wp'])
                );
            }
        }
        echo json_encode($arr);
    }
    /* memanggil report */
    public function reports1()
    {
        $jns = $this->uri->segment(4);
        $id  = $this->uri->segment(5) ? $this->uri->segment(5) : 0;
        if (($jns && ($jns == 1 || $jns == 2)) && ($get = $this->sspd_model->get($id))) {
            $where = ' where bphtb_sspd.id=' . $id;
            if ($jns == 1) { // sspd formated
                $data['rpt_file'] = 'bphtb_sspd_formated';
            } else { // sspd ploted
                $data['rpt_file'] = 'bphtb_sspd_plotted';
            }
            $terbilang                  = terbilang($get->bphtb_harus_dibayarkan, 3) . " Rupiah";
            $data['parameters']         = (object) array(
                'kondisi' => $where,
                'terbilang' => $terbilang
            );
            $data['id']                 = $id;
            $data['model']              = $this->sspd_model;
            $data['pdf']                = 'pdf';
            $data['update_print_state'] = true;
            $data['update_field']       = 'tgl_print';
            $data['table']              = 'bphtb_sppd';
            $this->load->view('vreports', $data);
        } else {
            show_404();
        }
    }
    
    public function reports()
    {
        $jns = $this->uri->segment(4);
        // $id = $this->uri->segment(5)?$this->uri->segment(5):0;
        if ($jns == 11) {
            
        }
        /*if (($jns && ($jns==1 || $jns==2)) && ($get = $this->sspd_model->get($id))) {
        $where = ' where bphtb_sspd.id=' . $id;
        if ($jns==1) {                              // sspd formated
        $data['rpt_file'] = 'bphtb_sspd_formated';
        } else {                                    // sspd ploted
        $data['rpt_file'] = 'bphtb_sspd_plotted';
        }
        $terbilang = terbilang($get->bphtb_harus_dibayarkan, 3) . " Rupiah";
        $data['parameters'] = (object) array('kondisi' => $where, 'terbilang' => $terbilang);
        $data['id'] = $id;
        $data['model'] = $this->sspd_model;
        $data['pdf'] = 'pdf';
        $data['update_print_state'] = true;
        $data['update_field'] = 'tgl_print';
        $data['table'] = 'bphtb_sppd';
        $this->load->view('vreports', $data);
        }*/
        else {
            show_404();
        }
    }
    
}