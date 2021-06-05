<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once dirname(__FILE__) . '/tcpdf/tcpdf.php';
require_once dirname(__FILE__) . '/fpdi/fpdi.php';

class PDFPDI extends FPDI
{	
	var $_tplIdx;
    var $_file;

    public function __construct($file='')
    {
        parent::__construct();
        $this->_file = $file;
    }

    public function Header() {
        if(is_null($this->_tplIdx)){
            $this->setSourceFile($this->_file);
            $this->_tplIdx = $this->importPage(1);
        }
        
        $size = $this->useTemplate($this->_tplIdx);
        $this->SetXY(PDF_MARGIN_LEFT, 5);
        //$this->Cell(0, $size['h'], 'TCPDF and FPDI');
    }
    
    public function Footer() {
        
    }
}

/* End of file Pdf.php */
/* Location: ./application/libraries/Pdf.php */