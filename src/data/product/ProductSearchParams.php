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

    public function rules()
    {
        return [
            [['categoryIDs'], 'each', 'rule' => ['integer']],
            [['stockStatus'], 'each', 'rule' => ['integer']],
            [['name'], 'string'],
        ];
    }

    public function isNameSet(): bool
    {
        return ! empty($this->name);
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
}