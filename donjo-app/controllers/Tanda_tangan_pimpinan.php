<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/*
 *  File ini:
 *
 * Controller untuk modul Surat Keluar
 *
 * donjo-app/controllers/Keluar.php
 *
 */
/*
 *  File ini bagian dari:
 *
 * OpenSID
 *
 * Sistem informasi desa sumber terbuka untuk memajukan desa
 *
 * Aplikasi dan source code ini dirilis berdasarkan lisensi GPL V3
 *
 * Hak Cipta 2009 - 2015 Combine Resource Institution (http://lumbungkomunitas.net/)
 * Hak Cipta 2016 - 2020 Perkumpulan Desa Digital Terbuka (https://opendesa.id)
 *
 * Dengan ini diberikan izin, secara gratis, kepada siapa pun yang mendapatkan salinan
 * dari perangkat lunak ini dan file dokumentasi terkait ("Aplikasi Ini"), untuk diperlakukan
 * tanpa batasan, termasuk hak untuk menggunakan, menyalin, mengubah dan/atau mendistribusikan,
 * asal tunduk pada syarat berikut:
 *
 * Pemberitahuan hak cipta di atas dan pemberitahuan izin ini harus disertakan dalam
 * setiap salinan atau bagian penting Aplikasi Ini. Barang siapa yang menghapus atau menghilangkan
 * pemberitahuan ini melanggar ketentuan lisensi Aplikasi Ini.
 *
 * PERANGKAT LUNAK INI DISEDIAKAN "SEBAGAIMANA ADANYA", TANPA JAMINAN APA PUN, BAIK TERSURAT MAUPUN
 * TERSIRAT. PENULIS ATAU PEMEGANG HAK CIPTA SAMA SEKALI TIDAK BERTANGGUNG JAWAB ATAS KLAIM, KERUSAKAN ATAU
 * KEWAJIBAN APAPUN ATAS PENGGUNAAN ATAU LAINNYA TERKAIT APLIKASI INI.
 *
 * @package	OpenSID
 * @author	Tim Pengembang OpenDesa
 * @copyright	Hak Cipta 2009 - 2015 Combine Resource Institution (http://lumbungkomunitas.net/)
 * @copyright	Hak Cipta 2016 - 2020 Perkumpulan Desa Digital Terbuka (https://opendesa.id)
 * @license	http://www.gnu.org/licenses/gpl.html	GPL V3
 * @link 	https://github.com/OpenSID/OpenSID
 */

class Tanda_tangan_pimpinan extends Admin_Controller {


	public function __construct()
	{
		parent::__construct();
		$this->load->model('tanda_tangan_model');
		$this->load->model('permohonan_surat_model');
        $this->load->helper('download');
		$this->load->model('pamong_model');
		$this->load->model('config_model');
        $this->load->model('mailbox_model');

		$this->modul_ini = 4;
		$this->sub_modul_ini = 135;
		$this->load->library(array('bsrelib','pdf','pdfpdi','ciqrcode'));
	}

	public function clear()
	{
		unset($_SESSION['cari']);
		unset($_SESSION['filter']);
		unset($_SESSION['jenis']);
		$_SESSION['per_page'] = 20;
		redirect('tanda_tangan');
	}

	public function index($p=1, $o=0)
	{
		$data['p'] = $p;
		$data['o'] = $o;

		if (isset($_SESSION['cari']))
			$data['cari'] = $_SESSION['cari'];
		else $data['cari'] = '';

		if (isset($_SESSION['filter']))
			$data['filter'] = $_SESSION['filter'];
		else $data['filter'] = '';

		if (isset($_SESSION['jenis']))
			$data['jenis'] = $_SESSION['jenis'];
		else $data['jenis'] = '';

		if (isset($_POST['per_page']))
			$_SESSION['per_page'] = $_POST['per_page'];
		$data['per_page'] = $_SESSION['per_page'];

		$data['paging'] = $this->tanda_tangan_model->paging($p,$o);
		$data['main'] = $this->tanda_tangan_model->list_data($o, $data['paging']->offset, $data['paging']->per_page);
		//die($this->db->last_query());
		$data['tahun_surat'] = $this->tanda_tangan_model->list_tahun_surat();
		$data['jenis_surat'] = $this->tanda_tangan_model->list_jenis_surat();
		$data['keyword'] = $this->tanda_tangan_model->autocomplete();

		$this->render('tanda_tangan_pimpinan/tanda_tangan', $data);
	}

    public function jenis()
    {
        $jenis = $this->input->post('jenis');
        if (!empty($jenis))
            $_SESSION['jenis'] = $jenis;
        else unset($_SESSION['jenis']);
        redirect('tanda_tangan_pimpinan');
    }

    public function dialog_cetak($aksi = '')
    {
        $data['aksi'] = $aksi;
        $data['pamong'] = $this->pamong_model->list_data();
        $data['form_action'] = site_url("tanda_tangan_pimpinan/cetak/$aksi");
        $this->load->view('global/ttd_pamong', $data);
    }

    public function cetak($aksi = '')
    {
        $data['aksi'] = $aksi;
        $data['input'] = $this->input->post();
        $data['config'] = $this->header['desa'];
        $data['pamong_ttd'] = $this->pamong_model->get_data($_POST['pamong_ttd']);
        $data['pamong_ketahui'] = $this->pamong_model->get_data($_POST['pamong_ketahui']);
        $data['desa'] = $this->config_model->get_data();
        $data['main'] = $this->keluar_model->list_data();

        //pengaturan data untuk format cetak/ unduh
        $data['file'] = "Data Tanda Tangan Surat  Desa ";
        $data['isi'] = "surat/cetak_tanda_tangan";
        $data['letak_ttd'] = ['2', '2', '3'];

        $this->load->view('global/format_cetak', $data);

    }

    public function graph()
    {
        $data['stat'] = $this->tanda_tangan_model->grafik();

        $this->render('tanda_tangan_pimpinan/tanda_tangan_graph', $data);
    }

	public function unduh_lampiran($id)
	{
		$berkas = $this->db->select('lampiran')->where('id', $id)->get('tanda_tangan_surat')->row();
		ambilBerkas($berkas->lampiran, 'tanda_tangan_pimpinan');
	}

    public function form($p = 1, $o = 0, $id = '')
    {
        $data['p'] = $p;
        $data['o'] = $o;
        $data['log_surat'] = $this->tanda_tangan_model->get_log_surat_tte();
        $data['form_action'] = base_url().'tanda_tangan/insert';
        $this->set_minsidebar(1);
        $this->render('tanda_tangan_pimpinan/form', $data);
    }

    public function edit_keterangan($id=0)
    {
        $data['data'] = $this->tanda_tangan_model->list_data_keterangan($id);
        $data['form_action'] = site_url("tanda_tangan_pimpinan/update_keterangan/$id");
        $this->load->view('surat/ajax_edit_keterangan', $data);
    }

    public function tanda_tangan($id=0)
    {

        $data['form_action'] = site_url("tanda_tangan_pimpinan/esign/$id");
        $this->load->view('tanda_tangan_pimpinan/form_esign', $data);
    }

    public function esign($id=0)
    {

        //java -jar esign-cli.jar -m sign -f dokumen.pdf -p 12345678 -nik {nik} -t visible -i TRUE -v {path_to_image_jpg} -page 1 -height 130 -width 550 -x 43 -y 28
        //param from form html
        $ttd  = $this->tanda_tangan_model->get_data_surat($id);
        $nik = $this->input->post('nik');
        $halaman = $this->input->post('halaman', TRUE);
        $passprhase = $this->input->post('passprhase');

        //create data qr code
        $qr_image =$ttd['id'].'-'.$ttd['bulan'] . '_'.$ttd['tahun'] . '_'  . str_replace('/', '-', $ttd['nama_surat']) .$ttd['nomor_surat']. '.png';
        //print_r($qr_image);
        //$params['data'] = 'Dari: ' . $nm_instansi . ' -- ' . 'No. Agenda: ' . $no_agenda . ' -- ' . 'Tgl: ' . tgl_login($create_date);
        $params['data'] = $ttd['id'].' - '.$ttd['tanggal'].' - '.$ttd['nama_surat'].' - '.$ttd['nomor_surat'];
        $params['level'] = 'H';
        $params['size'] = 8;
        $params['savename'] = FCPATH . LOKASI_TTE_QR . $qr_image;
        $this->ciqrcode->generate($params);

        //setting param bSrE
        //116, 255, 13, 30
        $this->bsrelib->setAppearance(
            $x = 43,
            $y = 28,
            $width = 570,
            $height = 130,
            $page = 1,
            $spesimen = FCPATH.LOKASI_TTE_SPESIMEN.$ttd[file_spesimen],
            $qr = null);

        //Update PDF
        $pdf = new PDFPDI( FCPATH.LOKASI_TTE_MAIL.$ttd['file_surat']);
        $pdf->numPages = $pdf->setSourceFile(FCPATH.LOKASI_TTE_MAIL.$ttd['file_surat']);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetAutoPageBreak(TRUE, 40);
        $pdf->setFontSubsetting(FALSE);
        $pdf->AddPage('P', 'F4', TRUE, FALSE);


        //print_r($result);
        //die($this->db->last_query());
        //die();
        if($ttd['id_format_surat']==15){
            $this->surat_ket_usaha($pdf, $halaman, $qr_image, $ttd);
        }else if($ttd['id_format_surat']==15){

        }else if($ttd['id_format_surat']==15){

        }else if($ttd['id_format_surat']==15){

        }else if($ttd['id_format_surat']==15){

        }else if($ttd['id_format_surat']==15){

        }

        $result = $this->bsrelib->sign($nik,$passprhase);
        if($result==1){
            //jika berhasil,update table tanda tangan
            $data=array('status'=> 1,'file_signed'=>substr($ttd[file_surat], 0, strpos($ttd[file_surat], ".") - 1) .'_signed'. '.pdf');
            $this->tanda_tangan_model->update($ttd['id'],$data);
            $data=array('tte'=> 'Y');
            $this->tanda_tangan_model->update_log_status($ttd['id_log'],$data);
            //status surat jika ada di mandiri - belum
            $result = $this->tanda_tangan_model->cekPermohonan($ttd);
            if(!empty($result)){
                //update status surat mandiri
                $this->permohonan_surat_model->update($result->id,array("status"=>"3"));
                //insert pesan ke masyarakat
                $post['email'] = $ttd['nik_pd']; // kolom email diisi nik untuk pesan
                $post['owner'] = $ttd['nama_pd'];
                $post['tipe'] = 1;
                $post['status'] = 2;
                $post['id_artikel'] = 775;
                $link = base_url("/desa/tte/signed/substr($ttd[file_surat], 0, strpos($ttd[file_surat], ".") - 1) .'_signed'. '.pdf')".'_signed'. '.pdf');
                $post['komentar'] = "Surat bapak/ibuk sudah di tanda tangani, Silahkan download di link berikut $link";
                $this->mailbox_model->insert($post);
            }
        }
        redirect("tanda_tangan_pimpinan");

    }

    /**
     * @param PDFPDI $pdf
     * @param $halaman
     * @param $qr_image
     * @param $ttd
     */
    public function surat_ket_usaha(PDFPDI $pdf, $halaman, $qr_image, $ttd)
    {
        if ($pdf->numPages > 1) {

            for ($i = 1; $i <= $pdf->numPages; $i++) {

                $pdf->_tplIdx = $pdf->importPage($i);

                if ($i > 1) {
                    $pdf->AddPage();
                }

                if ($i == $halaman) {

                    //QR Code
                    $pdf->Image(FCPATH . LOKASI_TTE_QR . $qr_image, 15, 245, 20, 20, 'PNG');
                    $pdf->SetFont('Helvetica', '', 8);
                    $pdf->MultiCell(80, 21, strtoupper( $this->config_model->get_data()['nama_desa']).', '.tgl_indo(date('Y-m-d')), 0, 'C', 0, 0, 114, 196, true);
                    // set fonts
                    $pdf->MultiCell(85, 26, '', 1, 'L', 0, 0, 115, 200, true);
                    $pdf->Image(FCPATH . LOKASI_TTE_SPESIMEN . 'solokkab.png', 116, 201, 18, 25, 'PNG');
                    $pdf->SetFont('Helvetica', '', 8);
                    $pdf->SetXY(134, 202); // position of text1, numerical, of course, not x1 and y1
                    $pdf->Write(0, 'Ditanda Tangani Secara Elektronik Oleh :');
                    $pdf->SetFont('Helvetica', 'B', 7);
                    $pdf->MultiCell(80, 21, strtoupper($ttd['jabatan'] . ' ' . $this->config_model->get_data()['nama_desa']), 0, 'C', 0, 0, 120, 206, true);
                    //Gambar Ttd
                    //$pdf->Image(FCPATH . LOKASI_TTE_SPESIMEN . $ttd['file_spesimen'], 132, 205, 70, 45, 'PNG');
                    $pdf->SetFont('Helvetica', 'BU', 7);
                    $pdf->MultiCell(80, 21, strtoupper($ttd['pamong_nama']), 0, 'C', 0, 0, 120, 219, true);

                    $pdf->SetFont('Helvetica', '', 7);
                    $pdf->MultiCell(80, 21, strtoupper($ttd['pamong_nik']), 0, 'C', 0, 0, 120, 222, true);

                    //Gambar BSrE
                    $pdf->Image(FCPATH . LOKASI_TTE_QR . 'BSrE.png', 180, 218, 16, 6, 'PNG');
                    $pdf->Line(15, 267, 200, 267);
                    $pdf->Ln();
                    //$pdf->SetY(-15);
                    $pdf->SetFont('Helvetica', '', 8);
                    $pdf->MultiCell(80, 12, "Catatan : ", 0, 'L', 0, 0, 15, 267, true);
                    $pdf->SetFont('Helvetica', '', 6);
                    $pdf->MultiCell(200, 12, "1. UU Nomor 11 Tahun 2008 Pasal 5 Ayat 1 : \"Informasi Elektronik dan/atau Dokumen Elektronik dan/atau hasil cetaknya merupakan alat bukti hukum yang sah\" ", 0, 'L', 0, 0, 15, 271, true);
                    $pdf->MultiCell(200, 12, "2. Dokumen ini telah di tanda Tangani Secara Elektronik Menggunakan Sertifikat Elektronik yang diterbitkan BSrE ", 0, 'L', 0, 0, 15, 274, true);
                    $pdf->MultiCell(200, 12, "3. Surat Ini dapat dibuktikan Keasliannya terdapat di http://cupak-slk.desa.id atau Scan QRCod ", 0, 'L', 0, 0, 15, 277, true);

                }
            }
        } else {

            //QR Code
            $pdf->Image(FCPATH . LOKASI_TTE_QR . $qr_image, 15, 245, 20, 20, 'PNG');
            $pdf->SetFont('Helvetica', '', 8);
            $pdf->MultiCell(80, 21, strtoupper( $this->config_model->get_data()['nama_desa']).', '.tgl_indo(date('Y-m-d')), 0, 'C', 0, 0, 114, 196, true);
            // set fonts
            $pdf->MultiCell(85, 26, '', 1, 'L', 0, 0, 115, 200, true);
            $pdf->Image(FCPATH . LOKASI_TTE_SPESIMEN . 'solokkab.png', 116, 201, 18, 25, 'PNG');
            $pdf->SetFont('Helvetica', '', 8);
            $pdf->SetXY(134, 202); // position of text1, numerical, of course, not x1 and y1
            $pdf->Write(0, 'Ditanda Tangani Secara Elektronik Oleh :');
            $pdf->SetFont('Helvetica', 'B', 7);
            $pdf->MultiCell(80, 21, strtoupper($ttd['jabatan'] . ' ' . $this->config_model->get_data()['nama_desa']), 0, 'C', 0, 0, 120, 206, true);
            //Gambar Ttd
            //$pdf->Image(FCPATH . LOKASI_TTE_SPESIMEN . $ttd['file_spesimen'], 132, 205, 70, 45, 'PNG');
            $pdf->SetFont('Helvetica', 'BU', 7);
            $pdf->MultiCell(80, 21, strtoupper($ttd['pamong_nama']), 0, 'C', 0, 0, 120, 219, true);

            $pdf->SetFont('Helvetica', '', 7);
            $pdf->MultiCell(80, 21, strtoupper($ttd['pamong_nik']), 0, 'C', 0, 0, 120, 222, true);

            //Gambar BSrE
            $pdf->Image(FCPATH . LOKASI_TTE_QR . 'BSrE.png', 180, 218, 16, 6, 'PNG');
            $pdf->Line(15, 267, 200, 267);
            $pdf->Ln();
            //$pdf->SetY(-15);
            $pdf->SetFont('Helvetica', '', 8);
            $pdf->MultiCell(80, 12, "Catatan : ", 0, 'L', 0, 0, 15, 267, true);
            $pdf->SetFont('Helvetica', '', 6);
            $pdf->MultiCell(200, 12, "1. UU Nomor 11 Tahun 2008 Pasal 5 Ayat 1 : \"Informasi Elektronik dan/atau Dokumen Elektronik dan/atau hasil cetaknya merupakan alat bukti hukum yang sah\" ", 0, 'L', 0, 0, 15, 271, true);
            $pdf->MultiCell(200, 12, "2. Dokumen ini telah di tanda Tangani Secara Elektronik Menggunakan Sertifikat Elektronik yang diterbitkan BSrE ", 0, 'L', 0, 0, 15, 274, true);
            $pdf->MultiCell(200, 12, "3. Surat Ini dapat dibuktikan Keasliannya terdapat di http://cupak-slk.desa.id atau Scan QRCod ", 0, 'L', 0, 0, 15, 277, true);


        }

        $pdf->Output(FCPATH . LOKASI_TTE_LETTER . substr($ttd[file_surat], 0, strpos($ttd[file_surat], ".") - 1)  . '.pdf', 'F');
        $pdf->Close();
        $this->bsrelib->setDocument(FCPATH . LOKASI_TTE_LETTER . substr($ttd[file_surat], 0, strpos($ttd[file_surat], ".") - 1) . '.pdf');
        $this->bsrelib->setDirOutput(LOKASI_TTE_SIGNED,false);
    }

    private function footer(){

   }

    public function update_keterangan($id='')
    {
        $data = array('keterangan' => $this->input->post('keterangan'));
        $data = $this->security->xss_clean($data);
        $data = html_escape($data);
        $this->tanda_tangan_model->update_keterangan($id, $data);
        redirect($_SERVER['HTTP_REFERER']);
    }



    public function delete($p=1, $o=0, $id='')
    {
        $this->redirect_hak_akses('h', "tanda_tangan_pimpinan/index/$p/$o");
        session_error_clear();
        $this->tanda_tangan_model->delete($id);
        redirect("tanda_tangan_pimpinan/index/$p/$o");
    }

    public function insert()
    {
        $this->tanda_tangan_model->insert();
        redirect("tanda_tangan_pimpinan");
    }



}
