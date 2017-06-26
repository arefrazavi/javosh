<?php

namespace App\Libraries;

use App\Helpers\Common;
use App\Helpers\Tokenizer;
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

        print_r("Number of sentences:" .sizeof($limitedSizeSentences) ."\n");

        return $limitedSizeSentences;
    }

    /**
     * @param $threshold
     * @param $productId
     */
    public function classifySentences($threshold, $productId)
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

        $product = Product::fetch($productId);

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
            print_r("Sentences Count:". sizeof($sentences) ."<br> \n");

            foreach ($sentences as $sentence) {
                $sentenceId = $sentence->id;
                $entropies = unserialize($sentence->entropy);
                $bestEntropyAspectId = Common::findMin($entropies);
                if ($bestEntropyAspectId['value'] <= $threshold) {
                    $bestEntropyAspectId = $bestEntropyAspectId['key'];
                    $sentenceWords = WordLib::extractWords($sentence->text);
                    $preprocessedSentenceText = implode(" ", $sentenceWords);
                    $selectedSentences[$sentenceId]['id'] = $sentenceId;
                    $selectedSentences[$sentenceId]['bestEntropyAspectId'] = $bestEntropyAspectId;
                    $selectedSentences[$sentenceId]['text'] = $preprocessedSentenceText;
                    $selectedSentences[$sentenceId]['like'] = $commentLikeRating->rate;
                    $selectedSentences[$sentenceId]['dislike'] = $commentDislikeRating->rate;
                    $commentAspectsRates = unserialize($commentAspectsRating->rate);
                    foreach ($commentAspectsRates as $aspectId => $rate) {
                        $selectedSentences[$sentenceId][$aspectId . '-rate'] = $rate;
                    }
                    foreach ($entropies as $aspectId => $entropy) {
                        $selectedSentences[$sentenceId][$aspectId . '-entropy'] = $entropy;
                    }
                    print_r("Sentence with id: <br> \n". $sentenceId . " is selected <br> \n");

                }
            }

            print_r("Selected Sentences Count: ". sizeof($selectedSentences) ."<br> \n");

        }

        $filePath = base_path('data/sentences/selected-sentences/' . $product->id . '.csv');
        $writingMode = 'w';

        Common::writeToCsv($selectedSentences, $filePath, $writingMode);

        print_r("\n Sentences of product $productId has been classified \n");
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

        $whereClause = "`entropy` IS NULL";
        $sentences = Sentence::fetchSentences(PHP_INT_MAX, 0, $whereClause);
        foreach ($sentences as $sentence) {
            print_r($sentence->id . " : " . $sentence->text . "\n");
            $sentenceWords = WordLib::extractWords($sentence->text);
            $sentenceEntropies = [];
            $newWord = 0;
            $categoryID = $sentence->comment->category_id;
            foreach ($sentenceWords as $sentenceWord) {
                $whereRaw = "value = '$sentenceWord' AND category_id = $categoryID";
                $word = Word::fetch($whereRaw);
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
        $whereRaw = 'comment_id = '. $commentId .' AND ' . '(user_id = '. $userId . " OR user_id IS NULL) 
        AND (method_id = ". $methodId . ' OR  method_id IS NULL)';
        $sentences = Sentence::fetchSentencesWithSummary("sentence.*, summaries.aspect_id, summaries.polarity", $whereRaw);

        return $sentences;
    }


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
                $sentences = Sentence::fetchSentences("*", "comment_id = $comment->id AND aspect_frequency IS NULL ");
                foreach ($sentences as $sentence) {
                    $sentenceText = $sentence->text;
                    print_r("  Sentence: $sentence->id \n");
                    if ($sentence->aspect_frequency) {
                        continue;
                    }
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

}