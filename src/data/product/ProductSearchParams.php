<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\data\product;

use yii\base\Model;

class ProductSearchParams extends Model
{
    /**
     * @var array
     */
    public $categoryIDs = [];

    /**
     * @var array of ProductSku entity stock statuses.
     */
    public $stockStatus = [];

    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $limit = 20;

    /**
     * @var null|int
     */
    public $offset;

    public function rules()
    {
        return [
            [['categoryIDs'], 'each', 'rule' => ['integer']],
            [['stockStatus'], 'each', 'rule' => ['integer']],
            [['name'], 'string'],
            [['limit', 'offset'], 'integer'],
        ];
    }

    public function isNameSet(): bool
    {
        return ! empty($this->name);
    }

    public function isOffsetSet(): bool
    {
        return ! empty($this->offset);
    }

    public function getCategoryIDs(): array
    {
        return $this->categoryIDs;
    }

    public function getStockStatuses(): array
    {
        return $this->stockStatus;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }
}