<?php

namespace app\myClass;

set_include_path(get_include_path() . PATH_SEPARATOR . "/vendor/dompdf/dompdf");
//require_once \Yii::getAlias('@app/vendor/dompdf/dompdf/dompdf_config.inc.php');

include \Yii::getAlias('@app/myClass/pdfmerger/PDFMerger.php');

use Dompdf\Exception;
use Dompdf\Options;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;
use yii\helpers\FileHelper;

class PDFHandler
{

    public static function createPDFFile($html, $filepath){
        $options = new Options();
        $options->set('defaultFont', 'dejavu serif');
        //$options->setFontHeightRatio(16);
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->load_html($html);
        $dompdf->setPaper('A4');

        $dompdf->render();
        $output = $dompdf->output();
        file_put_contents($filepath, $output);
    }

    public static function mergePDF($files, $pathResult, $mode = 'browser'){
        $pdf = new \PDFMerger\PDFMerger();
        //REPLACE 'file' WITH 'browser', 'download', 'string', or 'file' for output options
        //You do not need to give a file path for browser, string, or download - just the name.
        foreach($files as $file){
            $pdf->addPDF($file);
        }
        return $pdf->merge($mode, $pathResult);
    }

    public static function separatePDF($fileName){
        $files = [];
        $dir = __dir__ . '/tmp';
        FileHelper::createDirectory($dir);
        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($fileName);
        for($i = 1; $i <= $pageCount; $i++){
            $pageId = $pdf->importPage($i, PdfReader\PageBoundaries::MEDIA_BOX);
            $pdf->addPage();
            $pdf->useImportedPage($pageId);
            $filePath = $dir . '/' . $i . '.pdf';
            $pdf->Output("F", $filePath);
            $files[] = $filePath;
            $pdf = new Fpdi();
            $pdf->setSourceFile($fileName);
        }
        $pdf->Close();
        return $files;
    }

    public static function delTmpDir(){
        try {
            FileHelper::removeDirectory(__dir__ . '/tmp');
        } catch (Exception $e){}
    }
}