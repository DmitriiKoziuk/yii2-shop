<?php
namespace DmitriiKoziuk\yii2Shop\forms\supplier;

class SupplierProductSkuCompositeUpdateForm
{
    /**
     * @var SupplierProductSkuUpdateForm[]
     */
    private $_supplierProductSkuUpdateForms;

    public function load(array $data): bool
    {
        foreach ($data as $key => $d) {
            $updateForm = new SupplierProductSkuUpdateForm();
            $updateForm->setAttributes($d);
            $this->_supplierProductSkuUpdateForms[ $key ] = $updateForm;
        }
        return true;
    }

    public function validate(): bool
    {
        foreach ($this->_supplierProductSkuUpdateForms as $form) {
            if (! $form->validate()) {
                return false;
            }
        }
        return true;
    }

    public function getUpdateForms()
    {
        return $this->_supplierProductSkuUpdateForms;
    }
}