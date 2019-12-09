<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\services\currency;

use yii\base\Event;
use yii\db\Connection;
use DmitriiKoziuk\yii2Base\services\DBActionService;
use DmitriiKoziuk\yii2Base\exceptions\EntityNotFoundException;
use DmitriiKoziuk\yii2Shop\entities\Currency;
use DmitriiKoziuk\yii2Shop\data\CurrencyData;
use DmitriiKoziuk\yii2Shop\forms\currency\CurrencyInputForm;
use DmitriiKoziuk\yii2Shop\repositories\CurrencyRepository;
use DmitriiKoziuk\yii2Shop\events\CurrencyUpdateEvent;

class CurrencyService extends DBActionService
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
    {
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
                Event::trigger(
                    CurrencyUpdateEvent::class,
                    CurrencyUpdateEvent::EVENT_CURRENCY_UPDATE,
                    new CurrencyUpdateEvent([
                        'changedAttributes' => $changedAttributes,
                        'currencyAttributes' => $currency->getAttributes(),
                    ])
                );
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
     * @return CurrencyData[] index by currency id
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
