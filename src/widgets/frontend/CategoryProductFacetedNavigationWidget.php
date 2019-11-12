<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\widgets\frontend;

use yii\base\Widget;
use DmitriiKoziuk\yii2Shop\entities\categoryFaceted\EavAttributeEntity;

class CategoryProductFacetedNavigationWidget extends Widget
{
    /**
     * @var EavAttributeEntity[]
     */
    public $facetedAttributes;

    /**
     * @var EavAttributeEntity[]
     */
    public $filteredAttributes;

    /**
     * @var string
     */
    public $indexPageUrl;

    /**
     * @var array
     */
    public $getParams;

    public function init()
    {
        parent::init();
        $this->removeFilteredAttributes($this->facetedAttributes, $this->filteredAttributes);
    }

    public function run()
    {
        return $this->render('category-product-faceted-navigation', [
            'attributes' => $this->facetedAttributes,
            'indexPageUrl' => $this->indexPageUrl,
            'getParams' => $this->getParams,
        ]);
    }

    /**
     * @param EavAttributeEntity[] $facetedAttributes
     * @param EavAttributeEntity[] $filteredAttributes
     */
    private function removeFilteredAttributes(array &$facetedAttributes, array $filteredAttributes)
    {
        foreach ($facetedAttributes as $key => $facetedAttribute) {
            if (array_key_exists($facetedAttribute->id, $filteredAttributes)) {
                unset($facetedAttributes[ $key ]);
            }
        }
    }
}