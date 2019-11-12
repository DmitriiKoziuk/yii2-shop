<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\widgets\frontend;

use yii\base\Widget;
use yii\data\Pagination;

class LinkPagerWidget extends Widget
{
    /**
     * @var Pagination the pagination object that this pager is associated with.
     * You must set this property in order to make LinkPager work.
     */
    public $pagination;

    /** @var string */
    public $indexPageUrl;

    public function run()
    {
        $buttons = $this->getPageButtons();
        return $this->render('link-pager', [
            'buttons' => $buttons,
        ]);
    }

    private function getPageButtons()
    {
        $pageCount = $this->pagination->getPageCount();
        if ($pageCount < 2) {
            return [];
        }

        $currentPage = $this->pagination->getPage();
        $buttons = [];
        for ($i = 1; $i <= $pageCount; $i++) {
            $buttons[] = [
                'label' => $i,
                'url' => $i == 1 ? $this->indexPageUrl : $this->indexPageUrl . '?page=' . $i,
                'active' => $i == $currentPage ? true : false,
            ];
        }

        return $buttons;
    }
}