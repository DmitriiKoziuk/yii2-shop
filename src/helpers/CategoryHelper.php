<?php
namespace DmitriiKoziuk\yii2Shop\helpers;

use DmitriiKoziuk\yii2Shop\entities\Category;

class CategoryHelper
{
    /**
     * @param Category[] $categories
     * @param int $parentId
     * @return Category[] tree
     */
    public static function createCategoryTree(array $categories, int $parentId = null): array
    {
        $tree = [];
        foreach ($categories as $key => $category) {
            if ($category->parent_id === $parentId) {
                unset($categories[$key]);
                $category->children = static::createCategoryTree($categories, $category->id);
                $tree[ $category->id ] = $category;
            }
        }
        return $tree;
    }

    /**
     * @param Category[] $categoryTree
     * @param int|null   $cutOutBranchWithId
     * @param array      $list
     * @param int        $depth
     * @return array [
     *      'id'   => category->id,
     *      'name' => category->name,
     * ],
     */
    public static function categoryTreeToList(
        array $categoryTree,
        int $cutOutBranchWithId = null,
        &$list = [],
        int $depth = 0
    ): array {
        foreach ($categoryTree as $branchId => $category) {
            if ($cutOutBranchWithId !== $branchId) {
                $prefix = '';
                for($i = 0; $i<$depth; $i++) {
                    $prefix .= '-';
                }
                if (! empty($depth)) {
                    $prefix .= ' ';
                }
                $list[ $category->id ] = [
                    'id'   => $category->id,
                    'name' => $prefix . $category->name,
                ];
                if (count($category->children) > 0) {
                    static::categoryTreeToList($category->children, $cutOutBranchWithId, $list, ($depth + 1));
                }
            }
        }
        return $list;
    }
}