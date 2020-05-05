<?php

namespace Core;

interface ModelInterface
{

    public function setLimit(int $limit): void;

    public function setSortField(string $field): void;

    public function setSortOrder(string $order): void;

    public function add(array $data = []): int;

    public function update(array $data = []): int;

    public function count(): int;

    public function findById(int $id);

    public function find(int $page, int $pagePerPage, array $fields = []): array;

}