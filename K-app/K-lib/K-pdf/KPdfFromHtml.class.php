<?php
/**
 * Description of KPdf
 *
 * @author Mulot Louis
 */
class KPdfFromHtml 
{
    private $inputFile;
    private $outputFile;

    function __construct($inputFile="",$outputFile="")
    {
        $this->inputFile=$inputFile;
        $this->outputFile=$outputFile;
    }
    function  __destruct()
    {
    }

    public function doIt()
    {
        require_once("dompdf_config.inc.php");
        $paper = DOMPDF_DEFAULT_PAPER_SIZE;
        //$paper = "letter";
        $orientation = "portrait";

        $dompdf = new DOMPDF();
        $dompdf->load_html_file($this->inputFile);
        $dompdf->set_paper($paper, $orientation);
        $dompdf->render();

        file_put_contents($this->outputFile, $dompdf->output());
        if(file_exists($this->outputFile))
        {
            chmod($this->outputFile,0444);
            unlink($this->inputFile);
            return true;
        }
        unlink($this->inputFile);
        return false;          
    }

    public function streamPDF($outfile)
    {
        require_once("dompdf_config.inc.php");
        $dompdf = new DOMPDF();       
        echo $dompdf->stream($outfile);
    }
}
