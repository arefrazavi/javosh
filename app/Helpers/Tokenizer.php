<?php

namespace App\Helpers;

use App\Models\Word;

class Tokenizer
{
    public static $sentenceDelimiters = [
        "?" => "?",
        "؟" => "؟",
        "." => ".",
        "؛" => "؛",
        ";" => ";",
        "!" => "!",
        "\n" => "\n",
        "\r" => "\r"
    ];

    public static $pointsDelimiters = [
        "?" => "?",
        "؟" => "؟",
        "." => ".",
        "؛" => "؛",
        "!" => "!",
        "\n" => "\n",
        "\r" => "\r",
        "," => ",",
        "،" => "،",
        "-" => "-",
        "(" => "(",
        ")" => ")",
        "[" => "[",
        "]" => "]",
        "\"" => "\"",
        "'" => "'",
    ];

    public static $conjunctions = [
        "و" => "و",
        "یا" => "یا",
        "پس" => "پس",
        "بنابراین" => "بنابراین",
        "لذا" => "لذا",
        "تا" => "تا",
        "زیرا" => "زیرا",
        "چون" => "چون",
        "اگر" => "اگر",
        "بلکه" => "بلکه",
        "اما" => "اما",
        "ولی" => "ولی",
        "لیکن" => "لیکن",
        "خواه" => "خواه",
        "که" => "که",
    ];

    public static $wordDelimiters = [
        "?" => "?",
        "؟" => "؟",
        "." => ".",
        ":" => ":",
        "؛" => "؛",
        ";" => ";",
        "!" => "!",
        ',' => ',',
        '،' => '،',
        '(' => '(',
        ')' => ')',
        '[' => '[',
        ']' => ']',
        '"' => '"',
        "'" => "'",
        "\\" => "\\",
        "/" => "/",
        "\n" => "\n",
        "\r" => "\r",
        '^' => '^',
        "#" => "#",
        "@" => '@',
        "-" => "-",
        "_" => "_",
        "*" => "*",
        "`" => "`",
        "<" => "<",
        ">" => ">",
        "%" => "%",
        "&" => "&",
        "$" => "$",
        "+" => "+",
    ];

    /**
     * Divide the given text into array of words based on specific delimiters
     * @param $text
     * @return array $words
     */
    public static function tokenize($text)
    {
        foreach (self::$wordDelimiters as $delimiter) {
            $text = str_replace($delimiter, " ", $text);
        }
        $words = explode(" ", $text);
        //$words = preg_split('/ |؛|؟|\.|\?|;|!|\n|\r/', $text);
        $words = array_map('trim', $words);//trim toke+nized words
        $words = array_filter($words, array(__CLASS__, 'removeBlankElement')); //remove blank elements
        $words = array_values($words);

        return $words;
    }

    public static function segmentize($text)
    {
        foreach (self::$sentenceDelimiters as $sentenceDelimiter) {
            $text = str_replace($sentenceDelimiter, ". ", $text);
        }
        $sentences = explode(". ", $text);
        $sentences = array_map('trim', $sentences);//trim toke+nized words
        $sentences = array_filter($sentences, array(__CLASS__, 'removeBlankElement')); //remove blank elements
        $sentences = array_values($sentences);

        return $sentences;
    }

    /**
     * Determine if a string is blank or not
     * @param string $string
     * @return bool
     */
    public static function removeBlankElement($string)
    {
        if (is_array($string)) {
            return $string['word'] !== '';
        }
        return $string !== '';
    }

    /**
     * @param $words
     */
    public static function removeStopWords(&$words)
    {
        $wordMinimumLength = 2;
        $stopWordsArray = self::retrieveStopWords();
        foreach ($words as $key => $word) {
            if (isset($stopWordsArray[$word]) || iconv_strlen($word) < $wordMinimumLength) {
                unset($words[$key]);
            }
        }
    }

    /**
     * @return mixed
     */
    public static function retrieveStopWords()
    {
        $stopWords = Word::fetchStopWords();
        $stopWordsArray = [];
        foreach ($stopWords as $stopWord) {
            $stopWordsArray[$stopWord->value] = $stopWord->value;
        }
        return $stopWordsArray;
    }

}