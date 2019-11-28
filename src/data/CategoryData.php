<?php
namespace DmitriiKoziuk\yii2Shop\data;

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
        return $this->_categoryRecord->name_on_site ?? $this->getName();
    }

    public function getUrl(): string
    {
        return $this->_categoryRecord->url;
    }

    public function getDescription(): string
    {
        return $this->_categoryRecord->description ?? '';
    }

    public function getMetaTitle(): string
    {
        return $this->_categoryRecord->meta_title ?? '';
    }

    public function getMetaDescription(): string
    {
        return $this->_categoryRecord->meta_description ?? '';
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
                'url' => $parentCategory->url,
            ];
        }
        $breadcrumbs[] = [
            'label' => $this->_categoryRecord->getFrontendName(),
        ];
        return $breadcrumbs;
    }
}
