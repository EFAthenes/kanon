<?php

/**
 * Description of KPdf
 *
 * @author Hippolyte
 */
require_once __ROOT__.'/K-composer/vendor/autoload.php';

use Fpdf\Fpdf;

class KPdf
{
    private string $police="Arial";
    private int $sautLigne=4;
    private int $sautParagraphe=8;
    private int $dpi=72;
    private int $taillePoliceTitrePrincipal=18;
    private int $taillePoliceTitreParagraphe=12;
    private int $taillePoliceNormale=10;
    private Fpdf $pdf;
    private string $filename;
    private static int $A4_HEIGHT=297;
    private static int $A4_WIDTH=210;
    private static float $MM_IN_INCH=25.4;

    public function __construct(string $filename)
    {
        $this->filename=$filename;
        $this->pdf=new Fpdf();
    }

    //------------------------------------------------------------------------//
    //                                GETTER                                  //
    //------------------------------------------------------------------------//

    public function getPolice(): string
    {
        return $this->police;
    }

    public function getSautLigne(): int
    {
        return $this->sautLigne;
    }

    public function getSautParagraphe(): int
    {
        return $this->sautParagraphe;
    }

    public function getDpi(): int
    {
        return $this->dpi;
    }

    public function getTaillePoliceTitrePrincipal(): int
    {
        return $this->taillePoliceTitrePrincipal;
    }

    public function getTaillePoliceTitreParagraphe(): int
    {
        return $this->taillePoliceTitreParagraphe;
    }

    public function getTaillePoliceNormale(): int
    {
        return $this->taillePoliceNormale;
    }

    //------------------------------------------------------------------------//
    //                                SETTER                                  //
    //------------------------------------------------------------------------//

    /**
     * Police du document PDF
     * Valeur par défaut : Arial
     * Ne change pas les textes écrits avant le changement de police
     * @param string $police
     * @return $this
     */
    public function setPolice(string $police)
    {
        $this->police=$police;
        return $this;
    }

    /**
     * Taille d'un saut de ligne 
     * Valeur par défaut : 4
     * @param int $sautLigne
     * @return $this
     */
    public function setSautLigne(int $sautLigne)
    {
        $this->sautLigne=$sautLigne;
        return $this;
    }

    /**
     * Taille d'un saut de paragraphe
     * Valeur par défaut : 8
     * @param int $sautParagraphe
     * @return $this
     */
    public function setSautParagraphe(int $sautParagraphe)
    {
        $this->sautParagraphe=$sautParagraphe;
        return $this;
    }

    /**
     * DPI utilisé par les images
     * Valeur par défaut : 72
     * @param int $dpi
     * @return $this
     */
    public function setDpi(int $dpi)
    {
        $this->dpi=$dpi;
        return $this;
    }

    /**
     * Taille de police utilisée pour le titre principal (page de titre)
     * Valeur par défaut : 18
     * @param int $taillePoliceTitrePrincipal
     * @return $this
     */
    public function setTaillePoliceTitrePrincipal(int $taillePoliceTitrePrincipal)
    {
        $this->taillePoliceTitrePrincipal=$taillePoliceTitrePrincipal;
        return $this;
    }

    /**
     * Taille de police utilisée pour les titres de paragraphe
     * Valeur par défaut : 12
     * @param int $taillePoliceTitreParagraphe
     * @return $this
     */
    public function setTaillePoliceTitreParagraphe(int $taillePoliceTitreParagraphe)
    {
        $this->taillePoliceTitreParagraphe=$taillePoliceTitreParagraphe;
        return $this;
    }

    /**
     * Taille de police utilisée pour le texte standard
     * Valeur par défaut : 10
     * @param int $taillePoliceNormale
     * @return $this
     */
    public function setTaillePoliceNormale(int $taillePoliceNormale)
    {
        $this->taillePoliceNormale=$taillePoliceNormale;
        return $this;
    }

    //------------------------------------------------------------------------//
    //                               METHODES                                 //
    //------------------------------------------------------------------------//

    /**
     * Insère une page de titre
     * @param string $titre
     */
    public function setPageTitre(string $titre): void
    {
        $this->newPage();
        $this->pdf->SetFont($this->police,'B',$this->taillePoliceTitrePrincipal);
        $this->addSpace($this->sautParagraphe*2);
        $this->pdf->Cell(0,0,$this->utf8_decode($titre),0,2,'C');
    }

    /**
     * Ajoute un titre de paragraphe
     * @param string $titreParagraphe
     * @return void
     */
    public function addTitreParagraphe(string $titreParagraphe): void
    {
        $this->pdf->SetFont($this->police,'B',$this->taillePoliceTitreParagraphe);
        $this->pdf->Cell(0,0,$this->utf8_decode($titreParagraphe),0,2);
        $this->pdf->Ln($this->sautLigne);
    }

    /**
     * Ajoute du texte
     * @param string $text
     * @return void
     */
    public function addText(string $text): void
    {
        $this->pdf->SetFont($this->police,'',$this->taillePoliceNormale);
        $this->pdf->MultiCell(0,5,$this->utf8_decode($text));
    }

    /**
     * Ajoute une image
     * @param string $path
     * @return bool
     */
    public function addImage(string $path): bool
    {
        $tmp=new KFile($path);
        if($tmp->exists()&&$tmp->getFileSize()>0)
        {
            if(!is_null(list($width,$height)=$this->resizeToFit($path)))
            {
                $this->pdf->Image($path,(self::$A4_WIDTH-$width)/2,null,$width);
                return true;
            }
        }
        return false;
    }

    /**
     * Fais un saut de page
     * @return void
     */
    public function newPage(): void
    {
        $this->pdf->AddPage();
    }

    /**
     * Ajoute un espace d'une taille précisée en paramètre
     * @return void
     */
    public function addSpace(int $space): void
    {
        $this->pdf->Ln($space);
    }

    /**
     * Ajoute du texte souligné surchargé d'un lien
     * @param string $text
     * @param string $url
     * @return void
     */
    public function addTextWithLink(string $text,string $url): void
    {
        $this->pdf->SetFont($this->police,'U',$this->taillePoliceNormale);
        $this->pdf->Cell(0,5,$this->utf8_decode($text),'','','',false,$url);
    }

    /**
     * Envoie le pdf à la destination passée lors de l'initialisation de l'objet
     * @return void
     */
    public function getOutput(): void
    {
        $this->pdf->Output('F',$this->filename);
    }

    /**
     * Calcule le ration en fonction des mm et du dpi
     * @param mixed $val
     * @return float
     */
    private function pixelsToMM(mixed $val): float
    {
        return (float)($val*self::$MM_IN_INCH/$this->dpi);
    }


    /**
     * Redimensionne l'image pour son insertion dans le pdf
     * @param string $imgFilename
     * @return array<int,float>|null
     */
    private function resizeToFit(string $imgFilename): ?array
    {
        if(!getimagesize($imgFilename))
        {
            return null;
        }
        list($width,$height)=getimagesize($imgFilename);
        $widthScale=self::$A4_WIDTH/$width;
        $heightScale=self::$A4_HEIGHT/$height;
        $scale=min($widthScale,$heightScale);
        return array(
            round($this->pixelsToMM($scale*$width)),
            round($this->pixelsToMM($scale*$height))
        );
    }
    
    private function utf8_decode(mixed $string) : string
    {
        $s = (string) $string;
        $len = \strlen($s);

        for ($i = 0, $j = 0; $i < $len; ++$i, ++$j) {
            switch ($s[$i] & "\xF0") {
                case "\xC0":
                case "\xD0":
                    $c = (\ord($s[$i] & "\x1F") << 6) | \ord($s[++$i] & "\x3F");
                    $s[$j] = $c < 256 ? \chr($c) : '?';
                    break;

                case "\xF0":
                    ++$i;
                    // no break

                case "\xE0":
                    $s[$j] = '?';
                    $i += 2;
                    break;

                default:
                    $s[$j] = $s[$i];
            }
        }

        return substr($s, 0, $j);        
    }
}