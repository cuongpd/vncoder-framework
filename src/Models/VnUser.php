<?php

namespace VnCoder\Models;

class VnUser extends VnModelBase
{
    protected $table = '__users';

    protected $fillable = ["role","nickname","token","name","email","password","birthday","gender","address","phone","avatar","provider","authentication","sender","reset_token","reset_expiration"];

    public static function isLogin()
    {
        return (bool) session('user_id');
    }


}