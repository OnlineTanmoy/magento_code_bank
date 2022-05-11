<?php
/**
 * Namespace
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Api\ContactPerson;

use Appseconnect\B2BMage\Api\ContactPerson\Data\ContactPersonExtendInterface;

/**
 * Interface ContactPersonRepositoryInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface ContactPersonRepositoryInterface
{

    /**
     * Create customer account.
     * Perform necessary business operations like sending email.
     *
     * @param ContactPersonExtendInterface $contactPerson contact person
     *
     * @return \Appseconnect\B2BMage\Api\ContactPerson\Data\ContactPersonExtendInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createContactPerson(
        ContactPersonExtendInterface $contactPerson
    );
}
