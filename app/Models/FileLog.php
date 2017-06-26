<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileLog extends Model
{

    protected $fillable = ['file_name'];

    protected $primaryKey = 'file_name';

    /**
     * @param $fileName
     * @return mixed
     */
    public static function fetch($fileName)
    {
        $file = self::find($fileName);
        //$file = $this::where('file_name', $fileName)->first();
        return $file;
    }

    public static function insert($newFile)
    {
        return self::create($newFile);
    }
}
