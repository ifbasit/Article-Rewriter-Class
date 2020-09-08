<?php 


/**
* @author: Abdul Basit
* @email: ifbasit@gmail.com
* @Website: https://www.toolsbug.com
* @title: A PHP Article Rewriter Class
* 
* 
*/

class ArticleReWriter {




	public $minWordLength;

	public $inputContent;

	public $outputContent;

	public $url;


	public $randomResults;


	public $maxCharsAllowed;

	//if user add some words that needs to be ignored in a paragraph
	public $ignoreWords = array();



	public function __construct($randomResults = false, $ignoreWords = false){


		//word length to rewrite
		$this->minWordLength 					= 5;

		//when user ask to rewrite again, then it will get random 
		$this->randomResults 					= $randomResults;

		//maximum characters allowed per request
		$this->maxCharsAllowed 					= 1000;

		//ignore words
		if( $ignoreWords )  $this->ignoreWords 	= explode(',', $ignoreWords);


		$this->url = "https://relatedwords.org/api/related?term=";
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
						$synonym 		= $synonyms[0]['term'];
					} else {
						//Pick random index
						$randomIndex 	= rand(0,$totalSynonyms - 1);
						$synonym 		= $synonyms[$randomIndex]['term'];
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


	public function getSynonyms($word){

		$url 	= $this->url.$word;
		$result = json_decode($this->initCurl($url));
		$totalSynonyms = count($result);
		$data = array();
		if( $totalSynonyms !== 0 ){
			$similarity = 0;
			for( $i = 0; $i < $totalSynonyms; $i++ ){
				$data[$i]['similarity'] = $result[$i]->score;
				$data[$i]['term'] 		= $result[$i]->word;
			}

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
