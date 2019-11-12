<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\migrations;

use yii\db\Migration;

/**
 * Class m191022_085254_insert_eav_value_types_data
 */
class m191021_090812_insert_eav_value_types_data extends Migration
{
    private $eavValueTypesTableName = '{{%dk_shop_eav_value_types}}';

    private $eavValueTypeUnitsTableName = '{{%dk_shop_eav_value_type_units}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert($this->eavValueTypesTableName, ['id', 'name', 'code'],
            [
                [1, 'Length', 'length'],
                [2, 'Mass', 'mass'],
                [3, 'Volume', 'volume'],
                [4, 'Frequency', 'frequency'],
                [5, 'Power', 'power'],
                [6, 'Memory', 'memory'],
                [7, 'Area', 'area'],
                [8, 'Time', 'time'],
                [9, 'Torque', 'torque'],
                [10, 'Speed', 'speed'],
            ]
        );
        $this->batchInsert($this->eavValueTypeUnitsTableName, ['value_type_id', 'name', 'abbreviation', 'code'],
            [
                // Length
                [1, 'Nanometre', 'nm', 'nm'],
                [1, 'Micrometre', 'μm', 'um'],
                [1, 'Millimeter', 'mm', 'mm'],
                [1, 'Centimeter', 'cm', 'cm'],
                [1, 'Meter', 'm', 'm'],
                [1, 'Kilometer', 'km', 'km'],
                [1, 'Mile', 'mi', 'mi'],
                [1, 'Yard', 'yd', 'yd'],
                [1, 'Foot', 'ft', 'ft'],
                [1, 'Inch', 'in', 'in'],
                // Mass
                [2, 'Kilogram', 'kg', 'kg'],
                [2, 'Gram', 'g', 'g'],
                [2, 'Pound', 'lbs', 'lbs'],
                [2, 'Ounce', 'oz', 'oz'],
                // Volume
                [3, 'Milliliter', 'ml', 'ml'],
                [3, 'Liter', 'l', 'l'],
                [3, 'Cubic meter', 'm3', 'm3'],
                [3, 'Cubic centimeter', 'cm3', 'cm3'],
                [3, 'Cubic millimeter', 'mm3', 'mm3'],
                [3, 'Centiliter', 'cL', 'cl'],
                // Frequency
                [4, 'Hertz', 'Hz', 'Hz'],
                [4, 'Kilohertz', 'KHz', 'KHz'],
                [4, 'Megahertz', 'MHz', 'MHz'],
                [4, 'Gigahertz', 'GHz', 'GHz'],
                // Power
                [5, 'Horsepower', 'hp', 'hp'],
                [5, 'Milliwatt', 'mW', 'mW'],
                [5, 'Watt', 'W', 'W'],
                [5, 'Kilowatt', 'KW', 'KW'],
                [5, 'Megawatt', 'MW', 'MW'],
                // Memory
                [6, 'Byte', 'B', 'B'],
                [6, 'Kilo byte', 'KB', 'KB'],
                [6, 'Mega byte', 'MB', 'MB'],
                [6, 'Giga byte', 'GB', 'GB'],
                [6, 'Tera byte', 'TB', 'TB'],
                // Area
                [7, 'Square millimetre', 'mm2', 'mm2'],
                [7, 'Square centimetre', 'cm2', 'cm2'],
                [7, 'Square metre', 'm2', 'm2'],
                [7, 'Square kilometre', 'km2', 'km2'],
                // Time
                [8, 'Second', 'sec', 'sec'],
                [8, 'Minute', 'min', 'min'],
                [8, 'Hour', 'hr', 'hr'],
                // Torque
                [9, 'Newton metre', 'N⋅m', 'Nm'],
                // Speed
                [10, 'Metre per second', 'm/s', 'm-s'],
                [10, 'Kilometre per hour', 'km/h', 'km-h'],
                [10, 'Mile per hour', 'km/h', 'km-h'],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable($this->eavValueTypeUnitsTableName);
        $this->truncateTable($this->eavValueTypesTableName);
        return true;
    }
}
