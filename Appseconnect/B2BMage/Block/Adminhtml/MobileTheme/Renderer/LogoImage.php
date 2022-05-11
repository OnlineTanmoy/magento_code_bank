<?php
/**
 * Namespace
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Block\Adminhtml\MobileTheme\Renderer;

use Magento\Store\Model\StoreManagerInterface;

/**
 * Class LogoImage
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class LogoImage extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    /**
     * Get category name
     *
     * @return string
     */
    public function getElementHtml()
    {
        // here you can write your code.
        $html = '';

        if ($this->getValue()) {
            $html = $this->getMediaImageHtml($this->getValue());
        }
        return $html;
    }

    /**
     * GetMediaImageHtml
     *
     * @param $imageName ImageName
     *
     * @return string
     */
    public function getMediaImageHtml($imageName)
    {
        $Image = $this->getValue();
        $html = "<img src='".$Image."' height='250px' width='250px'>";
        return $html;
    }
}
