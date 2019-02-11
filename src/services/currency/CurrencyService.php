<?php
namespace DmitriiKoziuk\yii2Shop\services\currency;

use yii\db\Connection;
use yii\queue\cli\Queue;
use DmitriiKoziuk\yii2Base\services\DBActionService;
use DmitriiKoziuk\yii2Base\exceptions\EntityNotFoundException;
use DmitriiKoziuk\yii2Shop\entities\Currency;
use DmitriiKoziuk\yii2Shop\data\CurrencyData;
use DmitriiKoziuk\yii2Shop\forms\currency\CurrencyInputForm;
use DmitriiKoziuk\yii2Shop\repositories\CurrencyRepository;
use DmitriiKoziuk\yii2Shop\jobs\UpdateProductPriceOnSite;

class CurrencyService extends DBActionService
{
    /**
     * @var CurrencyRepository
     */
    private $_currencyRepository;

    /**
     * @var Queue
     */
    private $_queue;

    public function __construct(
        CurrencyRepository $currencyRepository,
        Queue $queue,
        Connection $db = null
    ) {
        parent::__construct($db);
        $this->_currencyRepository = $currencyRepository;
        $this->_queue = $queue;
    }

    /**
     * @param CurrencyInputForm $currencyInputForm
     * @return Currency
     * @throws \Throwable
     */
    public function create(CurrencyInputForm $currencyInputForm): Currency
    {
        try {
            $currency = new Currency();
            $currency->setAttributes($currencyInputForm->getAttributes());
            $this->_currencyRepository->save($currency);
            return $currency;
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    /**
     * @param int $currencyId
     * @param CurrencyInputForm $currencyInputForm
     * @return Currency
     * @throws \Throwable
     */
    public function update(int $currencyId, CurrencyInputForm $currencyInputForm): Currency
    {   //TODO update product sku price when currency changed
        try {
            $currency = $this->_currencyRepository->getCurrencyById($currencyId);
            if (empty($currency)) {
                throw new EntityNotFoundException("Currency with id '{$currencyId}' not found");
            }
            $currency->setAttributes($currencyInputForm->getAttributes());
            $changedAttributes = $currency->getDirtyAttributes();
            $this->_currencyRepository->save($currency);
            if (
                array_key_exists('rate', $changedAttributes) &&
                ! empty($changedAttributes['rate'])
            ) {
                $this->_queue->push(new UpdateProductPriceOnSite([
                    'currencyId' => $currency->id,
                ]));
            }
            return $currency;
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function delete(Currency $currency): void
    {
        //TODO currency delete method.
    }

    /**
     * @return CurrencyData[]
     */
    public function getAllCurrencies(): array
    {
        $currencyRecords = $this->_currencyRepository->getAllCurrencies();
        $currencies = [];
        foreach ($currencyRecords as $currencyRecord) {
            $currencies[ $currencyRecord->id ] = new CurrencyData($currencyRecord);
        }
        return $currencies;
    }

    public function getCurrencyById(int $currencyId): CurrencyData
    {
        $currencyRecord = $this->_currencyRepository->getCurrencyById($currencyId);
        return new CurrencyData($currencyRecord);
    }
}