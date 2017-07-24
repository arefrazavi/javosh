<?php

namespace App\Libraries;

use App\Helpers\Common;
use App\Helpers\Tokenizer;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Rating;
use App\Models\Sentence;
use App\Models\Type;
use App\Models\Word;
use Illuminate\Support\Facades\DB;

class WordLib
{

    /**
     * @return string
     */
    public function storeWords()
    {
        DB::connection()->disableQueryLog();
        $selectRaw = "categories.id";
        $categories = Category::fetchCategories($selectRaw);

        foreach ($categories as $category) {

            $commentType = Type::fetch('comment');
            $entityTypeId = $commentType->id;
            $aspectType = Type::fetch('aspects');
            $ratingTypeId = $aspectType->id;
            $whereClause = "is_analysed = 0 AND category_id = $category->id";
            //$whereClause = 'id IN (259, 833, 25)';
            $comments = Comment::fetchComments("*", $whereClause);

            //Extract Non-stop words
            if (!sizeof($comments)) {
                print_r("No unanalyzed comment for category $category->id\n");
            }
            $bowComments = [];
            foreach ($comments as $key => $comment) {
                print_r("\n $comment->id <br> \n");
                $ratingData['entity_id'] = $comment->id;
                $ratingData['entity_type_id'] = $entityTypeId;
                $ratingData['rating_type_id'] = $ratingTypeId;
                $commentRating = Rating::fetch($ratingData);
                $text = $comment->text . '. ' . $comment->positive_points . '. ' . $comment->negative_points;

                $commentWords = self::extractWords($text);

                $bowComments[$key]['words'] = $commentWords;
                $bowComments[$key]['rating'] = unserialize($commentRating->rate);
                //$bowComments[$key]['categoryId'] = $comment->category_id;
                $comment->is_analysed = 1;
                $comment->save();

                print_r("\n Comment $comment->id has been analysed \n");
            }

            //Extract Unique words and their occurrences
            $uniqueWords = [];
            foreach ($bowComments as $bowComment) {
                foreach ($bowComment['words'] as $wordVal) {
                    if (isset($uniqueWords[$wordVal])) {
                        $uniqueWords[$wordVal]['count']++;
                        foreach ($bowComment['rating'] as $aspectId => $rating) {
                            if (isset($uniqueWords[$wordVal]['occurrences'][$aspectId][$rating])) {
                                $uniqueWords[$wordVal]['occurrences'][$aspectId][$rating]++;
                            } else {
                                $uniqueWords[$wordVal]['occurrences'][$aspectId][$rating] = 1;
                            }
                        }
                    } else {
                        $uniqueWords[$wordVal]['value'] = $wordVal;
                        $uniqueWords[$wordVal]['count'] = 1;
                        $uniqueWords[$wordVal]['category_id'] = $category->id;
                        foreach ($bowComment['rating'] as $aspectId => $rating) {
                            $uniqueWords[$wordVal]['occurrences'][$aspectId][$rating] = 1;
                        }
                    }
                }
            }


            print_r("\n Saving or updating unique words into database \n");
            //Save or update unique words into database
            foreach ($uniqueWords as $uniqueWord) {
                $wordVal = $uniqueWord['value'];
                $whereRaw = "value = '$wordVal' AND category_id = $category->id";
                $word = Word::fetch($whereRaw);
                if (!$word) {
                    $uniqueWord['occurrences'] = serialize($uniqueWord['occurrences']);
                    $word = Word::insert($uniqueWord);
                } else {
                    $occurrences = unserialize($word->occurrences);
                    foreach ($uniqueWord['occurrences'] as $aspectId => $aspectOccurrence) {
                        foreach ($aspectOccurrence as $rate => $ratingOccurrence) {
                            if (isset($occurrences[$aspectId][$rate])) {
                                $occurrences[$aspectId][$rate] += $ratingOccurrence;
                            } else {
                                $occurrences[$aspectId][$rate] = $ratingOccurrence;
                            }
                        }
                    }
                    $word->count += $uniqueWord['count'];
                    $word->occurrences = serialize($occurrences);
                    $word->save();
                }

                print_r("\n Word " . $word->id . " : " . $word->value . " \n");
            }

        }
        print_r("\n********END******\n");
    }

    /**
     * @param $value
     */
    public function storeWord($value)
    {
        $selectRaw = "categories.id";
        $categories = Category::fetchCategories($selectRaw);

        foreach ($categories as $category) {
            $word = Word::fetch("value = '$value' AND category_id = $category->id");
            if (!$word) {
                $word = new Word();
            }
            $word->value = $value;
            $word->count = 0;
            $word->category_id = $category->id;
            $occurrences = [];


            $commentType = Type::fetch('comment');
            $entityTypeId = $commentType->id;
            $aspectType = Type::fetch('aspects');
            $ratingTypeId = $aspectType->id;
            $comments = $category->comments;

            foreach ($comments as $key => $comment) {
                print_r("\n $comment->id <br> \n");
                $ratingData['entity_id'] = $comment->id;
                $ratingData['entity_type_id'] = $entityTypeId;
                $ratingData['rating_type_id'] = $ratingTypeId;
                $commentRating = Rating::fetch($ratingData);
                $text = $comment->text . '. ' . $comment->positive_points . '. ' . $comment->negative_points;

                $count = substr_count($text, $value);

                $word->count += $count;

                foreach ($commentRating as $aspectId => $rating) {
                    if (isset($occurrences[$aspectId][$rating])) {
                        $occurrences[$aspectId][$rating] += $count;
                    } else {
                        $occurrences[$aspectId][$rating] = $count;
                    }
                }
            }

            if (!$word->count) {
                continue;
            }
            $occurrences = serialize($occurrences);
            print_r("\n $occurrences \n");
            $word->occurrences = $occurrences;
            $word->save();

            print_r("\n Saving word $word->value with id $word->id in category $category->id into database \n");

        }


    }

    /**
     *
     */
    public function findAttributes()
    {
        $wordsPositiveRate = [];
        $wordsNegativeRate = [];
        $positiveAttributes = [];
        $negativeAttributes = [];
        $negativeRatesWeights = [
            '0' => 3,
            '1' => 2,
            '2' => 1
        ];
        $positiveRatesWeights = [
            '4' => 1,
            '5' => 2,
            '6' => 3
        ];

        //$words = Word::fetchWords();
        $whereClause = 'pos_tag IS NUll';
        $words = Word::fetchWords("*", $whereClause);
        foreach ($words as $word) {
            $occurrences = unserialize($word->occurrences);
            $positiveFreq = 0;
            $negativeFreq = 0;
            foreach ($occurrences as $aspectId => $aspectOccurrence) {
                foreach ($negativeRatesWeights as $rate => $weight) {
                    if (isset($aspectOccurrence[$rate])) {
                        $negativeFreq += intval($aspectOccurrence[$rate]) * $weight;
                    }
                }
                foreach ($positiveRatesWeights as $rate => $weight) {
                    if (isset($aspectOccurrence[$rate])) {
                        $positiveFreq += intval($aspectOccurrence[$rate]) * $weight;
                    }
                }
            }

            $wordsPositiveRate[$word->id] = $positiveFreq - $negativeFreq;
            $wordsNegativeRate[$word->id] = $negativeFreq - $positiveFreq;

        }

        arsort($wordsNegativeRate);
        arsort($wordsPositiveRate);
        $highThresholdIndex = 100;

        foreach ($wordsNegativeRate as $wordId => $rate) {
            if (sizeof($negativeAttributes) > $highThresholdIndex) {
                break;
            }
            $negativeAttributes[] = [
                'id' => $wordId,
                'score' => $rate
            ];
        }

        foreach ($wordsPositiveRate as $wordId => $rate) {
            if (sizeof($positiveAttributes) > $highThresholdIndex) {
                break;
            }
            $positiveAttributes[] = [
                'id' => $wordId,
                'score' => $rate
            ];
        }

        print_r("\n Negative Words \n");
        foreach ($negativeAttributes as &$attribute) {
            $word = Word::fetch('', $attribute['id']);
            $attribute['value'] = $word->value;
            print_r("\n Word: " . $word->id . " : " . $word->value . " : " . $attribute['score']);
        }
        unset($attribute);


        $filePath = base_path('data/words/negative-attributes.csv');
        $writingMode = 'w';
        Common::writeToCsv($negativeAttributes, $filePath, $writingMode);

        print_r("\n Positive Words \n");
        foreach ($positiveAttributes as &$attribute) {
            $word = Word::fetch('', $attribute['id']);
            $attribute['value'] = $word->value;
            print_r("\n Word: " . $word->id . " : " . $word->value . " : " . $attribute['score']);

        }
        unset($attribute);

        $filePath = base_path('data/words/positive-attributes.csv');
        $writingMode = 'w';
        Common::writeToCsv($positiveAttributes, $filePath, $writingMode);

        print_r("\n********END of findAttributes ******\n");

    }

    /**
     *
     */
    public function findVerbs()
    {
        $verbs = [];
        $sentences = Sentence::fetchSentences();
        foreach ($sentences as $sentence) {
            $sentenceWords = Tokenizer::tokenize($sentence->text);
            $lastWord = end($sentenceWords);
            if (isset($verbs[$lastWord])) {
                $verbs[$lastWord]['count']++;
            } else {
                $verbs[$lastWord]['value'] = $lastWord;
                $verbs[$lastWord]['count'] = 1;
            }

            print_r("\n $lastWord <br> \n");

        }

        Common::sortTwoDimensional($verbs);

        $filePath = base_path('data/words/potential_verbs.csv');
        $writingMode = 'w';
        Common::writeToCsv($verbs, $filePath, $writingMode);

        print_r("\n********END of findVerbs ******\n");

    }

    /**
     * @param $files
     */
    public function updatePosTag($files)
    {
        foreach ($files as $file) {
            $words = Common::readFromCsv($file['filePath']);
            $posTag = $file['posTag'];

            foreach ($words as $word) {
                print_r("\n  $word[0] <br> \n");
                $whereRaw = "value = '" . trim($word[0]) . "'";
                $word = Word::fetch($whereRaw);
                if ($word) {
                    $word->pos_tag = $posTag;
                    $word->save();
                }
            }
        }
    }

    /**
     *
     */
    public function computeWordsEntropy()
    {
        $whereClause = 'entropy IS NULL';
        $words = Word::fetchWords("*", $whereClause);
        foreach ($words as $word) {
            print_r("Word: " . $word->value . "<br>");
            print_r("Count: " . $word->count . "<br>");
            $occurrences = unserialize($word->occurrences);
            $entropies = [];
            foreach ($occurrences as $aspectId => $aspectOccurrence) {
                $entropy = 0;
                foreach ($aspectOccurrence as $rate => $ratingOccurrence) {
                    $probability = $ratingOccurrence / $word->count;
                    $entropy += $probability * log($probability);
                }
                $entropies[$aspectId] = $entropy;
            }
            $word->entropy = serialize($entropies);
            $word->save();
            print_r("Entropy of word $word->id is updated \n");
        }

        print_r("\n********END******\n");
    }

    /**
     * @param $text
     * @return array
     */
    public static function extractWords($text)
    {
        $text = Common::sanitizeString($text);
        $words = Tokenizer::tokenize($text);
        Tokenizer::removeStopWords($words);

        return $words;
    }
    
    public static function cleanWords()
    {
        $categoryId = 4;
        $whereClause = "category_id = " . $categoryId;
        $words = Word::fetchWords("*", $whereClause);

        foreach ($words as $word) {
            print_r("<br>" . "Before Cleaning: " . $word->value . "<br>");
            foreach (Tokenizer::$wordDelimiters as $wordDelimiter) {
                $wordNewValue = str_replace($wordDelimiter, "", $word->value);
            }
            print_r("<br>" . "After Cleaning: " . $wordNewValue . "<br>");
            $isReplaced = 0;
            if ($wordNewValue != $word->value) {
                foreach ($words as $otherWord) {
                    if ($otherWord->value == $wordNewValue) {
                        $otherWord->count += $word->count;

                        $wordOccurrences = unserialize($word->occurrences);
                        $otherWordOccurrences = unserialize($otherWord->occurrences);
                        foreach ($wordOccurrences as $aspectId => $aspectOccurrence) {
                            foreach ($aspectOccurrence as $rate => $ratingOccurrence) {
                                if (isset($otherWordOccurrences[$aspectId][$rate])) {
                                    $otherWordOccurrences[$aspectId][$rate] += $ratingOccurrence;
                                } else {
                                    $otherWordOccurrences[$aspectId][$rate] = $ratingOccurrence;
                                }
                            }
                        }
                        $otherWord->occurrences = serialize($otherWordOccurrences);

                        $sentimentScores = [];
                        foreach ($otherWordOccurrences as $aspectId => $aspectOccurrence) {
                            $sentimentScore = 0;
                            foreach ($aspectOccurrence as $rate => $ratingOccurrence) {
                                $probability = $ratingOccurrence / $otherWord->count;
                                $sentimentScore += $probability * log($probability);
                            }
                            $sentimentScores[$aspectId] = $sentimentScore;
                        }
                        $otherWord->sentiment_score = serialize($sentimentScores);

                        $otherWord->save();
                        $word->delete();
                        $isReplaced = 1;
                        print_r("<br>" . "Word: $otherWord->value with id $otherWord->id. has been updated<br>");
                        break;
                    }
                }
                if (!$isReplaced) {
                    $word->value = $wordNewValue;
                    $word->save();
                }
            }
        }
    }

    public static function resolveNonStopWords()
    {
        $commentType = Type::fetch('comment');
        $entityTypeId = $commentType->id;
        $aspectType = Type::fetch('aspects');
        $ratingTypeId = $aspectType->id;
        $comments = Comment::fetchComments();

        //Extract Non-stop words
        $bowComments = [];
        foreach ($comments as $key => $comment) {
            print_r("\n $comment->id <br> \n");
            $ratingData['entity_id'] = $comment->id;
            $ratingData['entity_type_id'] = $entityTypeId;
            $ratingData['rating_type_id'] = $ratingTypeId;
            $commentRating = Rating::fetch($ratingData);
            $text = $comment->text . '. ' . $comment->positive_points . '. ' . $comment->negative_points;
            print_r("\n comment id:" . $comment->id . "<br> \n");

            $rawCommentWords = self::extractWords($text);
            $commentWords = [];

            foreach ($rawCommentWords as $commentWord) {
                $word = Word::fetch($commentWord);
                if (!$word) {
                    $commentWords[] = $commentWord;
                }
            }

            if (!empty($commentWords)) {
                $bowComments[$key]['words'] = $commentWords;
                $bowComments[$key]['rating'] = unserialize($commentRating->rate);
            }
            print_r("\n -------------------------------------------------- \n");
        }

        //Extract Unique words and their occurrences
        $uniqueWords = [];
        foreach ($bowComments as $bowComment) {
            foreach ($bowComment['words'] as $wordVal) {
                if (isset($uniqueWords[$wordVal])) {
                    $uniqueWords[$wordVal]['count']++;
                    foreach ($bowComment['rating'] as $aspectId => $rating) {
                        if (isset($uniqueWords[$wordVal]['occurrences'][$aspectId][$rating])) {
                            $uniqueWords[$wordVal]['occurrences'][$aspectId][$rating]++;
                        } else {
                            $uniqueWords[$wordVal]['occurrences'][$aspectId][$rating] = 1;
                        }
                    }
                } else {
                    $uniqueWords[$wordVal]['value'] = $wordVal;
                    $uniqueWords[$wordVal]['count'] = 1;
                    foreach ($bowComment['rating'] as $aspectId => $rating) {
                        $uniqueWords[$wordVal]['occurrences'][$aspectId][$rating] = 1;
                    }
                }
            }
        }

        //Save or update unique words into database
        foreach ($uniqueWords as $uniqueWord) {
            $uniqueWord['occurrences'] = serialize($uniqueWord['occurrences']);
            $word = Word::insert($uniqueWord);
            print_r("\n Word : " . $word->id . " : " . $word->value . " \n");
        }


        //Remove Stop-words from words table
        $words = Word::fetchWords();
        $stopWords = Word::fetchStopWords();
        $stopWordValues = [];
        foreach ($stopWords as $stopWord) {
            $stopWordValues[$stopWord->value] = $stopWord->value;
        }

        foreach ($words as $word) {
            if (isset($stopWordValues[$word->value])) {
                $word->delete();
            }
        }

        print_r("\n********END******\n");
    }

    public static function getWordValues($whereClause)
    {
        $wordValues = [];
        $words = Word::fetchWords("id, value", $whereClause, PHP_INT_MAX, 0, "count", "DESC");
        foreach ($words as $word) {
            $wordValues[$word->id] = $word->value;
        }

        return $wordValues;
    }

}