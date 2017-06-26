<?php

namespace App\Http\Controllers;

use App\Helpers\Tokenizer;
use App\Libraries\AspectLib;
use App\Libraries\WordLib;
use App\Models\Aspect;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Rating;
use App\Models\Type;
use App\Models\Word;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\CodeCoverage\Report\PHP;
use Yajra\Datatables\Datatables;

class WordController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewWordManagerPanel()
    {
        return view('word.word-manager');
    }

    public function viewList()
    {
        return view('word.list');
    }

    public function getList()
    {
        $words = Word::fetchWords("*", "1", PHP_INT_MAX, "0", 'count', 'DSC');

        return Datatables::of($words)->make(true);
    }


    /**
     * @param WordLib $wordLib
     */
    public function storeWords(WordLib $wordLib)
    {
        $wordLib->storeWords();
    }


    /**
     *
     */
    public function cleanWords()
    {
        WordLib::cleanWords();
    }

    /**
     * @param $words
     */
    public function stemming(&$words)
    {
        foreach ($words as $key => $word) {
            $stem = $this->extractStem($word);
            if ($stem) {
                $words[$key] = $stem;
            } else {
                $words[$key] = $stem;
            }
        }
    }

    /**
     * @param $word
     * @return mixed
     */
    public function extractStem($word)
    {
        $stem = $word;
        $suffixType = $this->determineSuffix($word);

        $wordLength = sizeof($word);

        if ($suffixType == "PL2") {
            $word = substr($word, 0, $wordLength - 2);
            $stem = $this->extractStem($word);
        } else if ($suffixType == "PO3") {
            $word = substr($word, 0, $wordLength - 3);
            $stem = $this->extractStem($word);
        } else if ($suffixType == "VB2") {
            $word = substr($word, 0, $wordLength - 2);
            $stem = $this->removePrefix($word);
        } else if ($suffixType == "VB4") {
            $word = substr($word, 0, $wordLength - 4);
            $stem = $this->removePrefix($word);
        }

        return $stem;
    }


    public function displayWordsInfo()
    {
        $words = Word::fetchWords();
        foreach ($words as $word) {
            echo "Word: " . $word->value . "<br>";
            echo "Count: " . $word->count . "<br>";
            $occurrences = unserialize($word->occurrences);
            foreach ($occurrences as $aspectId => $aspectOccurrence) {
                $aspect = Aspect::fetchAspect($aspectId);
                foreach ($aspectOccurrence as $rate => $ratingOccurrence) {
                    echo "Aspect ID: " . $aspect->title . " , Rate:   " . $rate . " ,  Rate Occurrences: " . $ratingOccurrence . '<br>';
                }
            }
            echo "<hr>";
        }
    }

    /**
     * @return array|string
     */
    public function computeReviewsRateCount()
    {
        $reviewsRateCount = [];
        $retrieveAspectIds = AspectLib::retrieveAspectIds(0, 'Mobile-Phone');
        foreach ($retrieveAspectIds as $aspectId) {
            for ($rate = 0; $rate <= 5; $rate++) {
                $reviewsRateCount[$aspectId][$rate] = 0;
            }
        }

        $commentType = Type::fetch('comment');
        $entityTypeId = $commentType->id;
        $aspectType = Type::fetch('aspects');
        $ratingTypeId = $aspectType->id;
        $comments = Comment::fetchComments();
        $rating = new Rating();

        //Extract Non-stop words
        if (!sizeof($comments)) {
            return 'No comment<br>';
        }
        foreach ($comments as $key => $comment) {
            // echo "<br>$comment->text<br>";
            $commentRating = $rating->fetchRating($comment->id, $entityTypeId, $ratingTypeId);
            $commentRate = unserialize($commentRating->rate);
            foreach ($commentRate as $aspectId => $aspectRate) {
                $reviewsRateCount[$aspectId][$aspectRate]++;
            }
        }
        return $reviewsRateCount;
    }

}
