<?php
header('Content-Type: text/html; charset=utf-8');
/*
1. zadanie
------------------ 
Napisz funkcję, która:	- zbierze możliwe informacje o użytkowniku z $_SERVER	- na ich podstawie utworzy ich stosunkowo unikalny
identyfikator (maks 32 znaki) i go zwróci
*/
function userUniqId(){
	return md5($_SERVER);
}
$userId=userUniqId();
//

/*
2. zadanie
------------------ 
Napisz funkcję, która przyjmie string, a zwróci ilość powtórzeń 3 takich samych znaków pod rząd. Przykładowo dla stringa 'deoooxxa' zwróci wartość 1, a dla stringa 'sificccosaaae' zwróci 2
*/
function sameThreeLettersCount($txt){
	$count = 0;
	for($i = 0; $i<(strlen($txt)-2); $i++) {
		if($txt[$i]==$txt[$i+1] && $txt[$i]==$txt[$i+2]) {
			$count++;
			$i+=2; // jeśli dla stringa 'aaaa' wynik powinien wynosić 2 (3 takie same znaki pod rząd występują dwa razy), ta linia jest zbędna
		}
	}
	return $count;
}
//$sameThreeLettersCount = sameThreeLettersCount('sificccosaaae');

/*
3. zadanie
------------------
Przyjmijmy, że alfabetowi odpowiada skala odcieni szarokości, tzn: literze alfabetu 'a' odpowiada kolorbiały (#FFFFFF) natomiast ostatniej
literze alfabetu 'z' odpowiada kolor czarny (#000000) oraz każda litera pomiędzy a i z ma odpowiadający kolor ze skali. Napisz funkcję, która
odpowiednio przydzieli kolor do każdej litery napisu z parametru i pokaże napis w skali odcieni szarości. Można skorzystać ze HTML:)
*/
function colorString($text){
	$colorString = '';
	$array = Array('a','ą','b','c','ć','d','e','ę','f','g','h','i','j','k','l','ł','m','n','ń','o','ó','p','q','r','s','ś','t','u','v','w','y','z','ź','ż');
	$array_b = Array('A','Ą','B','C','Ć','D','E','Ę','F','G','H','I','J','K','L','Ł','M','N','Ń','O','Ó','P','Q','R','S','Ś','T','U','V','W','Y','Z','Ź','Ż');

	$mnoznik =  255/(((count($array)-1)>0)?(count($array)-1):1); //maksymalna wartość pojedynczej składowej rgb podzielona przez ilość elementów tablicy (ilość odcieni)
	for($i =0; $i<mb_strlen($text, 'utf-8'); $i++) {
		$letter = mb_substr($text, $i, 1, "UTF-8");
		$position_letter_s = array_search($letter,  $array);
		$position_letter_b = array_search($letter,  $array_b);
		if($position_letter_s!==false || $position_letter_b!==false) {
			$color_dec = round($mnoznik*(count($array)-1-(($position_letter_s!==false)?$position_letter_s:$position_letter_b)));
			$composite_color = (($color_dec<17)?"0":"").dechex($color_dec);
			$color = $composite_color.$composite_color.$composite_color;
			$colorString .= "<span style='color: #".$color.";'>".$letter."</span>";
		} else {
			$colorString .= $letter;
		}
	}
	return $colorString;
}

//$colorString = colorString('napis, który zostanie pokazany w skali odcieni szarości').'a';

/*
4. zadanie 
------------------
Napisz klasę, która przyjmuje w konstrukturze treść artykułu jako string (przykładowy sring jest załączony w pliku article.txt) oraz umożliwi za pomocą kolejnych metod:
- zwrócenie ilości znaków alfanumerycznych w artykule
- zwrócenie ilości słów w artykule
- zamianę w treści artykułu podanego w 1. parametrze słowa na słowo zawarty w 2. parametrze
- zwrócenie tablicy zawierającej każde słowo, które wystąpiło w artykule wraz z ilością powtórzeń. tablica powinna być posortowana aby w pierwszej kolejności zawierała najczęściej występujące słowa
- zwrócenie szacunkowego czasu potrzebnego na przeczytanie artykułu w sekundach
- zwrócenie artykułu ze zdaniami losowo zamienionymi miejscami 
- pokolorowanie artykułu, korzystając z pomocy funkcji z poprzedniego zadania

*/
class Article{
	public $tresc;
	public function __construct($tresc){
		$this->tresc = $tresc;
	}
	
	public function countAlphanumeric() {
		return mb_strlen(preg_replace('#[^A-Za-z0-9ęóąśłżźćńĘÓĄŚŁŻŹĆĘŃ]#i','', $this->tresc), 'utf-8');
	}
	
	public function countWords() {
		return count(explode(" ", $this->tresc));
	}
	
	public function replaceWords($word1, $word2, $caseSensitive = false) { // $caseSensitive - czy uwzględniana jest wielkość liter
		$temp = preg_split("/[\r\n,. ]/", $this->tresc);
		$tresc = '';
		foreach($temp as $word) {
			if(!empty($word)) {
				$temp_word = trim(preg_replace('#[\r\n,. ]#i','', $word));
				if((!$caseSensitive && (strtoupper($temp_word)==strtoupper($word1))) || ($caseSensitive && ($temp_word==$word1))) {
					if($caseSensitive) {
						$tresc .= str_replace($word1,$word2,trim($word))." "; 
					} else {
						$tresc .= str_replace(strtoupper($word1),$word2,strtoupper(trim($word)))." "; 
					}
				} else {
					$tresc .= $word." "; 
				}
			}
		}
		return trim($tresc);
	}
	
	public function wordsAppearances() {
		$array = Array();
		$temp = preg_split("/[\r\n,. ]/", $this->tresc);
		foreach($temp as $word) {
			if(!empty($word)) {
				$array[strtolower($word)] = ((isset($array[strtolower($word)]))?$array[strtolower($word)]+1:1);
			}
		}
		arsort($array);
		return ($array);
	}
	
	public function averageSpeedReading($words_per_minute = 200) {
		return ($this->countWords()/$words_per_minute)*60;
	}
	
	public function randSentence() {
		$temp = explode(". ", $this->tresc);
		shuffle($temp);
		return implode(". ", $temp);
	}
	
	public function colorArticle() {
		return colorString($this->tresc);
	}
}

if(file_exists("article.txt")) {
	$txt = file_get_contents("article.txt");
} else {
	$txt = "Nie udało się odszukać pliku article.txt";
}

/* Testowe dane 

echo "<h1>Test</h1><hr/>";
echo '<h3>Zadanie 1:</h3> $userId = '.$userId;
echo '<h3>Zadanie 2:</h3> $sameThreeLettersCount = '.sameThreeLettersCount('sificccosaaae');
echo "<h3>Zadanie 3:</h3>".colorString('napis, który zostanie pokazany w skali odcieni szarości');
echo "<h3>Zadanie 4:</h3>";
$a = new Article($txt);
echo "Ilość znaków alfanumerycznych: ".$a->countAlphanumeric()."<br><hr>";
echo "Ilość słów: ".$a->countWords()."<br><hr>";
echo "Zmiana:<BR>".$a->replaceWords("środku", "xyz", false)."<br><hr>";
echo "Ilość słów:<BR>";
print_r($a->wordsAppearances());
echo "<br><hr>";
echo "Szybkość czytania:<BR>". $a->averageSpeedReading()."<br><hr>";
echo "Zmianiona kolejność zdań:<BR>". $a->randSentence()."<br><hr>";
echo "Kolorowanie:<BR>".$a->colorArticle()."<br><hr>";

 Testowe dane */
?>