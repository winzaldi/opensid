<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once dirname(__FILE__) . '/tcpdf/tcpdf.php';

class Pdf extends TCPDF
{
	public function __construct()
	{
		parent::__construct();
	}

}

/* End of file Pdf.php */
/* Location: ./application/libraries/Pdf.php */