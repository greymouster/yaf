<?php

/**
 * excel 操作类
 *
 * @author sangba <wangjianbo@747.cn>
 * @final 2013-7-29
 */
class TZ_Excel {

    private $_excelVersion = null; //当前excel本班信息
    private $_book = 0;    //当前的工作簿
    private $_phpexcel = null;  //phpexcel对象
    private $_row = 1;
    private $_fileName = 'heimi';

    public function __construct() {
        $this->_config = Yaf_Registry::get('config');
        $path = $this->_config->library->path;
        Yaf_Loader::import($path . '/PHPExcel.php');
        $this->_phpexcel = new PHPExcel();
    }

    /**
     * 获取一行单元格值
     * 
     * @param string $col
     * @param int $row
     */
    public function find($col, $row) {
        return $this->_phpexcel->getSheet($this->_book)->getCell($col . $row)->getValue();
    }

    /**
     * 加载phpexcel文件
     * 
     * @param string $fileName
     * @return object
     */
    public function loadExcel($fileName) {
        if (!is_file($fileName)) {
            throw new Exception('不能加载非电子表格文件.');
        }
        $reader = $this->selectSuitableReader($fileName);
        $this->_phpexcel = $reader->load($fileName);
        return true;
    }

    /**
     * 获取excel的列标题
     * 
     * @return array
     */
    public function findColumnTitle() {
        $colMax = $this->_getColMax();
        $outPut = array();
        $k = 0;
        for ($i = 'A'; $i != $colMax; $i++) {
            $k++;
        }
        $k++;
        $j = 0;
        $index = 'A';
        while ($j < $k) {
            $title = $this->find($index, 1);
            if (isset($title)) {
                $outPut[] = $title;
            } else {
                break;
            }
            $j++;
            $index++;
        }
        return $outPut;
    }

    private function _getColMax() {
        $colMax = $this->_phpexcel->getSheet($this->_book)->getHighestColumn();
        $k = 0;
        for ($i = 'A'; $i != $colMax; $i++) {
            $k++;
        }
        $j = 0;
        $index = 'A';
        while ($j < $k) {
            $title = $this->find($index, 1);
            if (empty($title)) {
                break;
            }
            $j++;
            $index++;
        }
        return $index;
    }

    /**
     * 获取某一列数据
     * 
     * @param int $col
     * @return array
     */
    public function findColumns($col) {
        $rowMax = $this->_phpexcel->getSheet($this->_book)->getHighestRow();
        $outPut = array();
        for ($i = 1; $i <= $rowMax; $i++) {
            $outPut[] = $this->find($col, $i);
        }
        return $outPut;
    }

    /**
     * 选择正确的reader对象
     * 
     * @param string $fileName
     * @return object $phpReader
     */
    public function selectSuitableReader($fileName) {
        $phpReader = new PHPExcel_Reader_Excel2007();

        //判断excel版本，同时返回reader对象
        if (!$phpReader->canRead($fileName)) {
            $phpReader = new PHPExcel_Reader_Excel5();
            if (!$phpReader->canRead($fileName)) {
                return FALSE;
            } else {
                $this->_excelVersion = 'Excel5';
            }
        } else {
            $this->_excelVersion = 'Excel2007';
        }
        return $phpReader;
    }

    /**
     * 从excel中读出所有的数据
     *  
     * @return array
     */
    public function findAll() {
        $col_max = $this->_getColMax();
        $k = 0;
        for ($i = 'A'; $i != $col_max; $i++) {
            $k++;
        }
        $k++;
        $row_max = $this->_phpexcel->getSheet($this->_book)->getHighestRow();
        $output = array();
        for ($r = 1; $r <= intval($row_max); $r++) {
            $j = 0;
            $index = 'A';
            while ($j < $k) {
                $output[$r][$index] = $this->find($index, $r);
                $j++;
                $index++;
            }
        }
        return $output;
    }

    /**
     * 导出excel
     * 
     * @param array $data
     * @return excel file
     */
    public function dump($data = array(), $excelType = '2003') {
        //设置附属属性，可以省略
        $this->_phpexcel->getProperties()->setCreator("heimi");
        $this->_phpexcel->getProperties()->setLastModifiedBy("heimi");
        $this->_phpexcel->getProperties()->setTitle("Office 2003 XLS Test Document");
        $this->_phpexcel->getProperties()->setSubject("Office XLS Test Document");
        $this->_phpexcel->getProperties()->setDescription("Test document for Office 2003 XLS, generated using PHP classes.");
        $this->_phpexcel->getProperties()->setKeywords("office 2003 openxml php");
        $this->_phpexcel->getProperties()->setCategory("Test result file");

        $this->_phpexcel->setActiveSheetIndex(0);

        foreach ($data as $val) {
            $molumn = 'A';
            foreach ($val as $v) {
                $this->_phpexcel->getActiveSheet()->setCellValue($molumn . $this->_row, $v);
                $molumn ++;
            }
            $this->_row ++;
        }

        $this->_phpexcel->getActiveSheet()->getHeaderFooter()
                ->setOddHeader('&L&BPersonal cash register&RPrinted on &D');
        $this->_phpexcel->getActiveSheet()->getHeaderFooter()
                ->setOddFooter('&L&B' . $this->_phpexcel->getProperties()
                        ->getTitle() . '&RPage &P of &N');

        // 设置页方向和规模
        $this->_phpexcel->getActiveSheet()->getPageSetup()
                ->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
        $this->_phpexcel->getActiveSheet()->getPageSetup()
                ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $this->_phpexcel->setActiveSheetIndex(0);

        if ($excelType == '2007') {
            //excel2007
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment;filename={$this->_fileName}.xlsx");
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($this->_phpexcel, 'Excel2007');
            $objWriter->save('php://output');
            exit;
        } else {
            //excel2003
            header('Content-Type: application/vnd.ms-excel');
            header("Content-Disposition: attachment;filename={$this->_fileName}.xls");
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($this->_phpexcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }
    }

    /**
     * 设置文件名
     * 
     */
    public function setFileName($fileName = '黑米') {
        $this->_fileName = $fileName;
        return $this;
    }

    /**
     * 设置excel标题
     * 
     */
    public function setTitle($title) {
        $column = 'A';
        foreach ($title as $val) {
            $this->_phpexcel->getActiveSheet()->setCellValue($column . '1', $val);
            $column ++;
        }
        $this->_row = 2;
        return $this;
    }

}
