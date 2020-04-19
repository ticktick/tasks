<?php

namespace Core;

interface ModelInterface
{

    public function setLimit(int $limit);

    public function setSortField(string $field);

    public function findById(int $id);

    public function find(int $page, int $pagePerPage, array $fields = []);

}