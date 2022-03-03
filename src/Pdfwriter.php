<?php
namespace booosta\pdfwriter;

use \booosta\Framework as b;
b::init_module('pdfwriter');

class Pdfwriter extends \booosta\base\Module
{ 
  use moduletrait_pdfwriter;

  protected $page_orientation, $page_format;
  protected $author, $title, $subject, $keywords;
  protected $font, $fontsize, $fontstyle;
  protected $html_content, $header_content = '', $footer_content = '';
  protected $images;

  protected $header_font, $footer_font;
  protected $header_margin, $footer_margin;


  public function __construct($html_content = null)
  {
    parent::__construct();

    $this->html_content = $html_content;
    $this->page_orientation = 'P';
    $this->page_format = 'A4';

    $this->font = 'dejavusans';
    $this->fontsize = '10';

    $this->images = [];
  }

  public function set_html_content($data) { $this->html_content = $data; }
  public function set_header_content($data) { $this->header_content = $data; }
  public function set_footer_content($data) { $this->footer_content = $data; }
  public function set_page_orientation($data) { $this->page_orientation = $data; }
  public function set_page_format($data) { $this->page_format = $data; }
  public function set_author($data) { $this->author = $data; }
  public function set_title($data) { $this->title = $data; }
  public function set_subject($data) { $this->subject = $data; }
  public function set_keywords($data) { $this->keywords = $data; }
  public function set_font($data) { $this->font = $data; }
  public function set_fontsize($data) { $this->fontsize = $data; }
  public function set_fontstyle($data) { $this->fontstyle = $data; }

  public function set_header_font($font) { $this->header_font = $font; }
  public function set_footer_font($font) { $this->footer_font = $font; }
  public function set_header_margin($margin) { $this->header_margin = $margin; }
  public function set_footer_margin($margin) { $this->footer_margin = $margin; }

  public function addImage($file, $x, $y, $params = null) { $this->images[] = new pdfimage($file, $x, $y, $params); }

  public function download($filename = 'download.pdf')
  {
    $pdf = $this->create_pdf();
    $pdf->Output($filename, 'D');
  }

  public function save($filename = 'document.pdf')
  {
    $pdf = $this->create_pdf();
    $pdf->Output(getcwd() . '/' . $filename, 'F');
  }

  public function show($filename = 'document.pdf')
  {
    $pdf = $this->create_pdf();
    $pdf->Output($filename, 'I');
  }
  
  public function get_data()
  {
    $pdf = $this->create_pdf();
    return $pdf->Output(null, 'S');
  }

  protected function create_pdf()
  {
    $pdf = new TCPDF($this->page_orientation, 'mm', $this->page_format, true, 'UTF-8');
    $pdf->SetCreator('TCPDF');

    if($this->author) $pdf->SetAuthor($this->author);
    if($this->title) $pdf->SetTitle($this->title);
    if($this->subject) $pdf->SetSubject($this->subject);
    if($this->keywords) $pdf->SetKeywords($this->keywords);

    $pdf->SetAutoPageBreak(true, 25);
    $pdf->setLanguageArray($this->lang);
    $pdf->setFontSubsetting(true);
    $pdf->setFont($this->font, $this->fontstyle, $this->fontsize);

    if($this->header_font) $pdf->set_header_font($this->header_font);
    if($this->footer_font) $pdf->set_footer_font($this->footer_font);
    if($this->header_margin) $pdf->set_header_margin($this->header_margin);
    if($this->footer_margin) $pdf->set_footer_margin($this->footer_margin);

    $pdf->setPrintHeader($this->header_content != '');
    $pdf->setPrintFooter($this->footer_content != '');

    if($this->header_content) $pdf->set_header($this->header_content);
    if($this->footer_content) $pdf->set_footer($this->footer_content);

    $pdf->AddPage();
    $pdf->writeHTML($this->html_content);

    if($this->images) foreach($this->images as $image) $image->add_to($pdf);

    return $pdf;
  }
}

class TCPDF extends \TCPDF
{
  protected $header, $footer;
  protected $header_font = ['helvetica', '', 12];
  protected $footer_font = ['helvetica', '', 12];
  protected $header_margin = 12, $footer_margin = -20;

  public function set_header($html) { $this->header = $html; }
  public function set_footer($html) { $this->footer = $html; }
  public function set_header_font($font) { $this->header_font = $font; }
  public function set_footer_font($font) { $this->footer_font = $font; }
  public function set_header_margin($margin) { $this->header_margin = $margin; }
  public function set_footer_margin($margin) { $this->footer_margin = $margin; }

  public function Header()
  {
    $this->setY($this->header_margin);
    $this->setFont($this->header_font[0], $this->header_font[1], $this->header_font[2]);

    $html = str_replace('{page}', $this->getAliasNumPage(), $this->header);
    $html = str_replace('{pages}', $this->getAliasNbPages(), $html);
    $this->writeHTML($this->header);
  }

  public function Footer()
  {
    $this->setY($this->footer_margin);
    $this->setFont($this->footer_font[0], $this->footer_font[1], $this->footer_font[2]);

    $html = str_replace('{page}', $this->getAliasNumPage(), $this->footer);
    $html = str_replace('{pages}', $this->getAliasNbPages(), $html);
    $this->writeHTML($html);
  }
}

class pdfimage
{
  protected $filename;
  protected $x, $y;

  public function __construct($filename, $x, $y, $params = null)
  {
    $this->filename = $filename;
    $this->x = $x;
    $this->y = $y;
  }

  public function add_to(&$pdf)
  {
    if(is_object($pdf)) $pdf->Image($this->filename, $this->x, $this->y);
    else return false;

    return true;
  }
}
