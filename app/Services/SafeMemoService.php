<?php

namespace App\Services;

use App\Repositories\SafeMemoRepository;

class SafeMemoService
{
    protected $safe_memo_repository;

    public function __construct()
    {
        $this->safe_memo_repository = new SafeMemoRepository;
    }

    public function getData()
    {
        return $this->safe_memo_repository->getData();
    }

    public function store($data)
    {
        return $this->safe_memo_repository->store($data);
    }

    public function show($id)
    {
        return $this->safe_memo_repository->show($id);
    }

}
