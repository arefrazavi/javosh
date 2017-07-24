<?php

namespace App\Libraries;

use App\Helpers\Common;
use App\Helpers\Tokenizer;
use App\Models\Aspect;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Sentence;
use App\Models\Word;
use Illuminate\Support\Facades\DB;

class AspectLib
{
    /**
     * @return string
     */
    public static function storeAspects($baseDir)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('aspects')->truncate();
        $categories = Category::fetchCategories();

        foreach ($categories as $category) {
            $basePath = base_path($baseDir . $category->title . "/aspect_keywords/*.csv");
            print_r($basePath."\n");
            $files = glob($basePath);
            $aspectData['category_id'] = $category->id;
            foreach ($files as $fileName) {
                $results = Common::readFromCsv($fileName);
                $aspectData['title'] = trim($results[0][0]);
                $aspectData['type'] = intval($results[0][1]);
                print_r("type: ". $aspectData['type'] . "\n");
                unset($results[0]);
                $keywords = [];
                foreach ($results as $result) {
                    $keyword = trim($result[0]);
                    $weight = $result[1];
                    $keywords[$keyword] = floatval($weight);
                }
                $aspectData['keywords'] = serialize($keywords);
                $aspect = Aspect::updateOrInsert($aspectData);
                print_r("Aspect: " . $aspect->id . " is stored\n");

            }

        }
        print_r("\n********END of storeAspects******\n");
    }

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
     * @param $items
     * @param $transactions
     * @param float $minSupport
     * @return array
     */
    public static function findFrequentItemSets(&$items, &$transactions, $minSupport = 0.01)
    {
        $frequentItemSets = [];
        $transactionsCount = sizeof($transactions);

        //Initialization - Itemset size 1
        $frequentItemSets[1] = [];
        foreach ($items as $key => $item) {
            $frequency = 0;
            $transactionIds = [];
            foreach ($transactions as $transactionId => $transaction) {
                if (strpos($transaction, $item) !== false) {
                    $frequency++;
                    $transactionIds[] = $transactionId;
                }
            }
            $support = $frequency / $transactionsCount;

            if ($support >= $minSupport) {
                print_r("\n frequent item: $item with support $support \n");
                $frequentItemSet['support'] = $support;
                $frequentItemSet['frequency'] = $frequency;
                $frequentItemSet['items'] = [$item => $item];
                $frequentItemSet['transactionIds'] = $transactionIds;
                $frequentItemSets[1][] = $frequentItemSet;

            }
        }
        $preSize = 1;
        $currentSize = 2;
        $sizeOneCount = sizeof($frequentItemSets[1]);
        while ($currentSize <= 3) {
            print_r("Current size : $currentSize \n");
            if (!isset($frequentItemSets[$preSize])) {
                break;
            }

            $frequentItemSetsCount = sizeof($frequentItemSets[$preSize]);
            for ($i = 0; $i < $frequentItemSetsCount; $i++) {
                for ($j = 0; $j < $sizeOneCount; $j++) {
                    $newItem = current($frequentItemSets[1][$j]['items']);
                    //Check if the new item doesn't exist in items of previous frequent item set
                    if (isset($frequentItemSets[$preSize][$i]['items'][$newItem])) {
                        continue;
                    }
                    $transactionIds = array_intersect($frequentItemSets[$preSize][$i]['transactionIds'],
                        $frequentItemSets[1][$j]['transactionIds']);
                    $frequency = sizeof($transactionIds);
                    $support = $frequency / $transactionsCount;
                    if ($frequency >= $minSupport) {
                        $frequentItemSet['support'] = $support;
                        $frequentItemSet['frequency'] = $frequency;
                        $newItems = $frequentItemSets[$preSize][$i]['items'];
                        $newItems[$newItem] = $newItem;
                        $frequentItemSet['items'] = $newItems;
                        $frequentItemSet['transactionIds'] = $transactionIds;
                        $frequentItemSets[$currentSize][] = $frequentItemSet;

                        print_r("\n frequent item found \n");
                    }
                }
            }
            $preSize = $currentSize;
            $currentSize++;
        }

        return $frequentItemSets;
    }

    /**
     * @param $frequentItemSets
     * @param $sentenceTexts
     * @param float $minAdjSupport
     * @return array
     */
    public static function findDynamicAspects(&$frequentItemSets, &$sentenceTexts, $minAdjSupport = 0.01)
    {
        $sentencesWords = [];
        foreach ($sentenceTexts as $sentenceId => $sentenceText) {
            $sentencesWords[$sentenceId] = WordLib::extractWords($sentenceText);
        }
        $adjWords = Word::fetchWords("value", "pos_tag = 'ADJ'");

        $aspects = [];
        foreach ($frequentItemSets as $sizedFrequentItemSets) {
            foreach ($sizedFrequentItemSets as $frequentItemSet) {
                $adjCount = 0;
                foreach ($frequentItemSet['transactionIds'] as $transactionId) {
                    $lastItemVal = end($frequentItemSet['items']);
                    $potAspectPos = array_search($lastItemVal, $sentencesWords[$transactionId]);
                    print_r("Potential aspect ends in $lastItemVal \n");

                    foreach ($adjWords as $adjWord) {
                        $adj = $adjWord->value;
                        $adjPos = array_search($adj, $sentencesWords[$transactionId]);

                        if ($adjPos !== false) {
                            $posDiff = $adjPos - $potAspectPos;
                            if (0 < $posDiff && $posDiff <= 3) {
                                $adjCount++;
                                print_r("Adjective $adj found in neighborhood \n");
                            }
                        }
                    }
                    $adjSupport = $adjCount / $frequentItemSet['frequency'];
                    if ($adjSupport >= $minAdjSupport) {
                        $itemVal = implode(" ", $frequentItemSet['items']);
                        $aspects[] = [
                            'value' => $itemVal,
                            'support' => $frequentItemSet['support'],
                            'adjSupport' => $adjSupport,
                            'frequency' => $frequentItemSet['frequency']
                        ];

                        print_r("*** Aspect found" . $itemVal . " **** \n");
                    }
                }
            }
        }

        return $aspects;
    }


}