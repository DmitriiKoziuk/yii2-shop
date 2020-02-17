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
        $maxRightPageLinksNumber = 3;
        $totalPage = $this->pagination->getPageCount();
        $currentPage = $this->pagination->getPage();

        $buttons = [];
        if ($totalPage > 1) {
            $buttons[ $currentPage ] = $currentPage;

            $destination = ($currentPage - $maxLeftPageLinksNumber);
            for ($i = $currentPage - 1; $i > $destination; $i--)
            {
                $buttons[ $i ] = $i;
            }

            $destination = ($currentPage + $maxRightPageLinksNumber) + 2;
            for ($i = $currentPage + 1; $i < $destination; $i++)
            {
                $buttons[ $i ] = $i;
            }

            ksort($buttons);

            if (array_key_first($buttons) < 1) {
                $overflow = 1 - array_key_first($buttons);
                $buttons = array_slice($buttons, $overflow, null,true);
            }

            if (array_key_last($buttons) > $totalPage) {
                $overflow = (array_key_last($buttons) - $totalPage) * -1;
                $buttons = array_slice($buttons, 0, $overflow,true);
            }

            // First page
            $buttons[1] = [
                'label' => 1,
                'url' => Url::to([
                    '/customUrl/create',
                    'url' => $this->indexPageUrl,
                    'filterParams' => $this->filterParams,
                ]),
                'active' => $this->isCurrentPageActive(1, $currentPage),
            ];

            // Last page
            $buttons[ $totalPage ] = [
                'label' => $totalPage,
                'url' => Url::to([
                    '/customUrl/create',
                    'url' => $this->indexPageUrl,
                    'filterParams' => $this->filterParams,
                    'getParams' => [
                        'page' => $totalPage,
                    ],
                ]),
                'active' => $this->isCurrentPageActive($totalPage, $currentPage),
            ];

            // Other pages
            foreach ($buttons as $key => $number) {
                if (is_numeric($number)) {
                    $buttons[ $number ] = [
                        'label' => $number,
                        'url' => Url::to([
                            '/customUrl/create',
                            'url' => $this->indexPageUrl,
                            'filterParams' => $this->filterParams,
                            'getParams' => [
                                'page' => $number,
                            ],
                        ]),
                        'active' => $this->isCurrentPageActive($number, $currentPage),
                    ];
                }
            }
        }

        ksort($buttons);
        return $buttons;
    }

    private function isCurrentPageActive(int $pageNumber, int $currentPage)
    {
        return ($pageNumber == ($currentPage + 1) ? true : false);
    }
}
