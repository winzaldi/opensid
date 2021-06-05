<?php class Tanda_tangan_model extends CI_Model
{
    // Konfigurasi untuk library 'upload'
    protected $uploadConfig = array();


    public function __construct()
    {
        parent::__construct();
        // Untuk dapat menggunakan library upload
        $this->load->library('upload');
        $this->uploadConfig = array(
            'upload_path' => LOKASI_TTE_MAIL,
            'allowed_types' => 'PDF|pdf',
            'max_size' => max_upload()*1024,
        );
    }

    public function paging($p=1, $o=0)
    {
        $sql = "SELECT COUNT(*) AS jml " . $this->list_data_sql();
        $query = $this->db->query($sql);
        $row = $query->row_array();
        $jml_data = $row['jml'];

        $this->load->library('paging');
        $cfg['page'] = $p;
        $cfg['per_page'] = $_SESSION['per_page'];
        $cfg['num_rows'] = $jml_data;
        $this->paging->init($cfg);

        return $this->paging;
    }

    private function list_data_sql()
    {
        $sql = " FROM tanda_tangan_surat u
			LEFT JOIN tweb_penduduk n ON u.id_pend = n.id
			LEFT JOIN tweb_surat_format k ON u.id_format_surat = k.id
			LEFT JOIN tweb_desa_pamong s ON u.id_pamong = s.pamong_id
			LEFT JOIN tweb_penduduk p ON s.id_pend = p.id
			LEFT JOIN user w ON u.id_user = w.id
			WHERE 1 ";
        $sql .= $this->search_sql();
        $sql .= $this->filter_sql();
        $sql .= $this->jenis_sql();
        return $sql;
    }

    private function search_sql()
    {
        if (isset($_SESSION['cari']))
        {
            $cari = $_SESSION['cari'];
            $kw = $this->db->escape_like_str($cari);
            $kw = '%' .$kw. '%';
            $search_sql = " AND (u.no_surat LIKE '$kw' OR n.nama LIKE '$kw' OR
					s.pamong_nama like '$kw' OR p.nama like '$kw')";
            return $search_sql;
        }
    }

    private function filter_sql()
    {
        if (isset($_SESSION['filter']))
        {
            $kf = $_SESSION['filter'];
            if ($kf == "0")
                $filter_sql = "";
            else
                $filter_sql = " AND YEAR(u.tanggal) = '".$kf."'";
            return $filter_sql;
        }
    }

    private function jenis_sql()
    {
        if (isset($_SESSION['jenis']))
        {
            $kf = $_SESSION['jenis'];
            if (empty($kf))
                $sql = "";
            else
                $sql = " AND k.nama = '".$kf."'";
            return $sql;
        }
    }

    public function list_tahun_surat()
    {
        $query = $this->db->distinct()->
        select('YEAR(tanggal) AS tahun')->
        order_by('YEAR(tanggal)','DESC')->
        get('tanda_tangan_surat')->result_array();
        return $query;
    }

    public function list_jenis_surat()
    {
        $query = $this->db->distinct()->
        select('k.nama as nama_surat')->
        from('tanda_tangan_surat u')->
        join('tweb_surat_format k', 'u.id_format_surat = k.id', 'left')->
        order_by('nama_surat')->
        get()->result_array();
        return $query;
    }

    public function autocomplete()
    {
        $sql = array();
        $sql[] = '('.$this->db->select('no_surat')
                ->from("tanda_tangan_surat")
                ->get_compiled_select()
            .')';
        $sql[] = '('.$this->db->select('n.nama')
                ->from("tanda_tangan_surat u")
                ->join("tweb_penduduk n", "u.id_pend = n.id", "left")
                ->get_compiled_select()
            .')';
        $sql[] = '('.$this->db->select('p.nama')
                ->from("tanda_tangan_surat u")
                ->join("tweb_desa_pamong s", "u.id_pamong = s.pamong_id", "left")
                ->join("tweb_penduduk p", "s.id_pend = p.id", "left")
                ->get_compiled_select()
            .')';
        $sql = implode('
		UNION
		', $sql);
        $data = $this->db->query($sql)->result_array();
        $str = autocomplete_data_ke_str($data);
        return $str;
    }

    public function list_data($o=0, $offset=0, $limit=0)
    {
        //Ordering SQL
        switch ($o)
        {
            case 1: $order_sql = ' ORDER BY u.no_surat * 1'; break;
            case 2: $order_sql = ' ORDER BY u.no_surat * 1 DESC'; break;
            case 3: $order_sql = ' ORDER BY nama'; break;
            case 4: $order_sql = ' ORDER BY nama DESC'; break;
            case 5: $order_sql = ' ORDER BY u.tanggal'; break;
            case 6: $order_sql = ' ORDER BY u.tanggal DESC'; break;

            default:$order_sql = ' ORDER BY u.tanggal DESC';
        }

        //Paging SQL
        $paging_sql = ($limit > 0 ) ? ' LIMIT ' .$offset. ',' .$limit : '';

        //Main Query
        $select_sql = "SELECT u.*, n.nama AS nama, w.nama AS nama_user, n.nik AS nik, k.nama AS format, k.url_surat as berkas, k.kode_surat as kode_surat, s.id_pend as pamong_id_pend, s.pamong_nama AS pamong, p.nama as nama_pamong_desa ";

        $sql = $select_sql . $this->list_data_sql();
        $sql .= $order_sql;
        $sql .= $paging_sql;

        $query = $this->db->query($sql);
        $data = $query->result_array();

        //Formating Output
        $j = $offset;
        for ($i=0; $i<count($data); $i++)
        {
            $data[$i]['no'] = $j+1;
            $data[$i]['t'] = $data[$i]['id_pend'];

            if ($data[$i]['id_pend'] == -1)
                $data[$i]['id_pend'] = "Masuk";
            else
                $data[$i]['id_pend'] = "Keluar";
            if (!empty($data[$i]['pamong_id_pend']))
                // Pamong desa
                $data[$i]['pamong'] = $data[$i]['nama_pamong_desa'];

            $j++;
        }
        return $data;
    }

    public function grafik()
    {
        $data = $this->db
            ->select('f.nama, COUNT(l.id) as jumlah')
            ->from('tanda_tangan_surat l')
            ->join('tweb_surat_format f', 'l.id_format_surat=f.id', 'left')
            ->group_by('f.nama')
            ->get()
            ->result_array();
        return $data;
    }

    public function update_keterangan($id, $data)
    {
        $this->db->where('id', $id);
        $outp = $this->db->update('tanda_tangan_surat', $data);

        status_sukses($outp); //Tampilkan Pesan
    }

    public function list_data_keterangan($id)
    {
        $this->db->select('id, keterangan');
        $this->db->from('tanda_tangan_surat');
        $this->db->where('id', $id);

        return $this->db->get()->row_array();
    }

    public function delete($id='')
    {
        $_SESSION['success'] = 1;
        $_SESSION['error_msg'] = '';
        $arsip = $this->db->select('nama_surat, lampiran')->
        where('id', $id)->
        get('tanda_tangan_surat')->
        row_array();
        $berkas_surat = pathinfo($arsip['nama_surat'], PATHINFO_FILENAME);
        unlink(LOKASI_TTE . $berkas_surat . ".rtf");
        unlink(LOKASI_TTE . $berkas_surat . ".pdf");

        if (!empty($arsip['lampiran']))
            unlink(LOKASI_ARSIP . $arsip['lampiran']);

        if (!$this->db->where('id', $id)->delete('tanda_tangan_surat')) {    // Jika query delete terjadi error
            $_SESSION['success'] = -1;                                // Maka, nilai success jadi -1, untuk memunculkan notifikasi error
            $error = $this->db->error();
            $_SESSION['error_msg'] = $error['message']; // Pesan error ditampung disession
        }
    }

        public function get_log_surat_tte()
    {
        $data = $this->db
            ->query("select l.*,k.nama as jenis_surat, s.pamong_nama as nama_pamong,
                     p.nama as nama_pend,p.nik, k.kode_surat 
                        FROM  log_surat l 
                        LEFT JOIN tweb_surat_format k ON l.id_format_surat = k.id 
                        LEFT JOIN tweb_desa_pamong s ON l.id_pamong = s.pamong_id 
                        LEFT JOIN tweb_penduduk p ON l.id_pend = p.id 
                        WHERE l.tte ='N'")
            ->result_array();
        return $data;
    }

    public function  insert(){
        $data = $this->input->post(NULL);
        $idlog_surat = $data['idlog_surat'];
        // Adakah lampiran yang disertakan?
        $adaLampiran = !empty($_FILES['dokumen']['name']);
        // Cek nama berkas user boleh lebih dari 80 karakter (+20 untuk unique id) karena -
        // karakter maksimal yang bisa ditampung kolom surat_masuk.berkas_scan hanya 100 karakter
        if ($adaLampiran && ((strlen($_FILES['satuan']['name']) + 20 ) >= 100))
        {
            $_SESSION['success'] = -1;
            $_SESSION['error_msg'] = ' -> Nama berkas yang coba Anda unggah terlalu panjang, '.
                'batas maksimal yang diijinkan adalah 80 karakter';
            redirect('surat_masuk');
        }

        $uploadData = NULL;
        $uploadError = NULL;
        // Ada lampiran file
        if ($adaLampiran === TRUE)
        {
            // Tes tidak berisi script PHP
            if (isPHP($_FILES['foto']['tmp_name'], $_FILES['foto']['name']))
            {
                $_SESSION['error_msg'] .= " -> Jenis file ini tidak diperbolehkan ";
                $_SESSION['success'] = -1;
                redirect('man_user');
            }
            // Inisialisasi library 'upload'
            $this->upload->initialize($this->uploadConfig);
            // Upload sukses
            if ($this->upload->do_upload('dokumen'))
            {
                $uploadData = $this->upload->data();
                // Buat nama file unik agar url file susah ditebak dari browser
                $namaFileUnik = tambahSuffixUniqueKeNamaFile($uploadData['file_name']);
                // Ganti nama file asli dengan nama unik untuk mencegah akses langsung dari browser
                $fileRenamed = rename(
                    $this->uploadConfig['upload_path'].$uploadData['file_name'],
                    $this->uploadConfig['upload_path'].$namaFileUnik
                );
                // Ganti nama di array upload jika file berhasil di-rename --
                // jika rename gagal, fallback ke nama asli
                $uploadData['file_name'] = $fileRenamed ? $namaFileUnik : $uploadData['file_name'];
            }
            // Upload gagal
            else
            {
                $uploadError = $this->upload->display_errors(NULL, NULL);
            }
        }
        // penerapan transcation karena insert ke 2 tabel
        $this->db->trans_start();

        $indikatorSukses = is_null($uploadError)
            && $this->db->query("insert into tanda_tangan_surat(`id_format_surat`, `id_pend`, `id_pamong`, `id_user`, `tanggal`, 
                `bulan`, `tahun`, `no_surat`, `nama_surat`, `lampiran`, `nik_non_warga`, `nama_non_warga`, 
                `keterangan`,status,file_surat)
                select `id_format_surat`, `id_pend`, `id_pamong`, `id_user`, `tanggal`, `bulan`, `tahun`, `no_surat`, 
                   `nama_surat`, `lampiran`, `nik_non_warga`, `nama_non_warga`, `keterangan`,'0','$namaFileUnik' 
                   from log_surat  where id = $idlog_surat ");

        // transaction selesai
        $this->db->trans_complete();

        // Set session berdasarkan hasil operasi
        $_SESSION['success'] = $indikatorSukses ? 1 : -1;
        $_SESSION['error_msg'] = $_SESSION['success'] === 1 ? NULL : ' -> '.$uploadError;

    }

    public function get_data_surat($id=0)
    {
        $sql = "SELECT u.*,p.pamong_nama,p.pamong_nik,p.jabatan FROM tanda_tangan_surat u 
	            LEFT JOIN tweb_desa_pamong p on u.id_pamong = p.pamong_id WHERE id = ?";
        $query = $this->db->query($sql,$id);
        $data = $query->row_array();
        return $data;
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('tanda_tangan_surat', $data);
    }



}

