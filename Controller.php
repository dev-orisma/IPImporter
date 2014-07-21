<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\Plugins\IPImporter;

use Piwik\View;

use PHPExcel;
use IOFactory as IOF;
require_once  './PHPExcel/Classes/PHPExcel.php';
require_once  './PHPExcel/Classes/PHPExcel/IOFactory.php';

/**
 *
 */
class Controller extends \Piwik\Plugin\Controller
{

    public function index()
    {
        $view = new View('@IPImporter/index.twig');
        // $this->setBasicVariablesView($view);
        $view->answerToLife = '42';

        return $view->render();
    }
	public function upload(){
		$inputFileName = $_FILES['fileupload']['tmp_name'];



		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
		$objPHPExcel = $objReader->load($inputFileName);

		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
		$highestRow = $objWorksheet->getHighestRow();
		$highestColumn = $objWorksheet->getHighestColumn();

		$data = array();
		for ($row = 2; $row <= $highestRow; ++$row) {
		    $dataRow = $objWorksheet->rangeToArray('A'.$row.':'.$highestColumn.$row,null, true, true, true);
		    $namedDataArray = array();
		    $namedDataArray["branch"] = ($dataRow[$row]["A"]);
		    $namedDataArray["province"] = ($dataRow[$row]["B"]);
		    $namedDataArray["lan1"] = ($dataRow[$row]["C"]);
		    $namedDataArray["lan2"] = empty($dataRow[$row]["D"])?"":$dataRow[$row]["D"];
		    $data[] = $this->parseRow($namedDataArray);

		}
		$str = "<?php return array(" .implode(",",$data). ");";
		try {
			if(file_put_contents("./tmp/assets/ipdata.php" , $str)){
				echo "Upload file success.";
			}else
				echo "Upload file failed.";

		} catch (Exception $e) {
			var_dump($e);
		}

	}
	public function parseProvince($name) {
		$province = array(
						"สุโขทัย" => "09",
						"กำแพงเพชร" => "11",
						"พิษณุโลก" => "12",
						"พิจิตร" => "13",
						"เพชรบูรณ์" => "14",
						"อุทัยธานี" => "15",
						"นครสวรรค์" => "16",
						"ชัยนาท" => "32",
						"สิงห์บุรี" => "33",
						"ลพบุรี" => "34",
						"อ่างทอง" => "35",
						"พระนครศรีอยุธยา" => "36",
						"สระบุรี" => "37",
						"นนทบุรี" => "38",
						"ปทุมธานี" => "39",
						"กรุงเทพมหานคร" => "40",
						"สมุทรปราการ" => "42",
						"นครนายก" => "43",
						"สุพรรณบุรี" => "51",
						"นครปฐม" => "53",
						"สมุทรสงคราม" => "54",
						"สมุทรสาคร" => "55",
						"ตาก" => "08",
						"กาญจนบุรี" => "50",
						"ราชบุรี" => "52",
						"เพชรบุรี" => "56",
						"ประจวบคีรีขันธ์" => "57",
						"ฉะเชิงเทรา" => "44",
						"ปราจีนบุรี" => "45",
						"ชลบุรี" => "46",
						"ระยอง" => "47",
						"จันทบุรี" => "48",
						"ตราด" => "49",
						"สระแก้ว" => "80",
						"หนองคาย" => "17",
						"เลย" => "18",
						"สกลนคร" => "20",
						"นครพนม" => "21",
						"ขอนแก่น" => "22",
						"กาฬสินธุ์" => "23",
						"มหาสารคาม" => "24",
						"ร้อยเอ็ด" => "25",
						"ชัยภูมิ" => "26",
						"นครราชสีมา" => "27",
						"บุรีรัมย์" => "28",
						"สุรินทร์" => "29",
						"ศรีสะเกษ" => "30",
						"อุบลราชธานี" => "71",
						"ยโสธร" => "72",
						"บึงกาฬ" => "73",
						"อุดรธานี" => "76",
						"อำนาจเจริญ" => "77",
						"มุกดาหาร" => "78",
						"หนองบัวลำภู" => "79",
						"แม่ฮ่องสอน" => "01",
						"เชียงใหม่" => "02",
						"เชียงราย" => "03",
						"น่าน" => "04",
						"ลำพูน" => "05",
						"ลำปาง" => "06",
						"แพร่" => "07",
						"อุตรดิตถ์" => "10",
						"พะเยา" => "41",
						"นราธิวาส" => "31",
						"ชุมพร" => "58",
						"ระนอง" => "59",
						"สุราษฎร์ธานี" => "60",
						"พังงา" => "61",
						"ภูเก็ต" => "62",
						"กระบี่" => "63",
						"นครศรีธรรมราช" => "64",
						"ตรัง" => "65",
						"พัทลุง" => "66",
						"สตูล" => "67",
						"สงขลา" => "68",
						"ปัตตานี" => "69",
						"ยะลา" => "70",
		            );
		return $province[$name];
	}

	public function parseIP($data) {
		$ch = explode("/",$data);
		if(count($ch) != 2){
			$data = $data. "/24";
		}
		return "'". str_replace(".x",".0",$data)."'";
	}
	public function parseRow($data) {
		$networks = array();
		if(!empty($data["lan1"])) $networks[] = $this->parseIP($data["lan1"]);
		if(!empty($data["lan2"])) $networks[] = $this->parseIP($data["lan2"]);
		return  "array(
			        'visitorInfo' => array(
			            'location_country' => 'th',
			            'location_region' => '" . $this->parseProvince($data["province"]) . "',
			            'location_city' => '".$data["province"]."',
			            'location_provider' => '".$data["branch"]."'
			        ),
			        'networks' => array(".implode(",",$networks).")
			    )";

	}
}
