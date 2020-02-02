<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\data;

use DmitriiKoziuk\yii2Shop\entities\Brand;
use DmitriiKoziuk\yii2Shop\entities\Category;

class CategoryData
{
    /**
     * @var Category
     */
    private $_categoryRecord;

    public function __construct(Category $categoryRecord)
    {
        $this->_categoryRecord = $categoryRecord;
    }

    public function isHasChildrenCategories()
    {
        return empty($this->_categoryRecord->children) ? false : true;
    }

    public function isProductsShow(): bool
    {
        return (boolean) $this->_categoryRecord->is_products_show;
    }

    public function isTemplateNameSet(): bool
    {
        return ! is_null($this->_categoryRecord->template_name);
    }

    public function getId(): int
    {
        return $this->_categoryRecord->id;
    }

    public function getName(): string
    {
        return $this->_categoryRecord->name;
    }

    public function getNameOnSite(): string
    {
        return ! empty($this->_categoryRecord->name_on_site) ? $this->_categoryRecord->name_on_site : $this->getName();
    }

    public function getUrl(): string
    {
        return $this->_categoryRecord->urlEntity->url;
    }

    public function getDescription(): string
    {
        return $this->_categoryRecord->description ?? '';
    }

    public function getMetaTitle(array $filteredAttributes, Brand $filteredBrand = null): string
    {
        if (
            (! empty($filteredAttributes) || ! empty($filteredBrand)) &&
            $this->_categoryRecord->isFilteredTitleTemplateSet()
        ) {
            return $this->_categoryRecord->getFilteredTitle($filteredAttributes, $filteredBrand);
        }
        return $this->_categoryRecord->meta_title ?? '';
    }

    public function getMetaDescription(array $filteredAttributes, Brand $filteredBrand = null): string
    {
        if (
            (! empty($filteredAttributes) || ! empty($filteredBrand)) &&
            $this->_categoryRecord->isFilteredDescriptionTemplateSet()
        ) {
            return $this->_categoryRecord->getFilteredDescription($filteredAttributes, $filteredBrand);
        }
        return $this->_categoryRecord->meta_description ?? '';
    }

    public function getH1(array $filteredAttributes, Brand $filteredBrand = null): string
    {
        if (
            (! empty($filteredAttributes) || ! empty($filteredBrand)) &&
            $this->_categoryRecord->isFilteredH1TemplateSet()
        ) {
            return $this->_categoryRecord->getFilteredH1($filteredAttributes, $filteredBrand);
        }
        return $this->getNameOnSite();
    }

    /**
     * @return array
     */
    public function getBreadcrumb(): array
    {
        $breadcrumbs = [];
        $parentCategories = $this->_categoryRecord->parents;
        foreach ($parentCategories as $parentCategory) {
            $breadcrumbs[] = [
                'label' => $parentCategory->getFrontendName(),
                'url' => $parentCategory->urlEntity->url,
            ];
        }
        $breadcrumbs[] = [
            'label' => $this->_categoryRecord->getFrontendName(),
        ];
        return $breadcrumbs;
    }

    /**
     * @return Category[]
     */
    public function getChildrenCategories(): array
    {
        return $this->_categoryRecord->children;
    }

    public function getDirectoryChildrenCategories(): array
    {
        return $this->_categoryRecord->directChildren;
    }

    public function getTemplateName(): string
    {
        return $this->_categoryRecord->template_name;
    }
}
