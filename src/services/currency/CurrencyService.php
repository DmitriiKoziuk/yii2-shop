<?php
namespace DmitriiKoziuk\yii2Shop\services\currency;

use yii\db\Connection;
use DmitriiKoziuk\yii2Base\services\EntityActionService;
use DmitriiKoziuk\yii2Base\exceptions\EntityNotFoundException;
use DmitriiKoziuk\yii2Shop\entities\Currency;
use DmitriiKoziuk\yii2Shop\repositories\CurrencyRepository;
use DmitriiKoziuk\yii2Shop\forms\currency\CurrencyInputForm;

class CurrencyService extends EntityActionService
{
    /**
     * @var CurrencyRepository
     */
    private $_currencyRepository;

    public function __construct(
        CurrencyRepository $currencyRepository,
        Connection $db = null
    ) {
        parent::__construct($db);
        $this->_currencyRepository = $currencyRepository;
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
            $this->_currencyRepository->save($currency);
            return $currency;
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function delete(Currency $currency): void
    {

    }
}