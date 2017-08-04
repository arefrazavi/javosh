<?php

namespace App\Libraries;

use App\Helpers\Common;
use App\Helpers\Tokenizer;
use App\Models\Aspect;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Product;
use App\Models\Rating;
use App\Models\Sentence;
use App\Models\Summary;
use App\Models\Type;
use App\Models\Word;

class SentenceLib
{

    public $sentenceMaxSize = 40;
    public $sentenceMinSize = 20;

    /**
     *
     */
    public function storeSentences()
    {
        $comments = Comment::fetchComments();

        foreach ($comments as $key => $comment) {
            if ($comment->sentences()->first() !== null) {
                print_r("<p style='color: red'>Sentences of comment: " . $comment->id . " have already been inserted!</p>\n");
                continue;
            }
            print_r("comment id: $comment->id<br>\n");
            $sentencesTexts = $this->extractSentences($comment->text);

            //print_r("<br>**Comment Sentences: **<br>\n");
            //dump($sentencesTexts);

            $sentence['comment_id'] = $comment->id;
            foreach ($sentencesTexts as $sentenceText) {
                $sentence['text'] = $sentenceText;
                Sentence::insert($sentence);
            }

            print_r("<b style='color: green'>Sentences of comment: " . $sentence['comment_id'] . " have been inserted!</b>\n");
            print_r("<hr>");
        }

        print_r("<br><b>*************End of storing sentences****************</b></div>");

    }

    /**
     * @param $text
     * @return array
     */
    public function extractSentences($text)
    {

        $sentences = Tokenizer::segmentize($text);

        $whereClause = "pos_tag = 'V'";
        $verbWords = Word::fetchWords("*", $whereClause);
        $verbs = [];

        foreach ($verbWords as $verbWord) {
            $verbs[$verbWord->value] = $verbWord->value;
        }

        $limitedSizeSentences = [];
        foreach ($sentences as $key => $sentence) {
            $sentenceWords = Tokenizer::tokenize($sentence);
            while (true) {
                $wordCount = sizeof($sentenceWords);

                if ($wordCount > $this->sentenceMaxSize) {
                    $delimiterIndex = $this->sentenceMaxSize;
                    for ($wordIndex = $this->sentenceMinSize; $wordIndex < $this->sentenceMaxSize; $wordIndex++) {
                        if (isset($verbs[$sentenceWords[$wordIndex]])) {
                            $delimiterIndex = $wordIndex + 1;
                            break;
                        }
                    }

                    if ($delimiterIndex == $this->sentenceMaxSize) {
                        for ($wordIndex = $this->sentenceMinSize; $wordIndex < $this->sentenceMaxSize; $wordIndex++) {
                            if (isset(Tokenizer::$conjunctions[$sentenceWords[$wordIndex]])) {
                                $delimiterIndex = $wordIndex;
                                break;
                            }
                        }
                    }

                    $sentenceWords1 = array_slice($sentenceWords, 0, $delimiterIndex);
                    $sentenceWords2 = array_slice($sentenceWords, $delimiterIndex);

                    $limitedSizeSentences[] = implode(" ", $sentenceWords1);
                    $sentenceWords = $sentenceWords2;
                } else {
                    $sentence = implode(" ", $sentenceWords);
                    $limitedSizeSentences[] = $sentence;
                    break;
                }
            }
        }

        print_r("Number of sentences:" . sizeof($limitedSizeSentences) . "\n");

        return $limitedSizeSentences;
    }

    /**
     * @param $threshold
     * @param $productId
     */
    public static function classifySentences($product, $wEscore, $wSscore, $threshold)
    {

        $selectedSentences = [];
        $commentType = Type::fetch('comment');
        $entityTypeId = $commentType->id;
        $aspectType = Type::fetch('aspects');
        $ratingTypeId = $aspectType->id;
        $likeType = Type::fetch('like');
        $likeTypeId = $likeType->id;
        $dislikeType = Type::fetch('dislike');
        $dislikeTypeId = $dislikeType->id;
        $category = $product->category;
        $adjectives = Word::fetchWords("value", "pos_tag = 'ADJ' AND category_id = $category->id");
        $aspects = $category->aspects;

        $outputDir = "data/sentences/$category->title/related";
        $filePath = base_path($outputDir);
        Common::makeDirectory($filePath);
        $writingMode = 'w';
        $fileName = "$outputDir/$product->id.csv";
        if (file_exists($fileName)) {
            print_r("Sentences of product $product->id has been already written. \n");
            return true;
        }


        $comments = $product->comments;
        foreach ($comments as $comment) {
            $ratingData['entity_id'] = $comment->id;
            $ratingData['entity_type_id'] = $entityTypeId;
            $ratingData['rating_type_id'] = $ratingTypeId;
            $commentAspectsRating = Rating::fetch($ratingData);

            $ratingData['entity_type_id'] = $entityTypeId;
            $ratingData['rating_type_id'] = $likeTypeId;
            $commentLikeRating = Rating::fetch($ratingData);

            $ratingData['entity_type_id'] = $entityTypeId;
            $ratingData['rating_type_id'] = $dislikeTypeId;
            $commentDislikeRating = Rating::fetch($ratingData);

            $sentences = $comment->sentences;
            //print_r("Sentences Count: " . sizeof($sentences) . " \n");

            foreach ($sentences as $sentence) {
                $sentenceWords = WordLib::extractNonStopWords($sentence->text);
                if (empty($sentenceWords)) {
                    continue;
                }

                $entropies = unserialize($sentence->entropy);
                if (empty($entropies)) {
                    continue;
                }
                $minEntropy = Common::findMin($entropies);
                $eScore = $minEntropy['value'];
                $sScore = WordLib::calculateSScore($sentence->text, $adjectives);
                print_r("s-score: $sScore, escore: $eScore \n");

                $rScore = ((abs($eScore) * $wEscore) + ($sScore * $wSscore)) / ($wEscore + $wSscore);
                print_r("R-score: " . $rScore . "\n");

                if ($rScore >= $threshold) {
                    $sentenceId = $sentence->id;
                    //$preprocessedSentenceText = implode(" ", $sentenceWords);
                    $selectedSentences[$sentenceId]['id'] = $sentenceId;
                    $selectedSentences[$sentenceId]['text'] = $sentence->text;
                    $selectedSentences[$sentenceId]['like'] = $commentLikeRating->rate;
                    $selectedSentences[$sentenceId]['dislike'] = $commentDislikeRating->rate;
                    $selectedSentences[$sentenceId]['sScore'] = $sScore;
                    $selectedSentences[$sentenceId]['eScore'] = $eScore;


                    $commentAspectsRates = unserialize($commentAspectsRating->rate);

                    foreach ($aspects as $aspect) {
                        $aspectClosestId = $aspect->closest_aspect_id;
                        if (isset($commentAspectsRates[$aspect->id]) && $aspect->id <= 16) {
                            $selectedSentences[$sentenceId][$aspect->id . '-rate'] = $commentAspectsRates[$aspect->id];
                        } else {
                            print_r("closet of $aspect->id: $aspectClosestId \n");
                            $selectedSentences[$sentenceId][$aspect->id . '-rate'] = $commentAspectsRates[$aspectClosestId];
                        }

                    }

                    foreach ($aspects as $aspect) {
                        $aspectClosestId = $aspect->closest_aspect_id;
                        if (isset($entropies[$aspect->id]) && $aspect->id <= 16) {
                            $selectedSentences[$sentenceId][$aspect->id . '-entropy'] = $entropies[$aspect->id];
                        } else {
                            $selectedSentences[$sentenceId][$aspect->id . '-entropy'] = $entropies[$aspectClosestId];
                        }
                    }

                    print_r("Sentence with id: $sentenceId is related \n");
                }
            }

            print_r("Related Sentences Count: " . sizeof($selectedSentences) . " \n");

        }

        Common::writeToCsv($selectedSentences, $fileName, $writingMode);

        print_r("\n Sentences of product $product->id has been classified \n");
    }

    /**
     *
     */
    public function computeSentencesEntropy()
    {
        $commentType = Type::fetch('comment');
        $entityTypeId = $commentType->id;
        $aspectType = Type::fetch('aspects');
        $ratingTypeId = $aspectType->id;

        $whereClause = "`entropy` IS NULL OR `entropy` = 'a:0:{}'";
        //$whereClause = 1;
        $sentences = Sentence::fetchSentences("*", $whereClause);
        foreach ($sentences as $sentence) {
            print_r("step 1 \n");
            print_r($sentence->id . "\n");
            $sentenceWords = WordLib::extractNonStopWords($sentence->text);
            $sentenceEntropies = [];
            $newWord = 0;
            $categoryID = $sentence->comment->category_id;
            foreach ($sentenceWords as $sentenceWord) {
                $whereClause = "value = '$sentenceWord' AND category_id = $categoryID";
                $word = Word::fetch($whereClause);
                if ($word && $word->entropy) {
                    print_r("step 2 \n");

                    $wordEntropies = unserialize($word->entropy);
                    foreach ($wordEntropies as $aspectId => $entropy) {
                        if (isset($sentenceEntropies[$aspectId])) {
                            $sentenceEntropies[$aspectId] += doubleval($entropy);
                        } else {
                            $sentenceEntropies[$aspectId] = doubleval($entropy);
                        }
                    }
                } else {
                    print_r("step 3 \n");

                    $comment = $sentence->comment;
                    $newRating['entity_id'] = $comment->id;
                    $newRating['entity_type_id'] = $entityTypeId;
                    $newRating['rating_type_id'] = $ratingTypeId;
                    $commentRating = Rating::fetch($newRating);
                    $commentRates = unserialize($commentRating->rate);
                    if (!$word) {

                        print_r("step 4 \n");
                        $wordData = [];
                        $wordData['value'] = $sentenceWord;
                        $wordData['count'] = 1;
                        $wordData['occurrences'] = [];
                        $wordData['category_id'] = $categoryID;
                        foreach ($commentRates as $aspectId => $rating) {
                            $wordData['occurrences'][$aspectId][$rating] = 1;
                        }
                        $wordData['occurrences'] = serialize($wordData['occurrences']);
                        $word = Word::insert($wordData);

                        print_r("New word: " . $word->value . "<br>\n");
                    } else {

                        print_r("step 5 \n");
                        $wordOccurrences = unserialize($word->occurrences);
                        foreach ($commentRates as $aspectId => $rating) {
                            if (isset($wordOccurrences[$aspectId][$rating])) {
                                $wordOccurrences[$aspectId][$rating]++;
                            } else {
                                $wordOccurrences[$aspectId][$rating] = 1;
                            }
                        }
                        $word->occurrences = serialize($wordOccurrences);
                        $word->count++;
                        $word->save();
                    }
                    $newWord = 1;
                }
            }

            if ($newWord) {
                print_r("New word in the sentence \n");
                print_r('<hr>');
                continue;
            }

            $wordsCount = sizeof($sentenceWords);
            foreach ($sentenceEntropies as $aspectId => $entropy) {
                $sentenceEntropies[$aspectId] = doubleval($entropy / $wordsCount);
            }
            $sentence->entropy = serialize($sentenceEntropies);
            $sentence->save();

            print_r("entropy for sentence $sentence->id has been updated: <br>");
        }


        print_r("\n********END of compute Sentences entropy ******\n");
    }

    /**
     * @param $commentId
     * @param $userId
     * @param $methodId
     * @return mixed
     */
    public static function getSentencesWihSummary($commentId, $userId, $methodId = Summary::GOLD_STANDARD_METHOD_ID)
    {
        $whereClause = 'comment_id = ' . $commentId . ' AND ' . '(user_id = ' . $userId . " OR user_id IS NULL) 
        AND (method_id = " . $methodId . ' OR  method_id IS NULL)';
        $sentences = Sentence::fetchSentencesWithSummary("sentences.*, summaries.aspect_id, summaries.polarity", $whereClause);

        return $sentences;
    }

    /**
     *
     */
    public static function computeAspectFrequency()
    {
        $categories = Category::fetchCategories();
        foreach ($categories as $category) {
            $comments = $category->comments;
            $aspects = $category->aspects;
            print_r("Category: $category->id \n");
            foreach ($comments as $comment) {
                print_r(" Comment: $comment->id \n");
                //$sentences = $comment->sentences;
                //$sentences = Sentence::fetchSentences("*", "comment_id = $comment->id AND aspect_frequency IS NULL ");
                $sentences = Sentence::fetchSentences("*", "comment_id = $comment->id ");
                foreach ($sentences as $sentence) {
                    $sentenceText = $sentence->text;
                    print_r("  Sentence: $sentence->id \n");
//                    if ($sentence->aspect_frequency) {
//                        continue;
//                    }
                    $aspectFrequency = [];
                    foreach ($aspects as $aspect) {
                        print_r("   Aspect: $aspect->id \n");
                        $frequency = 0;
                        $keywords = unserialize($aspect->keywords);
                        if (!$keywords) {
                            continue;
                        }
                        foreach ($keywords as $keyword => $similarity) {
                            $frequency += substr_count($sentenceText, $keyword) * $similarity;
                        }
                        $aspectFrequency[$aspect->id] = $frequency;
                        print_r("   Freq: $frequency \n");

                    }
                    $sentence->aspect_frequency = serialize($aspectFrequency);
                    $sentence->save();
                }
            }
        }
    }

    /**
     * @param $whereClause
     * @return array
     */
    public static function getTextSentencesTexts($whereClause)
    {
        $textSentenceTexts = [];
        $comments = Comment::fetchComments("id", $whereClause, 1);

        foreach ($comments as $comment) {
            $whereClause = "comment_id = $comment->id";
            $commentTextSentences = Sentence::fetchSentences("*", $whereClause);
            foreach ($commentTextSentences as $commentTextSentence) {
                $commentTextSentenceText = Common::sanitizeString($commentTextSentence->text);
                $textSentenceTexts[$commentTextSentence->id] = $commentTextSentenceText;
            }
        }

        return $textSentenceTexts;

    }

    /**
     * @param $whereClause
     * @return array
     */
    public static function getPointsSentencesTexts($whereClause)
    {
        $pointSentenceTexts = [];
        $comments = Comment::fetchComments("id", $whereClause, 1);
        $sentenceId = 1;
        foreach ($comments as $comment) {
            $pointsText = $comment->positive_points . ". " . $comment->negative_points;
            $commentPointsSentenceTexts = Tokenizer::segmentize($pointsText);
            foreach ($commentPointsSentenceTexts as $commentPointsSentenceText) {
                $commentPointsSentenceText = Common::sanitizeString($commentPointsSentenceText);
                $pointSentenceTexts[$sentenceId] = $commentPointsSentenceText;
                $sentenceId++;
            }
        }

        return $pointSentenceTexts;
    }

    /**
     *
     */
    public static function writeSentencesIntoFile()
    {
        $products = Product::fetchProducts();
        $commentType = Type::fetch('comment');
        $entityTypeId = $commentType->id;
        $aspectType = Type::fetch('aspects');
        $ratingTypeId = $aspectType->id;
        $likeType = Type::fetch('like');
        $likeTypeId = $likeType->id;
        $dislikeType = Type::fetch('dislike');
        $dislikeTypeId = $dislikeType->id;


        foreach ($products as $product) {

            $category = $product->category;
            $aspects = $category->aspects;
            $comments = $product->comments;
            $productSentences = [];

            $outputDir = "data/sentences/$category->title/all";
            $filePath = base_path($outputDir);
            Common::makeDirectory($filePath);
            $writingMode = 'w';
            $fileName = "$outputDir/$product->id.csv";
            if (file_exists($fileName)) {
                print_r("Sentences of product $product->id has been already written. \n");
                continue;
            }

            foreach ($comments as $comment) {
                $ratingData['entity_id'] = $comment->id;
                $ratingData['entity_type_id'] = $entityTypeId;
                $ratingData['rating_type_id'] = $ratingTypeId;
                $commentAspectsRating = Rating::fetch($ratingData);

                $ratingData['entity_type_id'] = $entityTypeId;
                $ratingData['rating_type_id'] = $likeTypeId;
                $commentLikeRating = Rating::fetch($ratingData);

                $ratingData['entity_type_id'] = $entityTypeId;
                $ratingData['rating_type_id'] = $dislikeTypeId;
                $commentDislikeRating = Rating::fetch($ratingData);

                $sentences = $comment->sentences;
                //print_r("Sentences Count: " . sizeof($sentences) . " \n");

                foreach ($sentences as $sentence) {
                    $sentenceId = $sentence->id;
                    print_r("Gathering information of Sentence with id: $sentenceId \n");
                    //$preprocessedSentenceText = implode(" ", $sentenceWords);
                    $productSentences[$sentenceId]['id'] = $sentenceId;
                    $productSentences[$sentenceId]['text'] = $sentence->text;
                    $productSentences[$sentenceId]['like'] = $commentLikeRating->rate;
                    $productSentences[$sentenceId]['dislike'] = $commentDislikeRating->rate;

                    $commentAspectsRates = unserialize($commentAspectsRating->rate);

                    foreach ($aspects as $aspect) {
                        $aspectClosestId = $aspect->closest_aspect_id;
                        if (isset($commentAspectsRates[$aspect->id]) && $aspect->id <= 16) {
                            $productSentences[$sentenceId][$aspect->id . '-rate'] = $commentAspectsRates[$aspect->id];
                        } else {
                            print_r("closest aspect of $aspect->id: $aspectClosestId \n");
                            $productSentences[$sentenceId][$aspect->id . '-rate'] = $commentAspectsRates[$aspectClosestId];
                        }

                    }

                    $entropies = unserialize($sentence->entropy);
                    foreach ($aspects as $aspect) {
                        $aspectClosestId = $aspect->closest_aspect_id;
                        if (isset($entropies[$aspect->id]) && $aspect->id <= 16) {
                            $productSentences[$sentenceId][$aspect->id . '-entropy'] = $entropies[$aspect->id];
                        } elseif (isset($entropies[$aspectClosestId])) {
                            $productSentences[$sentenceId][$aspect->id . '-entropy'] = $entropies[$aspectClosestId];
                        } else {
                            $productSentences[$sentenceId][$aspect->id . '-entropy'] = null;
                        }
                    }
                }
            }
            $sentenceCount = sizeof($productSentences);
            print_r("Product Sentences Count: " . $sentenceCount . " \n");
            if (!$sentenceCount) {
                continue;
            }

            Common::writeToCsv($productSentences, $fileName, $writingMode);

            print_r("\n Sentences of product $product->id has been written \n");
        }
    }
}