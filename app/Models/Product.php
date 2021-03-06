<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    /**
     * Make primary key non-increment
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'title',
        'description',
        'price',
        'recommendation_count',
        'category_id',

    ];

    /**
     * Get the comments of the products
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return self::hasMany(Comment::class);
    }


    /**
     * Get the comments of the products
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function summaries()
    {
        return self::hasMany(Summary::class);
    }

    /**
     * Get the main category of the products
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function fetch($id)
    {
        $product = self::find($id);
        return $product;
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
    public static function fetchProducts($selectClause = "*", $whereClause = '1', $limit = PHP_INT_MAX, $offset = 0, $orderBy = 'id', $order = 'ASC')
    {
        $products = self::select(DB::raw($selectClause))
            ->whereRaw($whereClause)
            ->skip($offset)
            ->take($limit)
            ->orderBy($orderBy, $order)
            ->get();

        return $products;
    }


    public static function insert($newProduct)
    {
        return self::create($newProduct);
    }

    public static function updateOrInsert($productData, $updateData = [])
    {
        $product = self::updateOrCreate($productData, $updateData);

        return $product;
    }

    /**
     * @param string $selectClause
     * @param int $whereClause
     * @param int $limit
     * @param int $offset
     * @param string $orderBy
     * @return mixed
     */
    public static function fetchProductsWithSummary($selectClause = "*", $whereClause = 1, $limit = PHP_INT_MAX, $offset = 0, $orderBy = "summary_count ASC")
    {
        $products = DB::table('products')
            ->leftjoin('summaries', 'summaries.product_id', '=', 'products.id')
            ->selectRaw($selectClause)
            ->whereRaw($whereClause)
            ->groupBy("id")
            ->orderByRaw($orderBy)
            ->skip($offset)
            ->take($limit)
            ->get();

        return $products;
    }
}
