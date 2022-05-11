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

/**
 * Interface CartRepositoryInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface CartRepositoryInterface
{

    /**
     * Creaate empty cart for customer
     *
     * @param int $contactPersonId contact person id
     *
     * @return int new cart ID if customer did not have a cart or ID of the existing cart otherwise.
     * @throws \Magento\Framework\Exception\CouldNotSaveException The empty cart and quote could not be created.
     */
    public function createEmptyCartForCustomer($contactPersonId);

    /**
     * Add cart item
     *
     * @param int                                       $contactPersonId contact person id
     * @param \Magento\Quote\Api\Data\CartItemInterface $cartItem        cartitem
     *
     * @return \Magento\Quote\Api\Data\CartItemInterface Item.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     * @throws \Magento\Framework\Exception\CouldNotSaveException The specified item could not be saved to the cart.
     * @throws \Magento\Framework\Exception\InputException The specified item or cart is not valid.
     */
    public function addCartItem($contactPersonId, \Magento\Quote\Api\Data\CartItemInterface $cartItem);

    /**
     * Delete item by id
     *
     * @param int $contactPersonId contact person id
     * @param int $itemId          item id
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified item or cart does not exist.
     * @throws \Magento\Framework\Exception\CouldNotSaveException The item could not be removed.
     */
    public function deleteById($contactPersonId, $itemId);

    /**
     * Get cart
     *
     * @param int $contactPersonId contact person id
     *
     * @return \Magento\Quote\Api\Data\CartInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCart($contactPersonId);

    /**
     * Get shipping method
     *
     * @param int $contactPersonId contact person id
     * @param int $addressId       The estimate address id
     *
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface[] An array of shipping methods.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified quote does not exist.
     * @throws \Magento\Framework\Exception\StateException The shipping address is missing.
     */
    public function getShippingMethod($contactPersonId, $addressId);

    /**
     * Get payment method
     *
     * @param int $contactPersonId contact person id
     *
     * @return \Magento\Quote\Api\Data\PaymentMethodInterface[] Array of payment methods.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     */
    public function getPaymentMethod($contactPersonId);

    /**
     * Assign billing address
     *
     * @param int                                      $contactPersonId contact person id
     * @param \Magento\Quote\Api\Data\AddressInterface $address         address
     * @param bool                                     $useForShipping  use for shipping same
     *
     * @return int Address ID.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     * @throws \Magento\Framework\Exception\InputException The specified cart ID or address data is not valid.
     */
    public function assignBilling($contactPersonId, $address, $useForShipping = false);

    /**
     * Save shipping information
     *
     * @param int                                                     $contactPersonId    contact person id
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation address information
     *
     * @return \Magento\Checkout\Api\Data\PaymentDetailsInterface
     */
    public function saveShippinginformation(
        $contactPersonId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    );


    /**
     * Set payment information and place order for a specified cart.
     *
     * @param int                                           $contactPersonId contact person id
     * @param \Magento\Quote\Api\Data\PaymentInterface      $paymentMethod   payment method
     * @param \Magento\Quote\Api\Data\AddressInterface|null $billingAddress  billing address
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return int Order ID.
     */
    public function savePaymentInformationAndPlaceOrder(
        $contactPersonId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    );

    /**
     * Get orders
     *
     * @param int                                            $contactPersonId contact person id
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria  The search criteria.
     *
     * @return \Magento\Sales\Api\Data\OrderSearchResultInterface Order search result interface
     */
    public function getOrders($contactPersonId, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Get order by id
     *
     * @param int $contactPersonId contact person id
     * @param int $orderId         order id
     *
     * @return \Magento\Sales\Api\Data\OrderInterface Order interface.
     */
    public function getOrderById($contactPersonId, $orderId);

    /**
     * Set coupon code
     *
     * @param int    $contactPersonId contact person id
     * @param string $couponCode      coupon code
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     * @throws \Magento\Framework\Exception\CouldNotSaveException The specified coupon could not be added.
     */
    public function setCouponCode($contactPersonId, $couponCode);


    /**
     * Deletes a coupon from a specified cart.
     *
     * @param int $contactPersonId contact person id
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     * @throws \Magento\Framework\Exception\CouldNotDeleteException The specified coupon could not be deleted.
     */
    public function deleteCouponCode($contactPersonId);

    /**
     * Deletes a coupon from a specified cart.
     *
     * @param int $contactPersonId contact person id
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     * @throws \Magento\Framework\Exception\CouldNotDeleteException The specified coupon could not be deleted.
     */
    public function getCouponCode($contactPersonId);

    /**
     * Get Total
     *
     * @param int $contactPersonId contact person id
     *
     * @return \Magento\Quote\Api\Data\TotalsInterface Quote totals data.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     */
    public function getTotal($contactPersonId);


}
