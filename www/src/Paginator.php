<?php

namespace Core;

class Paginator
{

    private $itemsPerPage;
    private $sortField;
    private $order;
    private $page;
    private $count;
    private $url;

    const ORDER_ASC = 'asc';
    const ORDER_DESC = 'desc';

    private $sortFields = [
        'user_name' => 'По имени',
        'email' => 'По email',
        'status' => 'По статусу',
    ];

    private $orderTitles = [
        self::ORDER_ASC => '↑',
        self::ORDER_DESC => '↓',
    ];

    public function __construct(int $itemsPerPage)
    {
        $this->itemsPerPage = $itemsPerPage;
    }

    private function makeQuery(string $url, string $page, string $field, string $order): string
    {
        return sprintf('%s?page=%s&sort=%s&ord=%s', $url, $page, $field, $order);
    }

    public function getPages(): array
    {
        $pages = [];
        $pagesCount = ceil($this->count / $this->itemsPerPage);
        if($pagesCount < 2){
            return [];
        }
        for ($i = 1; $i <= $pagesCount; $i++) {
            $active = $this->page == $i;
            $pageUrl = $this->makeQuery($this->url, $i, $this->sortField, $this->order);
            $pages[] = [
                'title' => $i,
                'active' => $active,
                'url' => $pageUrl
            ];
        }
        return $pages;
    }

    public function getSortButtons(): array
    {
        $buttons = [];
        foreach ($this->sortFields as $field => $title) {
            $active = false;
            if ($this->sortField == $field) {
                $order = $this->order == self::ORDER_ASC ? self::ORDER_DESC : self::ORDER_ASC;
                $active = true;
            } else {
                $order = self::ORDER_ASC;
            }
            $pageUrl = $this->makeQuery($this->url, $this->page, $field, $order);
            $buttons[] = [
                'title' => $title . ($active ? ' ' . $this->orderTitles[$order] : ''),
                'active' => $active,
                'url' => $pageUrl
            ];
        }
        return $buttons;
    }

    public function setSortField(string $sortField): void
    {
        $this->sortField = $sortField;
    }

    public function getSortField(): string
    {
        return $this->sortField;
    }

    public function setOrder(string $order): void
    {
        $this->order = $order;
    }

    public function getOrder(): string
    {
        return $this->order;
    }

    public function setPage(int $page): void
    {
        $this->page = $page;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}