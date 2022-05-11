<?php
/**
 * Namespace
 *
 * @category Helper
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Helper\Salesrep;

/**
 * Class Url
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Url
{

    /**
     * UrlInterface
     *
     * @var \Magento\Framework\UrlInterface
     */
    public $urlBuilder;

    /**
     * Url constructor.
     *
     * @param \Magento\Framework\UrlInterface $urlBuilder UrlBuilder
     */
    public function __construct(\Magento\Framework\UrlInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * GetUrl
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->urlBuilder->getUrl(
            '/*/Customergrid', [
            'id' => $this->getRequest()
                ->getParam('id')
            ]
        );
    }
}
