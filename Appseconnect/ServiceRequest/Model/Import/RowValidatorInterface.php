<?php

namespace Appseconnect\ServiceRequest\Model\Import;

interface RowValidatorInterface extends \Magento\Framework\Validator\ValidatorInterface
{
    const ERROR_INVALID_TITLE = 'InvalidValueTitle';
    const ERROR_ID_IS_EMPTY = 'Empty';

    /**
     * Initialize validator
     *
     * @return $this
     */
    public function init($context);
}
