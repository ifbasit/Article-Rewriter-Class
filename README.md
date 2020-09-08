# Article-Rewriter-Class
A PHP Class for Article Rewriter.

## Methods

### Constructor
1. $randomResults is set to false, set it to true if want to get random results on each request
2. $ignoreWords accepts an array of words, that you want to be ignored in rewriting process, e.g $ignoreWords = array('Book','Computer');


### getSynonyms()

This function accepts a word and gives you an array of synonyms of the word provided with similarity

## Properties

### maxCharsAllowed

Maximum characters you want to send in a request, 1000 would be great, default is 1000.

### minWordLength

The minimum word length that should be rewrite, probably you don't want to rewrite the word "is", so it should be 5, default is 5.

### Usage
```php
$ArticleReWriter = new ArticleReWriter();
$content = "Eating a variety of foods, regularly, and in the right amounts is the best formula for a healthy diet";
$ArticleReWriter->inputContent($content);
if( $ArticleReWriter->isExcessiveChars() ){	
  echo "1000 Characters Limit";
} else {	
  print_r($ArticleReWriter->getResults());
}
```

