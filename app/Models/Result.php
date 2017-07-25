<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Result extends Model
{
    const PRECISION_MEASURE_ID = 1;
    const RECALL_MEASURE_ID = 2;
    const F_MEASURE_ID = 3;


    protected $table = 'evaluation_results';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'measure_id',
        'category_id',
        'method_id',
        'aspect_id',
        'result'
    ];


    /**
     * Make primary key non-increment
     * @var bool
     */
    public $incrementing = false;

    protected $primaryKey = array('method_id', 'measure_id', 'category_id', 'aspect_id');

    public $timestamps = false;

    public static function updateOrInsert($resultData, $updateData)
    {
        $result = self::updateOrCreate($resultData, $updateData);

        return $result;
    }


    public static function fetchResult($whereClause)
    {
        $results = self::whereRaw($whereClause)->first();

        return $results;
    }


    public static function fetchResults($whereClause)
    {
        $results = self::whereRaw($whereClause)->get();

        return $results;
    }


    /**
     * @return mixed
     */
    public static function fetchEvaluationMeasures()
    {
        $stopWords = DB::table('evaluation_measures')->get();
        return $stopWords;
    }

}
