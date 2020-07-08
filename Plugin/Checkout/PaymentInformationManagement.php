<?php

namespace Hevelop\InvoiceRequest\Plugin\Checkout;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Checkout\Model\PaymentInformationManagement as Subject;

class PaymentInformationManagement
{
    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * PaymentInformationManagement constructor.
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param Subject $subject
     * @param $cartId
     * @param PaymentInterface $paymentMethod
     * @param AddressInterface $billingAddress
     * @throws NoSuchEntityException
     */
    public function beforeSavePaymentInformation(
        Subject $subject,
        $cartId,
        PaymentInterface $paymentMethod,
        AddressInterface $billingAddress
    ) {
        $extAttributes = $billingAddress->getExtensionAttributes();

        if (!empty($extAttributes)) {
            $quote = $this->quoteRepository->getActive($cartId);
            $quote->setEcWantInvoice($extAttributes->getEcWantInvoice());

            if ($extAttributes->getEcWantInvoice() == "1") {
                $quote->setEcInvoiceType($extAttributes->getEcInvoiceType());
                $quote->setEcCompany($extAttributes->getEcCompany());
                $quote->setEcVatId($extAttributes->getEcVatId());
                $quote->setEcTaxvat($extAttributes->getEcTaxvat());
                $quote->setSdiCode($extAttributes->getEcSdiCode());
            } else {
                $quote->setEcInvoiceType("");
                $quote->setEcCompany("");
                $quote->setEcVatId("");
                $quote->setEcTaxvat("");
                $quote->setSdiCode("");
            }
        }
    }
}
