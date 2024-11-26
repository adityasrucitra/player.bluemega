<?php
namespace App\Libraries;

use Fpdf\Fpdf;

class PDF_MemImage_2 extends Fpdf
{
    public function __construct($orientation = 'P', $unit = 'mm', $size = 'A4', $options = [])
    {
        parent::__construct($orientation, $unit, $size);
        $this->options = $options;
        // Register var stream protocol
        $existed = in_array('var', stream_get_wrappers());
        if ($existed) {
            stream_wrapper_unregister('var');
        }
        stream_wrapper_register('var', 'App\Libraries\VariableStream');
    }

    public function MemImage($data, $x = null, $y = null, $w = 0, $h = 0, $link = '')
    {
        // Display the image contained in $data
        $v = 'img'.md5($data);
        $GLOBALS[$v] = $data;
        $a = getimagesize('var://'.$v);
        if (!$a) {
            $this->Error('Invalid image data');
        }
        $type = substr(strstr($a['mime'], '/'), 1);
        $this->Image('var://'.$v, $x, $y, $w, $h, $type, $link);
        unset($GLOBALS[$v]);
    }

    public function GDImage($im, $x = null, $y = null, $w = 0, $h = 0, $link = '')
    {
        // Display the GD image associated with $im
        ob_start();
        imagepng($im);
        $data = ob_get_clean();
        $this->MemImage($data, $x, $y, $w, $h, $link);
    }

    public function Header()
    {
        $strImg = \file_get_contents(base_url('asset/img/imt-logo.png'));
        $strImg2 = \file_get_contents(base_url('asset/img/bm-logo.png'));

        $this->MemImage($strImg, $this->GetX(), $this->GetY(), 50);
        $this->MemImage($strImg2, $this->GetPageWidth() - 30, $this->GetY(), 20);
        // Select Arial bold 15
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 15);
        // $this->Cell(80);
    }

    public function Footer()
    {
        // Go to 1.5 cm from bottom
        $this->SetY(-15);
        // Select Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Print centered page number
        // $this->Cell(0, 10, 'Page '.$this->PageNo(), 0, 0, 'C');
    }
}
