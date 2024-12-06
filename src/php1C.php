<?php
declare(strict_types=1);

namespace Sikuda\Php1c;

class php1C
{
    /**
     * Запуск получения кода PHP
     *
     * @param string $buffer строка код для преобразования
     */
    function makeCode(string $buffer){

		$tokenStream = new TokenStream($buffer);
		$resToken = $tokenStream->CodeToTokens();
		if ($resToken !== true) {
			return $resToken; //возврат ошибки разбора
		}
		$this->functions1C = $tokenStream->functions1C;
		$this->keywords = $tokenStream->idTypes;
		$this->tokens = $tokenStream->tokens;

		//var_dump($this->tokens);
		//return "";
		
		try{
			$this->code = '';
			$this->codePHP = '';
			$this->GetChar();
			if($this->Type !== TokenStream::type_end_code){

				$this->continueCode();

				//Вывод результата переменной
			    return $this->codePHP;
			}  
			else return ""; //стиль 1С нет ошибки
		}
		catch (Exception $e) {
			$token = $this->tokens[$this->i_token-1];
    		return (" {(".$token->row.",".$token->col.")}: ".$e->getMessage()); //стиль ошибки 1С
		}
 	}

    /**
     * Запуск получения кода PHP
     *
     * @param string $buffer строка код для преобразования
     * @param string|null $name_var имя переменной для вывода результата выполнения кода
     */
    function evalCode(string $buffer, $name_var=null){

		$tokenStream = new TokenStream($buffer);
		$resToken = $tokenStream->CodeToTokens();
		if ($resToken !== true) {
			return $resToken; //возврат ошибки разбора
		}
		$this->functions1C = $tokenStream->functions1C;
		$this->keywords = $tokenStream->idTypes;
		$this->tokens = $tokenStream->tokens;

		//var_dump($this->tokens);
		//return "";
		
		//Блок преобразования в код php
		try{
			$this->code = '';
			$this->codePHP = '';
			$this->GetChar();
			if($this->Type !== TokenStream::type_end_code){

				$this->continueCode();

                if(isset($name_var)){
					if(fEnglishVariable) $name_var = str_replace(php1C_LetterLng, php1C_LetterEng, $name_var);
					$name_var = mb_strtoupper($name_var);
                }
                else $name_var = "RESULT";    
                
                eval($this->codePHP);
                
                return ${$name_var};
				
			}  
			else return ""; //стиль 1С нет ошибки
		}
		catch (Exception $e) {
			$token = $this->tokens[$this->i_token-1];
    		return (" {(".$token->row.",".$token->col.")}: ".$e->getMessage()); //стиль ошибки 1С
		}
 	}

}