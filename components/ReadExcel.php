<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 15.09.2015
 * Time: 11:01
 */

namespace app\admin\components;


use Exception;
use PHPExcel_IOFactory;
use yii\base\Component;

class ReadExcel extends Component
{
    /** @var \PHPExcel */
    private $_excel;
    /** @var \PHPExcel_Worksheet */
    private $_sheet;
    public $maxRow;
    public $maxColumn;
    public $currentRow = 1;

    public static function load($file, $sheet = 0)
    {
        $model = new self;

        try {
            $fileType = PHPExcel_IOFactory::identify($file);
            $reader = PHPExcel_IOFactory::createReader($fileType);
            $model->_excel = $reader->load($file);
        } catch (Exception $e) {
            die('Error loading file "' . pathinfo($file, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }

        $model->_sheet = $model->_excel->getSheet($sheet);
        $model->maxRow = $model->_sheet->getHighestRow();
        $model->maxColumn = $model->_sheet->getHighestColumn();

        return $model;
    }

    public function getRow()
    {
        if($this->currentRow > $this->maxRow) return false;
        $data = $this->_sheet->rangeToArray('A' . $this->currentRow . ':' . $this->maxColumn . $this->currentRow, null, true, false);
        $this->currentRow++;
        return reset($data);
    }
}