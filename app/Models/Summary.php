<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Summary extends Model
{

    const GOLD_STANDARD_METHOD_ID = 1;
    const CENTROID_BASED_METHOD_ID = 2;

    const GS_METHOD_ID = 1;
    const CB_METHOD_ID = 3;
    const RANDOM_METHOD_ID = 2;
    const E_WE_SCB_METHOD_ID = 3;
    const MAX_SUMMARY_SIZE = 5;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'sentence_id',
        'method_id',
        'user_id',
        'aspect_id',
        'polarity',
    ];


    /**
     * Make primary key non-increment
     * @var bool
     */
//    public $incrementing = false;

//    protected $primaryKey = array('product_id', 'sentence_id', 'method_id', 'user_id');

    public $timestamps = false;


    /**
     * Get the products that owns the comment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return self::belongsTo(Product::class);
    }

    public static function insert($newSummaryData)
    {
        return self::create($newSummaryData);
    }

    public static function updateOrInsert($summaryData, $updateData = [])
    {
        $summary = self::updateOrCreate($summaryData, $updateData);

        return $summary;
    }


    public static function fetch($whereClause)
    {
        $results = self::whereRaw($whereClause)->first();

        return $results;
    }

    public static function fetchProductSummary($selectClause = "*", $whereClause = "1", $limit = self::MAX_SUMMARY_SIZE, $offset = 0)
    {
        $summary = DB::table('sentences')
            ->leftjoin('comments', 'comments.id', '=', 'sentences.comment_id')
            ->leftjoin('summaries', 'summaries.sentence_id', '=', 'sentences.id')
            ->selectRaw($selectClause)
            ->whereRaw($whereClause)
            ->groupBy("sentence_id")
            ->orderBy("summary_count", "DESC")
            ->skip($offset)
            ->take($limit)
            ->get();

        return $summary;
    }

    public static function fetchSentence($summaryData)
    {
        $sentence = self::where('product_id', $summaryData['product_id'])
            ->where('sentence_id', $summaryData['sentence_id'])
            ->where('method_id', $summaryData['method_id'])
            ->where('user_id', $summaryData['user_id'])
            ->first();

        return $sentence;
    }

    public static function deleteSummary($whereClause)
    {
        $result = self::whereRaw($whereClause)->delete();

        return $result;
    }


    /**
     * @param int $id
     * @param string $methodTitle
     * @return mixed
     */
    public static function fetchMethod($id = 0, $methodTitle = '')
    {
        if ($id != 0) {
            $method = self::find($id);
        } else {
            $method = self::where('title', $methodTitle)->first();
        }

        return $method;
    }

    public static function fetchMethods($selectClause = "*", $whereClause = "1")
    {
        $methods = DB::table('summarization_methods')
                ->select(DB::raw($selectClause))
                ->whereRaw($whereClause)
                ->get();

        return $methods;
    }

    public static function fetchSummary($whereClause)
    {
        $summary = self::whereRaw($whereClause)->get();

        return $summary;

    }

    public static function fetchSummaries($selectClause = '*', $whereClause = "1", $limit = PHP_INT_MAX, $offset = 0, $orderBy = 'id', $order = 'ASC', $groupBy = '')
    {
        if (!$groupBy) {
            $summaries = self::select(DB::raw($selectClause))
                ->whereRaw($whereClause)
                ->skip($offset)
                ->take($limit)
                ->orderBy($orderBy, $order)
                ->get();
        } else {
            $summaries = self::select(DB::raw($selectClause))
                ->whereRaw($whereClause)
                ->groupBy("id")
                ->orderBy($orderBy, $order)
                ->skip($offset)
                ->take($limit)
                ->get();

        }
        return $summaries;
    }

    public function deleteSummaries($whereClause)
    {
        $result = self::whereRaw($whereClause)->delete();

        return $result;
    }

}
