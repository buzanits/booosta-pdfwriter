<?php
namespace booosta\pdfwriter;

\booosta\Framework::add_module_trait('webapp', 'pdfwriter\webapp');

trait webapp
{
  protected function make_pdf_image_tag($image, $fx = null, $fy = null)
  {
    list($x, $y, $dummy1, $dummy2) = getimagesize($image);
    if($fx !== null) $x = $fx;
    if($fy !== null) $y = $fy;

    return "<img src=\"$image\" height=\"$y\" width=\"$x\">";
  }
}
