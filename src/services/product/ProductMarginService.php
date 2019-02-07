<?php
namespace DmitriiKoziuk\yii2Shop\services\product;

use yii\db\Connection;
use yii\queue\cli\Queue;
use DmitriiKoziuk\yii2Base\services\EntityActionService;
use DmitriiKoziuk\yii2Shop\repositories\ProductTypeMarginRepository;
use DmitriiKoziuk\yii2Shop\entities\ProductTypeMargin;
use DmitriiKoziuk\yii2Shop\forms\product\ProductMarginUpdateForm;
use DmitriiKoziuk\yii2Shop\forms\product\ProductMarginCompositeUpdateForm;
use DmitriiKoziuk\yii2Shop\data\ProductMarginUpdateData;
use DmitriiKoziuk\yii2Shop\data\ProductTypeMarginData;
use DmitriiKoziuk\yii2Shop\services\currency\CurrencyService;
use DmitriiKoziuk\yii2Shop\jobs\UpdateProductSellPriceJob;

class ProductMarginService extends EntityActionService
{
    /**
     * @var ProductTypeMarginRepository
     */
    private $_productTypeMarginRepository;

    /**
     * @var CurrencyService
     */
    private $_currencyService;

    /**
     * @var ProductTypeService
     */
    private $_productTypeService;

    /**
     * @var Queue
     */
    private $_queue;

    public function __construct(
        ProductTypeMarginRepository $productTypeMarginRepository,
        CurrencyService $currencyService,
        ProductTypeService $productTypeService,
        Queue $queue,
        Connection $db = null
    ) {
        parent::__construct($db);
        $this->_productTypeMarginRepository = $productTypeMarginRepository;
        $this->_currencyService = $currencyService;
        $this->_productTypeService = $productTypeService;
        $this->_queue = $queue;
    }

    public function updateMargins(
        int $productTypeId,
        ProductMarginCompositeUpdateForm $updateCompositeForm
    ): ProductMarginUpdateData {
        $existMarginRecordList = $this->_productTypeMarginRepository->getProductTypeMargins($productTypeId);
        foreach ($updateCompositeForm->getForms() as $updateForm) {
            if ($updateForm->validate()) {
                if (array_key_exists($updateForm->currency_id, $existMarginRecordList)) {
                    $existRecord = $existMarginRecordList[ $updateForm->currency_id ];
                    if (empty($updateForm->margin_value)) {
                        $this->_productTypeMarginRepository->delete($existRecord);
                    } else {
                        $existRecord->setAttributes($updateForm->getUpdatedAttributes());
                        $this->_productTypeMarginRepository->save($existRecord);
                        $this->_queue->push(new UpdateProductSellPriceJob([
                            'productTypeId' => $productTypeId,
                            'currencyId' => $updateForm->currency_id,
                        ]));
                    }
                } elseif (! empty($updateForm->margin_value)) {
                    $newRecord = new ProductTypeMargin();
                    $newRecord->setAttributes($updateForm->getAttributes());
                    $this->_productTypeMarginRepository->save($newRecord);
                    $this->_queue->push(new UpdateProductSellPriceJob([
                        'productTypeId' => $productTypeId,
                        'currencyId' => $updateForm->currency_id,
                    ]));
                }
            }
        }
        $allCurrenciesDataList = $this->_currencyService->getAllCurrencies();
        $productTypeData = $this->_productTypeService->getProductTypeById($productTypeId);
        return new ProductMarginUpdateData(
            $updateCompositeForm,
            $allCurrenciesDataList,
            $productTypeData
        );
    }

    public function getDataForUpdate(int $productTypeId): ProductMarginUpdateData
    {
        $existMarginRecordList = $this->_productTypeMarginRepository->getProductTypeMargins($productTypeId);
        $allCurrenciesDataList = $this->_currencyService->getAllCurrencies();
        $formList = [];
        foreach ($allCurrenciesDataList as $currencyData) {
            $form = new ProductMarginUpdateForm();
            $form->product_type_id = $productTypeId;
            $form->currency_id = $currencyData->getId();
            if (array_key_exists($currencyData->getId(), $existMarginRecordList)) {
                $form->margin_type = $existMarginRecordList[ $currencyData->getId() ]->margin_type;
                $form->margin_value = $existMarginRecordList[ $currencyData->getId() ]->margin_value;
            }
            $formList[] = $form;
        }
        $productTypeData = $this->_productTypeService->getProductTypeById($productTypeId);
        return new ProductMarginUpdateData(
            new ProductMarginCompositeUpdateForm($formList),
            $allCurrenciesDataList,
            $productTypeData
        );
    }

    public function getProductTypeMargins(int $productTypeId): array
    {
        $marginRecordList = $this->_productTypeMarginRepository
            ->getProductTypeMargins($productTypeId);
        $marginDataList = [];
        foreach ($marginRecordList as $marginRecord) {
            $marginDataList[ $marginRecord->currency_id ] = new ProductTypeMarginData($marginRecord);
        }
        return $marginDataList;
    }
}