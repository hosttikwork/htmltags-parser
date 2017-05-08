<?php

class Parser {
        protected $get_content;
        protected $error=false;

		 /**
		 * Получение html тегов
		 *
		 * @param  string $url адрес страницы
		 * @param  integer $timeout продолжительность соединения в секундах
		 * @return array отсортированный массив с html тегами
		 */
        public function getTags()
        {
        	$input_array = preg_match_all("(\<(/?[^>]+)>)",$this->get_content,$out);
		  	$get_array=$out[1];
		  	foreach ($get_array as $key => $value) {

		  		if(preg_match("/[^a-z,A-Z]/",$value[0])) {
		  			unset($get_array[$key]);
		  		}else {
		  			$get_array[$key]=split(" ", $value)[0];
		  		}
		  	}

		  	$sorted_mass=array_count_values($get_array);
		  	return $sorted_mass;
        }

        /**
		 * Вывод результата в формате json
		 */
        public function printResult() 
        {
		  	if($this->error!==false) {
		  		$json_err=json_encode($this->error);
		  		echo $json_err;
		  	}else {
		  		$res=array();
		  		$get_tags=$this->getTags();
		  		$res['tags']=$get_tags;
		  		$json=json_encode($res);
		  		echo $json;
		  	}
		}
}
 
class LinkParser extends Parser {

    protected $get_content = null; 
    protected $timeout=30;

    /**
	* Получение контента страницы
	* @param  string $url адрес страницы
	*/
	function __construct($url)
	{
		$c = curl_init();
	    curl_setopt($c, CURLOPT_URL, $url);
	    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($c, CURLOPT_CONNECTTIMEOUT, $this->timeout);

	    $curl_result = curl_exec($c);

	    if (curl_error($c)) {
	      $this->error = 'Ошибка curl: ' . curl_error($c);
	    }

	    curl_close($c);

	  
	   $this->get_content=$curl_result;
	}
	
}
 
class FileParser extends Parser {

	/**
	* Получение контента файла
	* @param  string $param директория файла
	*/
	function __construct($param)
	{
		$result=file_get_contents($param);
		if($result==false) {
			$this->error="Файл не читается";
		} else {
			$this->get_content=$result;
		}
		
	}
}

class DebugParser extends Parser {

	/**
	* Отображение данных полученных на вход
	* @param  array $param название класса и входные параметры
	*/
	function __construct($param)
	{
		$this->error=$param;		
	}
}

class ParserFactory
{ 
	protected static $debug=false;

	/**
	* Создание экземпляра класса
	*
	* @param  string $type название класса
	* @param  $param входные параметры могут быть строкой и массивом
	* @return экземпляр класса
	*/
    public static function createParser($type,$param)
    {
    	if(self::$debug==false) {
    	
	        $baseClass = 'Parser';
	        $targetClass = ucfirst($type).$baseClass;
	 
	        if (class_exists($targetClass) && is_subclass_of($targetClass, $baseClass)) {
	            return new $targetClass($param);
	        } else {
	            throw new Exception("Parser type '$type' is not recognized.");
	        } 
   		}else {
   			$debug_arr=array('type'=>$type,'param'=>$param);
   			return new DebugParser($debug_arr);

   		}
    }
}


$allowed = array("link"=>"link", "file"=>"file");
$key     = array_search($_POST['action'],$allowed);
$action = $allowed[$key];

switch($action)
{ 
	case "link" :
		$param=filter_var($_POST['url'], FILTER_VALIDATE_URL);
		break;
	case "file" :
		$data = array();
 
		
		$error = false;
		$files = array();
		 
		$uploaddir = './uploads/'; 
		 
		if( ! is_dir( $uploaddir ) ) mkdir( $uploaddir, 0777 );
		 
		foreach( $_FILES as $file ){
		    if( move_uploaded_file( $file['tmp_name'], $uploaddir . basename($file['name']) ) ){
		        $files[] = realpath( $uploaddir . $file['name'] );
		    }else{
		        $error = true;
		    }
		}
		 
		$data = $error ? array('error' => 'Ошибка загрузки файлов.') : array('files' => $files );
		$param=$uploaddir . basename($file['name']);
		break;
	default : 
		$param=null;
	break;
}

ParserFactory::createParser($action,$param)->printResult();

 