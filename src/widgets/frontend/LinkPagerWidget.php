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
        $maxLeftPageLinksNumber = 3;
        $maxRightPageLinksNumber = 4;
        $isActivePageSelected = false;
        $pageCount = $this->pagination->getPageCount();
        if ($pageCount < 2) {
            return [];
        }

        $currentPage = $this->pagination->getPage();
        $buttons = [];
        for ($i = 1; $i <= $pageCount; $i++) {
            $isCurrentPageActive = ($i == ($currentPage + 1) ? true : false);

            if ($i == 1) {
                $url = Url::to([
                    '/customUrl/create',
                    'url' => $this->indexPageUrl,
                    'filterParams' => $this->filterParams,
                ]);
                $buttons[] = [
                    'label' => $i,
                    'url' => $url,
                    'active' => $isCurrentPageActive,
                ];
            }

            if ($i >= ($currentPage - $maxLeftPageLinksNumber)) {
                if ($i != $currentPage && $i != 1) {
                    $url = Url::to([
                        '/customUrl/create',
                        'url' => $this->indexPageUrl,
                        'filterParams' => $this->filterParams,
                        'getParams' => [
                            'page' => $i,
                        ],
                    ]);
                    $buttons[] = [
                        'label' => $i,
                        'url' => $url,
                        'active' => $isCurrentPageActive,
                    ];
                }
            }
            if ($isCurrentPageActive) {
                $isActivePageSelected = true;
            }
            if ($isActivePageSelected) {
                if (0 == --$maxRightPageLinksNumber) {
                    break;
                }
            }
        }

        if (($i - 1) != $pageCount) {
            $url = Url::to([
                '/customUrl/create',
                'url' => $this->indexPageUrl,
                'filterParams' => $this->filterParams,
                'getParams' => [
                    'page' => $pageCount,
                ],
            ]);
            $buttons[] = [
                'label' => $pageCount,
                'url' => $url,
                'active' => false,
            ];
        }

        return $buttons;
    }
}
