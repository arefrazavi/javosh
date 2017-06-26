<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['type'];

    /**
     * @param string $typeValue
     * @param int $id
     * @return mixed
     */
    public static function fetch($typeValue = '', $id = 0)
    {
        if ($id != 0) {
            $type = self::find($id);
        } else {
            $type = self::where('type', $typeValue)->first();
        }
        return $type;
    }
}
