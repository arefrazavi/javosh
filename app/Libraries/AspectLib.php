<?php

namespace App\Libraries;

use App\Helpers\Common;
use App\Helpers\Tokenizer;
use App\Models\Aspect;
use App\Models\Category;
use App\Models\Sentence;
use App\Models\Word;

class AspectLib
{
    /**
     * @return string
     */
    public static function storeKeywords()
    {
        $categoryId = 4;
        $aspects = Aspect::fetchAspects("*", "category_id = $categoryId");
        foreach ($aspects as $aspect) {
            $keywords = [];
            $filePath = base_path('data/aspects/Mobile-Phone/' . strval($aspect->id) . '_seed.csv');
            $keywordsFile = Common::readFromCsv($filePath);
            dump($keywordsFile);
            foreach ($keywordsFile[0] as $key => $keyword) {
                $keyword = trim($keyword);
                $weight = floatval($keywordsFile[1][$key]);
                if (!isset($keywords[$keyword]) || ($keywords[$keyword] < $weight)) {
                    $keywords[$keyword] = $weight;
                }
                print_r("\n Word: " . $keyword . " , weight: " . $weight . "<br> \n");
            }

            $aspect->keywords = serialize($keywords);
            $aspect->save();
        }

        print_r("\n********END of storeKeywords******\n");
    }

    public static function retrieveAspectIds($categoryId = 0, $categoryTitle = '')
    {
        $category = Category::fetch($categoryId, $categoryTitle);
        $ancestors = Category::fetchAncestors($category->id);
        $aspectIds = [];

        foreach ($ancestors as $ancestor) {
            foreach ($ancestor->aspects as $aspect) {
                $aspectIds[$aspect->id] = $aspect->id;
            }
        }

        foreach ($category->aspects as $aspect) {
            $aspectIds[$aspect->id] = $aspect->id;
        }

        return $aspectIds;
    }

    public static function getAspects($categoryId = 0, $categoryTitle = '')
    {
        $category = Category::fetch($categoryId, $categoryTitle);
        $ancestors = Category::fetchAncestors($category->id);
        $aspects = [];

        foreach ($ancestors as $ancestor) {
            foreach ($ancestor->aspects as $aspect) {
                $aspect->keywords = unserialize($aspect->keywords);
                $aspects[$aspect->id] = $aspect;
            }
        }

        foreach ($category->aspects as $aspect) {
            $aspect->keywords = unserialize($aspect->keywords);
            $aspects[$aspect->id] = $aspect;
        }

        return $aspects;
    }


    /**
     * Find frequent item sets as pot aspects
     * Apriori Algorithm
     */
    public static function findFrequentItemSets()
    {

        $frequentItemSets = [];
        $minSupport = 0.01;
        $sentencesTexts = [];
        $prosAndCons = [];
        $aspects = [];


        $words = Word::fetchWords("*", 'pos_tag IS NULL', PHP_INT_MAX, 0, 'count', 'DESC');
        $sentences = Sentence::fetchSentences();
        $sentencesCount = sizeof($sentences);

        foreach ($sentences as $sentence) {
            $text = Common::sanitizeString($sentence->text);
            $sentencesTexts[$sentence->id] = $text;
        }

        //Initialization - Itemset size 1
        $frequentItemSets[1] = [];
        foreach ($words as $word) {
            $wordValue = $word->value;
            $probability = $word->count / $sentencesCount;
            //Break when it comes to below min support, because words are in descending order of theirs counts,
            if ($probability < $minSupport) {
                break;
            }
            $frequency = 0;
            $sentenceIds = [];
            foreach ($sentencesTexts as $sentenceId => $sentenceText) {
                if (strpos($sentenceText, $wordValue) !== false) {
                    $frequency++;
                    $sentenceIds[] = $sentenceId;
                }
            }
            $support = $frequency / $sentencesCount;

            if ($support >= $minSupport) {
                print_r("\n frequent item:" . $wordValue . "\n");
                $frequentItemSets[1][$wordValue]['support'] = $support;
                $frequentItemSets[1][$wordValue]['sentenceIds'] = $sentenceIds;
                $frequentItemSets[1][$wordValue]['frequency'] = $frequency;
            }
        }
        $preSize = 1;
        $currentSize = 2;
        while ($currentSize <= 3) {

            print_r("Current size :" . $currentSize . "<br> \n");

            if (!isset($frequentItemSets[$preSize]) || empty($frequentItemSets[$preSize])) {
                break;
            }

            //$potFrequentItems = [];
            foreach ($frequentItemSets[$preSize] as $frequentItem1 => $info1) {
                $itemWords = explode(" ", $frequentItem1);
                foreach ($frequentItemSets[1] as $itemWord => $info2) {
                    if (in_array($itemWord, $itemWords)) {
                        continue;
                    }

                    $potFrequentItem = $frequentItem1 . " " . $itemWord;
                    print_r("\n pot frequent item:" . $potFrequentItem . "\n");

                    $frequency = 0;
                    $sentenceIds = [];
                    foreach ($info1['sentenceIds'] as $sentenceId) {
                        if (strpos($sentencesTexts[$sentenceId], $potFrequentItem) !== false) {
                            $frequency++;
                            $sentenceIds[] = $sentenceId;
                        }
                    }

                    $support = $frequency / $sentencesCount;
                    if ($support >= $minSupport) {
                        $frequentItemSets[$currentSize][$potFrequentItem]['support'] = $support;
                        $frequentItemSets[$currentSize][$potFrequentItem]['sentenceIds'] = $sentenceIds;
                        $frequentItemSets[$currentSize][$potFrequentItem]['frequency'] = $frequency;
                        print_r("\n frequent item:" . $potFrequentItem . "\n");

                    }
                }
            }

            $preSize = $currentSize;
            $currentSize++;

        }


        $frequentItems = [];
        foreach ($frequentItemSets as $frequentItemSet) {
            foreach ($frequentItemSet as $item => $info) {
                $frequentItems[] = [
                    'item' => $item,
                    'support' => $info['support'],
                    'frequency' => $info['frequency'],
                    'sentenceIds' => serialize($info['sentenceIds'])
                ];
            }
        }

        $filePath = base_path('data/aspects/Mobile-Phone/frequent_item_sets.csv');
        $writingMode = 'w';
        Common::writeToCsv($frequentItems, $filePath, $writingMode);

    }

    /**
     *
     */
    public static function findDynamicAspects()
    {
        $minAdjSupport = 0.01;

        $filePath = base_path('data/aspects/Mobile-Phone/frequent_item_sets.csv');
        $frequentItems = Common::readFromCsv($filePath);
        unset($frequentItems[0]); // remove title row

        $sentences = Sentence::fetchSentences();
        $sentencesWords = [];
        foreach ($sentences as $sentence) {
            //$text = Common::sanitizeString($sentence->text);
            $sentencesWords[$sentence->id] = WordLib::extractWords($sentence->text);
        }
        $adjWords = Word::fetchWords("*", "pos_tag = 'ADJ'");

        $aspects = [];
        foreach ($frequentItems as $frequentItem) {
            $potAspect = $frequentItem[0];
            $support = $frequentItem[1];
            $frequency = $frequentItem[2];
            $sentenceIds = unserialize($frequentItem[3]);

            print_r("Potential Aspect: " . $potAspect . "<br> \n");

            $adjCount = 0;
            foreach ($sentenceIds as $sentenceId) {
                $potAspectPos = array_search($potAspect, $sentencesWords[$sentenceId]);

                foreach ($adjWords as $adjWord) {
                    $adj = $adjWord->value;
                    print_r("Adj:" . $adj . " <br> \n");

                    $adjPos = array_search($adj, $sentencesWords[$sentenceId]);

                    if ($adjPos !== false) {
                        $posDiff = $potAspectPos - $adjPos;
                        if (-3 <= $posDiff && $posDiff <= -1) {
                            $adjCount++;
                        }
                    }
                }

            }

            $adjSupport = $adjCount / $frequency;

            print_r("\n" . $adjSupport . "\n");
            if ($adjSupport >= $minAdjSupport) {
                print_r("Aspect: " . $potAspect . "<br> \n");
                $aspects[] = [
                    'word' => $potAspect,
                    'support' => $support,
                    'adjSupport' => $adjSupport,
                    'frequency' => $frequency
                ];
            }
        }

        $filePath = base_path('data/aspects/Mobile-Phone/potential_dynamic_aspects.csv');
        $writingMode = 'w';
        Common::writeToCsv($aspects, $filePath, $writingMode);

    }


}