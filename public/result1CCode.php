<?php
declare(strict_types=1);

namespace Sikuda\Php1c;
require_once __DIR__."/../src/CodeStream.php";



/**
* Скрипт для обмена выполняемого кода по 1С (обработка UnitTests.epf)
*
*/
//ini_set('display_errors', 0);
//ini_set('display_startup_errors', 0);
//error_reporting(0);

if(isset($_GET["lang"])) $Language1C = $_GET["lang"];

$str = 'Silence is gold';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$code = file_get_contents('php://input');

	//echo $str;
	//die();

	$php1C = new CodeStream();

	if(isset($_GET["code"])) $str = $php1C->makeCode($code);
	else{	
		if ($Language1C === 'en') {
			$str = $php1C->makeCode($code, "Result");    
		}
		else{
			$str = $php1C->makeCode($code, "Результат"); 
		} 
	}
}	

//echo $php1C->toString1C($str);
echo $str;
