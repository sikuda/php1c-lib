<?php
/**
* Модуль для разбора кода 1С в массив токенов
* 
* @author  sikuda@yandex.ru
* @version 0.3
*/

namespace Sikuda\Php1c;
use Exception;
require_once 'Common1C.php';

/**
* Класс токена - элемент кода
*/
class Token {
	public $type    = 0;
	public $context = '';
	public $index   = -1;

	//pointer to handle error
	public int $row = 0;
    public int $col = 0;
	
	function __construct($type = 0, $context = '', $index=-1){
		$this->type = $type;
		$this->context = $context;
		$this->index   = $index;
	}
}

/**
* Класс обработки потока кода 1С в массив токенов
*/
class TokenStream {

	//array of token
    public array $tokens;
    //private int $i_token = 0;

    //common 
	private $str;
	private int $start = 0;
	private int $pos = 0;

	//pointer to handle error
	public int $row = 1;
    public int $col = 1;

    //Типы(types)
    const type_end_code  = -1;
    const type_undefined = 0;
    const type_newline   = 1;
    const type_space     = 2;
    const type_tablespace  = 3;
    const type_comments  = 10;
    const type_meta      = 11;
    const type_number    = 12;
    const type_string    = 13;
    const type_date      = 14;
    const type_operator  = 15;
    const type_keyword   = 16;
    const type_identification = 17;
    const type_function       = 18;
    const type_extinction    = 19;
    const type_variable       = 50;
    
    //Операции(operations)
    const operation_open_bracket  = 1;
	const operation_close_bracket = 2;
	const operation_plus         = 3;
	const operation_minus        = 4;
	const operation_div          = 5;
	const operation_multi         = 6;
	const operation_point        = 7;
	const operation_equal        = 8;
	const operation_semicolon    = 9;
	const operation_comma        = 10;
	const operation_less         = 11;
	const operation_less_equal    = 12;
	const operation_more         = 13;
	const operation_more_equal     = 14;
	const operation_question     = 15;
	const operation_notequal     = 16;
	const operation_or           = 20;
	//const operation_xor        = 21;
    const operation_and          = 22;
	const operation_not          = 23;
	const operation_new          = 25;
	const operation_open_sq_bracket  = 26;
	const operation_close_sq_bracket = 27;
	
	//Ключевые слова(keywords)
	const keyword_undefined = 0; 
	const keyword_true   = 1; 
	const keyword_false  = 2; 
	const keyword_if     = 3; 
	const keyword_then   = 4; 
	const keyword_elseif = 5; 
	const keyword_else   = 6; 
	const keyword_endif  = 7; 
	const keyword_while  = 8; 
	const keyword_for    = 9; 
	const keyword_foreach = 10; 
	const keyword_to     = 11; 
	//const keyword_in     = 12;
	const keyword_from   = 13;
	const keyword_circle = 14; 
	const keyword_end_circle = 15; 
	const keyword_break  = 16;  
	const keyword_continue = 17; 
	const keyword_function = 18; 
	const keyword_procedure = 19; 
	const keyword_end_function = 20; 
	const keyword_end_procedure = 21; 
	const keyword_return  = 22; 
	const keyword_var     = 23; 
	const keyword_chars   = 24; 
	const keyword_export  = 25; 
	const keyword_val     = 26;
    const keyword_null    = 27;


    //Идентификаторы типов - type_identification
	public array $idTypes = array(
		"lng" => array(), // типы на языке в верхнем регистре для поиска	
        "php" => array()  // типы по-английски как будет в коде			
	);
	public int $indexTypesColl = -1;

    /**
     * @var array|array[]
     */
    public array $functions1C = array(
		"lng" => array(),  // функции на языке в верхнем регистре для поиска
		"php" => array()   // функции по-английски как будет в коде
	);
	//Индексы функций модулей
	public int $indexFuncCom = -1;
	public int $indexFuncStr = -1;
	public int $indexFuncNum = -1;
	public int $indexFuncDate = -1;
	public int $indexFuncColl = -1;

	/**
	* Конструктор класса
	* заполняет массив функций для распознания.
	*/
	function __construct($str = ''){

		//Копирование строки кода
		$this->str = $str;

		//Добавление в таблицы общих типов
		$this->indexTypesColl  = $this->AddTypes();

		//Добавление в таблицы общих функций 
		$this->indexFuncCom  = $this->AddModule( php1C_functions_Com,  php1C_functionsPHP_Com);
		$this->indexFuncStr  = $this->AddModule( php1C_functions_String,  php1C_functionsPHP_String);
		$this->indexFuncNum  = $this->AddModule( php1C_functions_Number,  php1C_functionsPHP_Number);
		$this->indexFuncDate = $this->AddModule( php1C_functions_Date, php1C_functionsPHP_Date);
		$this->indexFuncColl = $this->AddModule( php1C_functions_Collections, php1C_functionsPHP_Collections);
        $this->indexFuncColl = $this->AddModule( php1C_functions_File, php1C_functionsPHP_File);
	}

	/**
	* Добавление типов в общую таблицу типов
	*
     * @return int возвращаем верхнюю границу модуля в общем списке
	*/
	private function AddTypes(): int
    {
        //Collection
        foreach (php1C_types_Collection as $value) {
            $this->idTypes['lng'][] = mb_strtoupper($value);
        }
        foreach (php1C_typesPHP_Collection as $value) {
            $this->idTypes['php'][] = $value;
        }

        //File
        foreach (php1C_types_File as $value) {
            $this->idTypes['lng'][] = mb_strtoupper($value);
        }
        foreach (php1C_typesPHP_File as $value) {
            $this->idTypes['php'][] = $value;
        }

        return count($this->idTypes['php']);
	}

    /**
     * Добавление функций модуля в общую таблицу функций
     *
     * @param array $func
     * @param array $funcPHP
     * @return int возвращаем верхнюю границу модуля в общем списке
     */
	private function AddModule( array $func, array $funcPHP ): int
    {
        foreach ($func as $value) {
            $this->functions1C['lng'][] = mb_strtoupper($value);
        }
        foreach ($funcPHP as $value) {
            $this->functions1C['php'][] = $value;
        }
		return count($this->functions1C['php']);
	}

	/**
	* Функции для разбора кода в токен
	*/
    public function eol(): bool
    {
    	return $this->pos >= mb_strlen($this->str); 
    }
	public function current(): string
    {
		if($this->pos==$this->start) return '';
		else return mb_substr($this->str,$this->start,$this->pos-$this->start); 
	}
	public function future(): string
    {
		return mb_substr($this->str,$this->pos); 
	}
//	private function prev(): string
//    {
//		if($this->pos>=0) return mb_substr($this->str,$this->pos-1,1);
//		else return '';
//	}
	private function curr(): string
    {
		if ($this->pos <= mb_strlen($this->str)) return mb_substr($this->str, $this->pos, 1) ;
		else return '';	
	}
	private function next(): string
    {
		if ($this->pos < mb_strlen($this->str)) return mb_substr($this->str, $this->pos+1, 1);
		else return '';	
	}
	private function move($count=1) {
		$this->pos+= $count;
	}
	
	private function matchMove(): bool
    {
        $text = $this->future();
		if( preg_match(php1C_Identifiers, $text, $matches) === 1 ){
			$this->move(mb_strlen($matches[0]));
			return true;
		}
		else return false;
	}
	private function eatWhile(): void
    {
        while ( preg_match('/[0-9]/', $this->curr())) { $this->pos++; }
    }
	private function eatSymbols(): bool
    {
        $start = $this->pos;
        //$pattern = '/['. "\r" .']/';
        $pattern = '/\r/';
		while ( preg_match($pattern, $this->curr())) { $this->pos++; }
		return $this->pos > $start;
	}

	private function eatTo($pattern): bool
    {
		while ( !$this->eol() ){
			$ch = $this->curr();
			$posStr = strpos( $pattern, $ch);
			if( $posStr === false) $this->pos++;
			else {
				$this->pos++;
				return true;	
			}	
		}
		return false;
	}
	private function skipToEndLine(): void
    {
		$this->row += 1;
		$this->col = 1;
        $this->eatTo("\n");
    }

    /**
     * Прочитать очередной токен из строки
     * @throws Exception
     */
	private function readToken(): Token
    {

		if($this->eol()) return new Token(self::type_end_code, self::type_undefined);

		//Съедаем все возвраты каретки
		if( $this->eatSymbols() ){
		     //$this->col += ($this->pos - $this->start);
		 	$this->start = $this->pos;
		    if($this->eol()) return new Token(self::type_end_code, self::type_undefined);
		}
				
		$ch = $this->curr();
		//$prev = $this->prev();

		//Обработка новой строки
		if( $ch === "\n" ){
		    $this->move();
		 	return new Token(self::type_newline, $ch);
		 } 

		if( $ch === " " ){
			$this->move();
			return new Token(self::type_space, $ch);
		}
		
		if( $ch === "\t" ){
		    $this->move();
		 	return new Token(self::type_tablespace, $ch);
		}

        //Обработка комментариев
		if ($ch === '/') {
			if ($this->next() === '/') {
				$this->skipToEndLine();
				return new Token(self::type_comments, $this->current());
			}
		}

	    //Обработка мета символов & или #
		if ($ch === '&' || $ch === '#') {
			$this->skipToEndLine();
			return new Token(self::type_meta, $this->current());
		}

		//Обработка чисел с одной точкой и лидирующей цифрой.
		if( is_numeric($ch) ){
			$this->eatWhile();
			if($this->curr() === '.'){
				$this->move();
				$this->eatWhile();
			} 
			return new Token(self::type_number, $this->current());
		}

	    //Обработка строк
		if($ch === '"') {
			$this->move();
			if( $this->eatTo($ch) ) return new Token(self::type_string, trim($this->current(),'\"'));
			else throw new Exception(php1C_error_LostSymbol . php1C_double_quotes);
		}

		//Обработка дат (всяких неправильных дат типа '19090101-000000' или '19591015T00:00:00') 
		if ($ch === "'") {
			$this->move();
			$ch = $this->curr();
			$value = '';
			while( !$this->eol() ){
				$this->move();
				if($ch === "'"){
					//только правильные даты 191711070000, 194506240000 или 19450509000000
					if( mb_strlen($value) == 14 || mb_strlen($value) == 12 || mb_strlen($value) == 8){
						if( !checkdate(mb_substr($value, 4, 2), mb_substr($value, 6, 2),mb_substr($value, 0, 4))){
							throw new Exception(php1C_error_BadDateType);	
						}
						return new Token(self::type_date, $value);
					}
					else throw new Exception(php1C_error_BadDateType);	
				}
				if(is_numeric($ch))	$value .= $ch;
				$ch = $this->curr();
			}
			throw new Exception(php1C_error_LostSymbol . php1C_single_quotes);
		}

		//обработка операторов
		if ( mb_strpos("()+-/*.=;,<>?[]", $ch) !== false){
			$this->move();
			if($ch=='<'){
				if( $this->curr()=='>'){
				$this->move();
				return new Token(self::type_operator,'<>',self::operation_notequal);
				}
				elseif($this->curr()=='='){
					$this->move();
					return new Token(self::type_operator,'<=',self::operation_less_equal);
				}
			}
			if($ch=='>' && $this->curr()=='='){
				$this->move();
				return new Token(self::type_operator,'>=',self::operation_more_equal);
			}	

			switch($ch){
				case '(': return new Token(self::type_operator, '(', self::operation_open_bracket);
				case ')': return new Token(self::type_operator, ')', self::operation_close_bracket);
				case '+': return new Token(self::type_operator, '+', self::operation_plus);
				case '-': return new Token(self::type_operator, '-', self::operation_minus);
				case '/': return new Token(self::type_operator, '/', self::operation_div);
				case '*': return new Token(self::type_operator, '*', self::operation_multi);
				case '.': return new Token(self::type_operator, '.', self::operation_point);
				case '=': return new Token(self::type_operator, '=', self::operation_equal);
				case ';': return new Token(self::type_operator, ';', self::operation_semicolon);
				case ',': return new Token(self::type_operator, ',', self::operation_comma);
				case '<': return new Token(self::type_operator, '<', self::operation_less);
				case '>': return new Token(self::type_operator, '>', self::operation_more);
				case '?': return new Token(self::type_operator, '?', self::operation_question);
				case '[': return new Token(self::type_operator, '[', self::operation_open_sq_bracket);
				case ']': return new Token(self::type_operator, ']', self::operation_close_sq_bracket);
			}
			throw new Exception(php1C_error_UndefineOperator.' '.$ch);	
		}
	    
		//Обработка переменных, ключевых слов или функций
		if( $this->matchMove()){
			
			$current = mb_strtoupper($this->current());
			if($current == php1C_type_New ) return new Token(self::type_operator, php1C_type_New, self::operation_new);
			elseif($current == php1C_OR ) return new Token(self::type_operator, php1C_OR, self::operation_or);
			elseif($current == php1C_AND ) return new Token(self::type_operator, php1C_AND, self::operation_and);
			elseif($current == php1C_NOT ) return new Token(self::type_operator, php1C_NOT, self::operation_not);	
			elseif($this->curr() == '('){
				//Идентификатор типа с аргументами
				$key = array_search($current, $this->idTypes['lng']);
				if( $key !== false ) return new Token(self::type_identification, $this->idTypes['php'][$key], $key);
				$this->move();
				//Общая функция на языке
				$key = array_search($current.'(', $this->functions1C['lng']);
				if( $key !== false ) return new Token(self::type_function, $current, $key); 
				else{
					//нераспознанные функции переводим на английский
					return new Token(self::type_extinction, str_replace(php1C_LetterLng, php1C_LetterEng, $current));
				}
				//throw new Exception( php1C_error_UndefinedFunction.' ('.$current.')');
		    } 
			else{
				//Идентификатор без аргументов
				$key = array_search($current, php1C_Keywords);
				if( $key !== false ) return new Token(self::type_keyword, $current, $key);
				else{
					//Идентификатор типа без скобок
					$key = array_search($current, $this->idTypes['lng']);
					if( $key !== false ) return new Token(self::type_identification, $current, $key);
					//Нераспознанные переменные
					return new Token(self::type_variable, $current); //Токены на русском и английском
				} 
			}
		}
	    $this->move();
	    throw new Exception( php1C_error_UndefineSymbol.' (\''.$ch.'\';code='.ord($ch).')' );
	}

	/**
	* Записать массив токенов из строки кода
	*/
	public function CodeToTokens(){

		try{	
			unset($this->tokens);
			$this->tokens = array();
			$this->start = 0;
			$this->pos = 0;
			$token = $this->readToken();

			//echo '+10';

			$key = 0;
			while( $token->type !== self::type_end_code ){

				//echo ">".$token->context;
				$token->col = $this->col;
				$token->row = $this->row;
				if($token->type !== self::type_newline) $this->col += ($this->pos - $this->start);
				else{
					$this->col = 1;
					$this->row++;
				} 
				$this->start = $this->pos;
				$this->tokens[$key] = $token;
				$token = $this->readToken(); 
				$key++;
			}
			$this->tokens[$key] = new Token(self::type_end_code);
			$this->tokens[$key]->col = $this->col;
			$this->tokens[$key]->row = $this->row;
			$this->start = 0;
			$this->pos = 0;
			return true;
		}
		catch (Exception $e) {
			return ("{(".$this->row.",".$this->col.")}: ".$e->getMessage()."\n"); //стиль ошибки 1С
		}
	}
}


