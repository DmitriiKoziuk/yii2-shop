<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\widgets\frontend;

use yii\base\Widget;
use yii\data\Pagination;
use yii\helpers\Url;

class LinkPagerWidget extends Widget
{
    /**
     * @var Pagination the pagination object that this pager is associated with.
     * You must set this property in order to make LinkPager work.
     */
    public $pagination;

    /**
     * @var string
     */
    public $indexPageUrl;

    /**
     * @var array
     */
    public $filterParams;

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
            if ($i == 1) {
                $url = Url::to([
                    '/customUrl/create',
                    'url' => $this->indexPageUrl,
                    'filterParams' => $this->filterParams,
                ]);
            } else {
                $url = Url::to([
                    '/customUrl/create',
                    'url' => $this->indexPageUrl,
                    'filterParams' => $this->filterParams,
                    'getParams' => [
                        'page' => $i,
                    ],
                ]);
            }
            $isActive = $i == ($currentPage + 1) ? true : false;
            $buttons[] = [
                'label' => $i,
                'url' => $url,
                'active' => $isActive,
            ];
        }

        return $buttons;
    }
}
