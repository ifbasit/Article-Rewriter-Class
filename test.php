<?php 


include 'article-rewriter.class.php';

//Get Random Results 
	//$ArticleReWriter = new ArticleReWriter(true);

//Ignore some words (CSV) and random results
	//$ArticleReWriter = new ArticleReWriter(true,'foods,healthy');

//Default
$ArticleReWriter = new ArticleReWriter();
$content = "Eating a variety of foods, regularly, and in the right amounts is the best formula for a healthy diet";
$ArticleReWriter->inputContent($content);
if( $ArticleReWriter->isExcessiveChars() ){
	echo "1000 Characters Limit";
} else {

	$ArticleReWriter->getWordsToReWrite();
	print_r($ArticleReWriter->getResults());
}




 ?>