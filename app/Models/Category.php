<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Category extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'alias',
        'parent_id'
    ];

    /**
     * Get the products of the category
     **
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function aspects()
    {
        return $this->hasMany(Aspect::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }


    public static function fetch($id = 0, $title = '')
    {
        if ($id != 0) {
            $category = self::find($id);
        } else {
            $category = self::where('title', $title)->first();
        }
        return $category;
    }

    public static function fetchAncestors($categoryId)
    {
        $category = self::fetch($categoryId);
        $parentId = $category->parent_id;
        $ancestors = [];
        while ($parentId != 0) {
            $ancestor = self::fetch($parentId);
            $ancestors[] = $ancestor;
            $parentId = $ancestor->parent_id;
        }
        return $ancestors;
    }

    public static function fetchDescendants($categoryId)
    {
        $descendants = [];
        $whereRaw = "categories.parent_id = " . $categoryId;
        $categories = self::fetchCategories("categories.*", $whereRaw);
        $new = 1;
        while ($new) {
            $newCategories = [];
            $new = 0;
            foreach ($categories as $category) {
                $descendants[] = $category;
                $whereRaw = "categories.parent_id = " . $category->id;
                $newCats = self::fetchCategories("categories.*", $whereRaw);
                foreach ($newCats as $newCat) {
                    $newCategories[] = $newCat;
                }
            }
            if (sizeof($newCategories)) {
                $new = 1;
                $categories = $newCategories;
            }
        }

        return $descendants;
    }


    public static function fetchCategories($selectRaw = "categories.*, c2.alias as parent_alias", $whereRaw = "1", $limit = PHP_INT_MAX,
                                           $offset = 0, $orderBy = "categories.id", $order = "ASC")
    {
        $categories = self::select(DB::raw($selectRaw))
            ->leftjoin('categories as c2', 'c2.id', '=', 'categories.parent_id')
            ->whereRaw($whereRaw)
            ->skip($offset)
            ->take($limit)
            ->orderBy($orderBy, $order)
            ->get();

        return $categories;
    }


}
