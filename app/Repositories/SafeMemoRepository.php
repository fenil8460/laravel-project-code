<?php

namespace App\Repositories;

use App\Models\SafeMemo;

class SafeMemoRepository
{
    public function getData()
    {
        return SafeMemo::all();
    }
    public function store($data)
    {
        return SafeMemo::create($data);
    }

    public function show($id)
    {
        return SafeMemo::where('user_id',$id)->get();
    }


}
