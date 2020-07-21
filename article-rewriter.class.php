<?php 

//START DATE: 20 JULY 2020


class ArticleReWriter {




	public $minWordLength;

	public $inputContent;

	public $outputContent;

	public $url;

	public $html;

	public $dom;

	public $randomResults;


	public $maxCharsAllowed;

	//if user add some words that needs to be ignored in a paragraph
	public $ignoreWords = array();



	public function __construct($randomResults = false, $ignoreWords = false){


		//word length to rewrite
		$this->minWordLength = 5;

		//when user ask to rewrite again, then it will get random 
		$this->randomResults = $randomResults;

		//maximum characters allowed per request
		$this->maxCharsAllowed = 1000;

		//ignore words
		if( $ignoreWords )  $this->ignoreWords = explode(',', $ignoreWords);


		$this->url = "https://www.thesaurus.com/browse/";
	}


	public function getWordsToReWrite(){


		$result 	= array();
		if( strlen($this->inputContent) > 0  ){

			$ex = explode(" ", $this->inputContent);
			for( $i = 0; $i < count($ex); $i++ ){
				if( strlen($ex[$i]) >= $this->minWordLength ){
					array_push($result, $this->cleanWord($ex[$i]));
				}
			}
		}

		return $result;
	}


	public function getResults(){


		$words 	 = $this->getWordsToReWrite();
		for( $i = 0; $i < count($words); $i++ ){
			if(!in_array($words[$i], $this->ignoreWords)){
				$synonyms 		= $this->getSynonyms($words[$i]);
				$totalSynonyms	= count($synonyms);
				if( $totalSynonyms !== 0 ){

					if( $this->randomResults == false ){
						//First and much similar word
						$synonym = $synonyms[0]['term'];
					} else {
						//Pick random index
						$randomIndex = rand(0,$totalSynonyms - 1);
						$synonym = $synonyms[$randomIndex]['term'];
					}
					
					$this->inputContent = str_replace($words[$i], $synonym, $this->inputContent);

				}
			}

		}
		return $this->inputContent;
	}


	public function initCurl($url){

		$config['useragent'] = 'Mozilla/5.0 (Windows NT 6.1; rv:19.0) Gecko/20100101 Firefox/19.0';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_USERAGENT, $config['useragent']);
		// Execute request
		$result = curl_exec($ch);
		//handling error
		curl_close($ch);

		return $result;
	}

	private function initDOM(){
		
		//initializing DOM Document
		$this->dom = new DOMDocument('1.0');
		//Loading main HTML 
		@$this->dom->loadHTML(mb_convert_encoding($this->html, 'HTML-ENTITIES', 'UTF-8'));
	}


	public function getSynonyms($word){

		$url = $this->url.$word;
		$this->html = $this->initCurl($url);

		$this->initDOM();
		$data = array();
		$finder = new DomXPath($this->dom);
    	$ul = $finder->query("//*[contains(@class, 'css-17d6qyx-WordGridLayoutBox et6tpn80')]")->item(0);
		$j = 0;	
		//lesser $similarity the higher Similarity
		$similarity = 0;		
		if (isset($ul)) {
			foreach($ul->childNodes as $el ):
				if( isset($el->nodeValue) ){
					$similarity++;
					$data[$j]['similarity'] = $similarity;
					$data[$j]['term']		= $el->nodeValue;
					$j++;
				}
			endforeach;
		}

		return $data;
	}


	private function isContain($string,$keyword){

		 return strpos($string, $keyword) !== false;

	}



	public function cleanWord($word){

		$word = str_replace(".", "", $word);
		$word = str_replace("'", "", $word);
		$word = str_replace(",", "", $word);
		return $word;
	}



	public function inputContent($content){

		$this->inputContent = $content;
	}

	public function isExcessiveChars(){

		return strlen(preg_replace('/[^a-zA-Z]/', '', $this->inputContent) )   >= $this->maxCharsAllowed;
	}


}





 ?>