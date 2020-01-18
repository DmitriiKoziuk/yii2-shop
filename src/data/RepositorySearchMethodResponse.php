<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\data;

use DmitriiKoziuk\yii2Shop\interfaces\RepositorySearchMethodResponseInterface;

class RepositorySearchMethodResponse implements RepositorySearchMethodResponseInterface
{
    /**
     * @var int
     */
    private $totalCount;

    /**
     * @var array
     */
    private $items;

    public function __construct(int $totalCount, array $items)
    {
        $this->totalCount = $totalCount;
        $this->items = $items;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    public function getItems(): array
    {
        return $this->items;
    }
}