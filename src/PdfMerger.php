<?php
/**
 * TODO
 */
namespace andreaventuri\PdfMerger;

use tecnickcom\tcpdf;
use propa\tcpdi;

class PdfMerger
{
    protected $_files;
    protected $_tcpdi;
    protected $_showHeader;
    protected $_showFooter;

    /**
     * Constructor
     * @param \TCPDI $_tcpdi custom TCPDI object
	 * @param bool $showHeader print header on merged file
	 * @param bool $showFooter print footer on merged file
     */
    public function __construct(\TCPDI $tcpdi=null, $showHeader=false, $showFooter=false)
    {
        if(empty($_tcpdi))
        {
            $this->_tcpdi = new \TCPDI;
        }
        else
        {
            $this->_tcpdi = $tcpdi;
        }

        $this->_showHeader = $showHeader;
        $this->_showFooter = $showFooter;
    }

    /**
     * Add a PDF for inclusion in the merge with a valid file path. Pages should be formatted: 1,3,6, 12-16.
     * @param string $filepath
     * @param string $pages
     * @throws \Exception
     * @return PDFMerger
     */
    public function addPDF($filepath, $pages = 'all')
    {
        if(file_exists($filepath))
        {
            if(strtolower($pages) != 'all')
            {
                $pages = $this->_rewritepages($pages);
            }

            $this->_files[] = array($filepath, $pages);
        }
        else
        {
            throw new \Exception("Could not locate PDF on '$filepath'");
        }

        return $this;
    }

    /**
     * Merges your provided PDFs and outputs to specified location.
     * @param string $outputmode
     * @param string $outputname
     * @throws \Exception
     * @return string|bool
     */
    public function merge($outputmode = 'browser', $outputpath = 'newfile.pdf')
    {
        if(!isset($this->_files) || !is_array($this->_files)) throw new \Exception("No PDFs to merge.");

        $this->_tcpdi->SetPrintHeader($this->_showHeader);
        $this->_tcpdi->SetPrintFooter($this->_showFooter);

        // merger operations
        foreach($this->_files as $file)
        {
            $filename  = $file[0];
            $filepages = $file[1];

            $count = $this->_tcpdi->setSourceFile($filename);

            // add the pages
            if($filepages == 'all')
            {
                for($i=1; $i<=$count; $i++)
                {
                    $template = $this->_tcpdi->importPage($i);
                    $size = $this->_tcpdi->getTemplateSize($template);
                    $orientation = ($size['h'] > $size['w']) ? 'P' : 'L';

                    $this->_tcpdi->AddPage($orientation, array($size['w'], $size['h']));
                    $this->_tcpdi->useTemplate($template);
                }
            }
            else
            {
                foreach($filepages as $page)
                {
                    if(!$template = $this->_tcpdi->importPage($page))
                        throw new \Exception("Could not load page '$page' in PDF '$filename'. Check that the page exists.");

                    $size = $this->_tcpdi->getTemplateSize($template);
                    $orientation = ($size['h'] > $size['w']) ? 'P' : 'L';

                    $this->_tcpdi->AddPage($orientation, array($size['w'], $size['h']));
                    $this->_tcpdi->useTemplate($template);
                }
            }
        }

        //output operations
        $mode = $this->_switchmode($outputmode);

        if($mode == 'S')
        {
            return $this->_tcpdi->Output($outputpath, 'S');
        }
        else if($mode == 'F')
        {
            $this->_tcpdi->Output($outputpath, $mode);
            return true;
        }
        else
        {
            if($this->_tcpdi->Output($outputpath, $mode) == '')
            {
                return true;
            }
            else
            {
                throw new \Exception("Error outputting PDF to '$outputmode'.");
                return false;
            }
        }
    }

    /**
     * FPDI uses single characters for specifying the output location. Change our more descriptive string into proper format.
     * @param string $mode
     * @return string
     */
    protected function _switchmode($mode)
    {
        switch(strtolower($mode))
        {
            case 'download':
                return 'D';

            case 'browser':
                return 'I';

            case 'file':
                return 'F';

            case 'string':
                return 'S';

            default:
                return 'I';
        }
    }

    /**
     * Takes our provided pages in the form of 1,3,4,16-50 and creates an array of all pages
     * @param string $pages
     * @throws \Exception
     * @return array
     */
    protected function _rewritepages($pages)
    {
        $pages = str_replace(' ', '', $pages);
        $part = explode(',', $pages);

        // parse hyphens
        foreach($part as $i)
        {
            $ind = explode('-', $i);

            if(count($ind) == 2)
            {
                $x = $ind[0]; // start page
                $y = $ind[1]; // end page

                if($x > $y) throw new \Exception("Starting page, '$x' is greater than ending page '$y'.");

                // add middle pages
                while($x <= $y)
                {
                    $newpages[] = (int) $x;
                    $x++;
                }
            }
            else
            {
                $newpages[] = (int) $ind[0];
            }
        }

        return $newpages;
    }
}
