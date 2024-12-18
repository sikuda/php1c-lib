<?php
declare(strict_types=1);

/**
* Модуль работы с файлами в 1С
* 
* Модуль для работы с файлами, записями в файлы и чтение из файлов
* 
* TO DO! 
*
* @author  sikuda admin@sikuda.ru
* @version 0.3
*/
namespace Sikuda\Php1c;
use Exception;

require_once('Date1C.php');

const php1C_typesPHP_File = array('File1C', 'TextReader1C', 'TextWriter1C');
const php1C_functionsPHP_File = array('Exist(','IsFile(','IsDirectory(','Size(','Open(', 'Close(', 'Read(', 'ReadLine(', 'Write(','WriteLine(');
//
/**
 * Файл на сервере
 */
class File1C {

    private string $fileName;
	function __construct($buffer = '') {
       if(is_string($buffer)) $this->fileName = $buffer;
    }

	/**
	* Получить короткое имя файла
	*
	* @return string 
	*/
	public function Name(): string
    {
		$pos = mb_strrpos($this->fileName,'/');
		if(!$pos) return $this->fileName;
		return mb_substr($this->fileName,$pos);
	}

	/**
	* Получить короткое имя файла без расширения
	*
	* @return string 
	*/
	public function BaseName(): string
    {
		return strstr($this->Name(),'.', true);		
	}

	/**
	* Получить полное имя файла на сервере
	*
	* @return string 
	*/
	public function FullName(): string
    {
		return $this->fileName;		
	}

	/**
	* Получить полный путь на сервере
	*
	* @return string 
	*/
	public function Path(): string
    {
		return strstr($this->fileName,'.', true);
	}

	/**
	* Получить расширение файла
	*
	* @return string 
	*/
	public function Extension(): string
    {
		return ltrim(strstr($this->fileName,'.'), '.');
	}

	/**
	* Существует файл на сервере
	*
	* @return bool 
	*/
	public function Exist(): bool
    {
		if(file_exists($this->fileName) == 1) return true;
		else return false;	
	}

	/**
	* Это файл на сервере
	*
	* @return bool 
	*/
	public function IsFile(): bool
    {
		if(is_file($this->fileName)) return true;
		else return false;	
	}

	/**
	* Это директория на сервере
	*
	* @return bool 
	*/
	public function IsDirectory(): bool
    {
		if(is_dir($this->fileName)) return true;
		else return false;		
	}

    /**
     * GetModificationTime
     * @throws \Exception
     */
    public function GetModificationTime(): Date1C
    {
		return Date1C( date("YmdHis", filemtime($this->fileName)));
	}
	public function GetModificationUniversalTime(): Date1C
    {
        return Date1C( date("YmdHis", filemtime($this->fileName)));
	}
	//touch()
	function SetModificationTime(Date1C $date)
    {
        touch($this->fileName, $date->value->getOffset());
	}
	function SetModificationUniversalTime (Date1C $date){
        touch($this->fileName, $date->value->getOffset());
	}
    //size of file
    function Size(){
        $size = filesize($this->fileName);
        if($size === false) return 0;
        else return $size;
    }

}
function File1C($name): File1C
{
	return new File1C( $name);
}

/*
 * Новый ЧтениеТекста(<ИмяФайла>, <Кодировка>, <РазделительСтрок>, <КонвертируемыйРазделительСтрок>, <МонопольныйРежим>)
 * */
class TextReader1C{
    private string $fileName;
    private $handle;
    function __construct(string $buffer = '') {
        $this->fileName = $buffer;
    }
    function Open(){
        $this->handle = fopen($this->fileName, 'r');
    }
    function Close(){
        fclose($this->handle);
    }
    function Read(){
        return fgets($this->handle);
    }
    function ReadLine(string $delim="\n"): string {
        return stream_get_line($this->handle, PHP_INT_MAX, $delim);
    }
}

/**
 * @throws Exception
 */
function TextReader1C(array $value): TextReader1C
{
    if (sizeof($value) == 0 || !is_string($value[0]))  throw new Exception("Неправильный тип первого параметра");
    if(!file_exists($value[0])) throw new Exception("Ошибка при вызове конструктора (ЧтениеТекста): Файл не обнаружен '".$value[0]."'");

    $reader = new TextReader1C($value[0]);
    $reader->Open();
    return $reader;
}

/*
 * Новый ЗаписьТекста(<ИмяФайла>, <Кодировка>, <РазделительСтрок>, <Дописывать>, <КонвертируемыйРазделительСтрок>)
 */
class TextWriter1C{
    private string $fileName;
    private $handle;
    function __construct($buffer = '') {
        if(is_string($buffer)) $this->fileName = $buffer;
    }
    function Open(){
        $this->handle = fopen($this->fileName, 'w');
    }
    function Close(){
        fclose($this->handle);
    }
    function Write(string $data){
        fwrite($this->handle, $data);
    }
    function WriteLine(string $data, string $delim="\n"){
        fwrite($this->handle, $data.$delim);
    }
}

function TextWriter1C(array $value): TextWriter1C
{
    if (sizeof($value) == 0 || !is_string($value[0]))  throw new Exception("Неправильный тип первого параметра");

    $writer = new TextWriter1C($value[0]);
    $writer->Open();
    return $writer;
}