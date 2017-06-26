<?php

namespace App\Helpers;

use App\Models\Category;
use App\Models\Word;
use Symfony\Component\Finder\Iterator\RecursiveDirectoryIterator;

class Common
{

    public static function findMin($elements)
    {
        $minElementValue = PHP_INT_MAX;
        $minElementKey = -1;
        foreach ($elements as $key => $value) {
            if ($value < $minElementValue) {
                $minElementValue = $value;
                $minElementKey = $key;
            }
        }
        $minElement['key'] = $minElementKey;
        $minElement['value'] = $minElementValue;

        return $minElement;
    }

    public static function writeToCsv($content, $filePath, $writingMode = 'a+', $hasTitleRow = 1)
    {

        $file = fopen($filePath, $writingMode);

        if ($hasTitleRow) {
            $firstRow = reset($content);
            if ($firstRow) {
                $columnTitles = array_keys($firstRow);
                fputcsv($file, $columnTitles);
            }
        }

        foreach ($content as $row) {
            fputcsv($file, $row);
        }

        fclose($file);
    }

    public static function readFromCsv($filePath)
    {
        $rows = [];
        if (($handle = fopen($filePath, "r")) !== false) {
            while (($row = fgetcsv($handle)) !== false) {
                $rows[] = $row;
            }
        }

        return $rows;
        //return array_map('fgetcsv', file($filePath));
    }

    public static function sanitizeString($string)
    {
        $string = strip_tags($string);
        $string = preg_replace("/&#?[a-z0-9]{2,8};/i", "", $string);

        return $string;
    }

    public static function sortTwoDimensional(&$array)
    {
        usort($array, array('self', 'compare'));
    }

    public static function compare($a, $b)
    {
        if ($a["count"] == $b["count"]) {
            return 0;
        }

        return ($a["count"] > $b["count"]) ? -1 : 1;
    }

    public static function countStrOccurrences($string, $substring)
    {
        $count = 0;

        while (true) {
            $pos = strpos($string, $substring);
            if ($pos === false) {
                break;
            }
            $string = substr($string, $pos + 1);
            $count++;
        }

        return $count;
    }

    public static function getDirFiles($dirPath, $format = "csv")
    {
        $Directory = new \RecursiveDirectoryIterator($dirPath);
        $Iterator = new \RecursiveIteratorIterator($Directory);
        $filesIterator = new \RegexIterator($Iterator, '/^.+\.'.$format.'$/i', \RecursiveRegexIterator::GET_MATCH);
        $filesArray = iterator_to_array($filesIterator);
        $files = [];
        foreach ($filesArray as $item) {
            $files[] = $item[0];
        }

        return $files;
    }

    public static function makeDirectory($dir, $mode = "0777")
    {
        if (!file_exists($dir)) {
            mkdir($dir, $mode, true);
        }
    }
}