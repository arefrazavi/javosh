<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Sentence extends Model
{

    /**
     * Get the products that owns the sentence
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function comment()
    {
        return self::belongsTo(Comment::class);
    }


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'comment_id',
        'text',
        'entropy',
        'aspect_frequency'
    ];


    public static function insert($newSentenceData)
    {
        return self::create($newSentenceData);
    }


    /**
     * @param int $id
     * @param string $sentenceText
     * @return mixed
     */
    public static function fetch($id = 0, $sentenceText = '')
    {
        if ($id != 0) {
            $sentence = self::find($id);
        } else {
            $sentence = self::where('text', $sentenceText)->first();
        }
        return $sentence;
    }

    /**
     * @param string $selectClause
     * @param int $limit
     * @param int $offset
     * @param string $whereClause
     * @param string $orderBy
     * @param string $order
     * @return mixed
     */
    public static function fetchSentences($selectClause = '*', $whereClause = "1", $limit = PHP_INT_MAX, $offset = 0, $orderBy = 'id', $order = 'ASC')
    {
        $sentences = self::select(DB::raw($selectClause))
            ->whereRaw($whereClause)
            ->skip($offset)
            ->take($limit)
            ->orderBy($orderBy, $order)
            ->get();

        return $sentences;
    }

    /**
     * @param string $selectClause
     * @param int $whereClause
     * @return mixed
     */
    public static function fetchSentencesWithSummary($selectClause = "*", $whereClause = 1) {
        $sentences = DB::table('sentences')
            ->leftjoin('summaries', 'summaries.sentence_id', '=', 'sentences.id')
            ->select(DB::raw($selectClause))
            ->whereRaw($whereClause)
            ->get();

        return $sentences;
    }
}
