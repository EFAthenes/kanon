<?php
/**
 * Description of KMakePdf
 *
 * @author Mulot Louis
 */
//require_once(dirname(__FILE__) . '/tcpdf/config/lang/eng.php');
require_once(dirname(__FILE__) . '/tcpdf/tcpdf.php');

class KMakePdf extends TCPDF
{
    private $logo_header_path_right="";
    private $logo_header_path_left="";
    private $link_right="";
    private $link_left="";
    public function __destruct()
    {     
    }
    
    public function setImageHeader($logo_header_path_right="",$logo_header_path_left="",$link_right="",$link_left="")
    {
        $this->logo_header_path_right=$logo_header_path_right;
        $this->logo_header_path_left=$logo_header_path_left;
        $this->link_right=$link_right;
        $this->link_left=$link_left;
    }
    
    public function Header()
    {
        if($this->logo_header_path_right!=""&&$this->logo_header_path_left!="")
        {
            // Logo
            $image_file = $this->logo_header_path_left;
            $this->Image($image_file, 15, 3, 50, '', 'JPG', $this->link_left, 'T', false, 300, '', false, false, 0, false, false, false);
            // Set font     
            $this->SetFont('helvetica', 'B', 20);
            $this->SetY(5);
            $this->Cell(210, 100, 'Ecole Francaise d\'Athènes', 0, false, 'C', 0, $this->link_right, 0, false, 'A', 'C'); 

            $image_file = $this->logo_header_path_right;
            $this->Image($image_file, 180, 2, 10, '', 'JPG', $this->link_right, 'T', false, 50, '', false, false, 0, false, false, false);

            //SEPARATOR
            $style = array('width' => 0.4, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0));
            $this->Line(15, 18, 195, 18, $style);     
        }
    }
}