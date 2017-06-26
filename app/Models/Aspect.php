<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Aspect extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'keywords',
        'category_id'
    ];

    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @param $aspectData
     * @param array $updateData
     * @return mixed
     */
    public static function updateOrInsert($aspectData, $updateData = [])
    {
        $aspect = self::updateOrCreate($aspectData, $updateData);

        return $aspect;
    }

    /**
     * @param int $id
     * @param string $title
     * @return mixed
     */
    public static function fetch($id = 0, $title = '')
    {
        if ($id != 0) {
            $aspect = self::find($id);
        } else {
            $aspect = self::where('title', $title)->first();
        }
        return $aspect;
    }

    /**
     * @param string $selectRaw
     * @param string $whereClause
     * @param int $limit
     * @param int $offset
     * @param string $orderBy
     * @param string $order
     * @return mixed
     */
    public static function fetchAspects($selectRaw = "*", $whereClause = '1', $limit = PHP_INT_MAX, $offset = 0, $orderBy = 'id', $order = 'ASC')
    {
        $aspects = self::select(DB::raw($selectRaw))
            ->whereRaw($whereClause)
            ->skip($offset)
            ->take($limit)
            ->orderBy($orderBy, $order)
            ->get();

        return $aspects;
    }

}
