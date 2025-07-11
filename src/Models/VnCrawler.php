<?php

namespace VnCoder\Models;

use VnCoder\Models\VnModelBase;

class VnCrawler extends VnModelBase
{
    public $timestamps = false;
    protected $table = '__crawler';
    protected $fillable = ['id','source','category','title','description','photo','content','data','status', 'tags'];

    public static function saveData($url, $data)
    {
        $data['source'] = $url;
        return self::updateOrCreate(['source' => $url], $data);
    }

    public static function checkData($url)
    {
        return self::where('source', $url)->first();
    }
}
