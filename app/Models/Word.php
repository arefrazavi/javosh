<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Word extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'value',
        'count',
        'occurrences',
        'entropy',
        'sentiment_score',
        'pos_tag',
        'category_id'
    ];

    /**
     * @param $whereRaw
     * @param int $id
     * @return mixed
     */
    public static function fetch($whereRaw = "1", $id = 0)
    {
        if ($id != 0) {
            $word = self::find($id);
        } else {
            $word = self::whereRaw($whereRaw)->first();
        }
        return $word;
    }

    /**
     * @param string $selectClause
     * @param string $whereClause
     * @param int $limit
     * @param int $offset
     * @param string $orderBy
     * @param string $order
     * @return mixed
     */
    public static function fetchWords($selectClause = "*", $whereClause = "1", $limit = PHP_INT_MAX, $offset = 0, $orderBy = 'id', $order = 'ASC')
    {
        $words = self::select(DB::raw($selectClause))
            ->whereRaw($whereClause)
            ->skip($offset)
            ->take($limit)
            ->orderBy($orderBy, $order)
            ->get();

        return $words;
    }

    /**
     * @return mixed
     */
    public static function fetchStopWords()
    {
        $stopWords = DB::table('stop_words')->get();
        return $stopWords;
    }


    public static function insert($newWordData)
    {
        return self::create($newWordData);
    }


}
