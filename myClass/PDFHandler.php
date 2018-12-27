<?php
/**
 * Created by PhpStorm.
 * User: E.Pinchuk
 * Date: 26.12.2018
 * Time: 15:39
 */

namespace app\myClass;

set_include_path(get_include_path() . PATH_SEPARATOR . "/vendor/dompdf/dompdf");
//require_once "dompdf_config.inc.php";

class PDFHandler
{

    public static function createPDF(){
        $dompdf = new \Dompdf\Dompdf(array('isPhpEnabled' => true));

        $html = <<<'ENDHTML'
<html>
 <body>
  <h1>Hello Dompdf</h1>
 </body>
</html>
ENDHTML;


        $dompdf->load_html($html);
        $dompdf->render();
        $dompdf->stream("hello.pdf");

//        $output = $dompdf->output();
//        file_put_contents("/path/to/file.pdf", $output);
    }
}