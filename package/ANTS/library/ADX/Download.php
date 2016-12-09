<?php
/**
 * Created by PhpStorm.
 * User: GiangBeo
 * Date: 9/28/16
 * Time: 10:07 AM
 */
namespace ADX;

use Box\Spout\Reader\ReaderFactory as ReaderFactory;
use Box\Spout\Writer\WriterFactory as WriterFactory;
use Box\Spout\Common\Type as TypeSpout;
use Box\Spout\Writer\Style\Border as Border;
use Box\Spout\Writer\Style\BorderBuilder as BorderBuilder;
use Box\Spout\Writer\Style\Color as Color;
use Box\Spout\Writer\Style\StyleBuilder as StyleBuilder;
use ADX\Exception;
use ADX\Model;

class Download {
    public static $_extension = 'csv';
    const REPORT_TEMPLATE_XLS_NAME = 'report_template.xlsx';
    const REPORT_FONT_SIZE = '14';

    public static function downloadFile($extension,$file_name,$data,$config = array()){
        self::$_extension = $extension;
        switch ($extension){
            case 'csv':{
                $writer = WriterFactory::create(TypeSpout::CSV);
                self::csvWithoutTemplate($writer,$data,$file_name);
                break;
            }
            case 'xlsx':{
                $writer = WriterFactory::create(TypeSpout::XLSX);
                switch ($config){
                    case Model\Common::DOWNLOAD_USE_TEMPLATE:{
                        self::excelUsingFormatTemplate($writer,$data,$file_name,true);
                        break;
                    }
                    case Model\Common::DOWNLOAD_MULTIPLE_SHEET:{
                        self::multipleSheet($writer,$data,$file_name);
                        break;
                    }
                    case Model\Common::DOWNLOAD_WITHOUT_TEMPLATE:{
                        self::excelUsingFormatTemplate($writer,$data,$file_name,false);
                        break;
                    }
                }
                break;
            }
        }

    }
    public static function excelUsingFormatTemplate($writer,$data,$file_name,$template){
        $reader = ReaderFactory::create(TypeSpout::XLSX);
        if($template){
            $reader->open(TPL_EXCEL_PATH.'/'.self::REPORT_TEMPLATE_XLS_NAME);
            $reader->setShouldFormatDates(true);
            foreach ($reader->getSheetIterator() as $sheetIndex => $sheet) {
                if ($sheetIndex !== 1) {
                    $writer->addNewSheetAndMakeItCurrent();
                }
                foreach ($sheet->getRowIterator() as $row) {
                    $writer->addRow($row);
                }
            }
        }
        $writer->openToBrowser($file_name.'.'.self::$_extension);
        $header = isset($data['header']) ? $data['header'] : array();
        $data_excel = isset($data['data']) ? $data['data'] : array();
        foreach ($header as $row_data){
            $border = (new BorderBuilder())
                ->setBorderBottom(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
                ->build();
            $style = (new StyleBuilder())
                ->setFontBold()
                ->setFontSize(self::REPORT_FONT_SIZE)
                //->setBorder($border)
                //->setBackgroundColor(Color::YELLOW)
                ->build();
            $writer->addRowWithStyle($row_data,$style);
        }
        foreach ($data_excel as $row_data){
            $border = (new BorderBuilder())
                ->setBorderBottom(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
                ->build();
            $style = (new StyleBuilder())
                //->setBorder($border)
                ->setFontSize(self::REPORT_FONT_SIZE)
                ->build();
            $writer->addRowWithStyle($row_data,$style);
        }
        $sheet = $writer->getCurrentSheet();
        $sheet->setName($file_name);
        //Close
        $reader->close();
        $writer->close();
    }
    public static function multipleSheet($writer,$data_excel,$file_name){
        $reader = ReaderFactory::create(TypeSpout::XLSX);
        $reader->open(TPL_EXCEL_PATH.'/'.self::REPORT_TEMPLATE_XLS_NAME);
        $reader->setShouldFormatDates(true);
        $writer->openToBrowser($file_name.'.'.self::$_extension);
        $data_first = current($data_excel);
        foreach ($data_first as $data_first){
            $title_sheet_first = isset($data_first['title']) ? $data_first['title']: '';
            if(!empty($title_sheet_first)){
                $writer->setName($title_sheet_first);
            }
//            $border = (new BorderBuilder())
//                ->setBorderBottom(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
//                ->build();
//            $style = (new StyleBuilder())
//                ->setBorder($border)
//                ->setFontSize(self::REPORT_FONT_SIZE)
//                ->build();
            $data_first_sheet = isset($data_first['data']) ? $data_first['data'] : array();
            foreach ($data_first_sheet as $row_first_sheet){
                $writer->addRow($row_first_sheet);
            }
        }
        unset($data_excel[0]);
        foreach ($data_excel as $data_new_sheet){
            $newSheet = $writer->addNewSheetAndMakeItCurrent();
            $title_sheet = isset($data_new_sheet['title']) ? $data_new_sheet['title']: '';
            if(!empty($title_sheet)){
                $newSheet->setName($data_new_sheet['title']);
            }
            $data_rows = isset($data_new_sheet['data']) ? $data_new_sheet['data']: array();
            foreach ($data_rows as $rows){
                $writer->addRow($rows);
            }
        }
        $reader->close();
        $writer->close();
    }
    public static function csvWithoutTemplate($writer,$data,$file_name){
        $writer->openToBrowser($file_name.'.'.self::$_extension); // stream data directly to the browser
        $writer->addRows($data); // add multiple rows at a time
        $writer->close();
    }
}