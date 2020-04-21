<?php

namespace Core;

interface ModelInterface
{

    public function setLimit(int $limit);

    public function setSortField(string $field);

    public function setSortOrder(string $order);

    public function add(array $data = []);

    public function update(array $data = []);

    public function count(): int;

    public function findById(int $id);

    public function find(int $page, int $pagePerPage, array $fields = []): array;

}