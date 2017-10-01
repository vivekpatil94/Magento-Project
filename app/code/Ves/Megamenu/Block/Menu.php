<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Megamenu
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Megamenu\Block;

class Menu extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Ves\Megamenu\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Ves\Megamenu\Model\Menu
     */
    protected $_menu;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context      
     * @param \Ves\Megamenu\Helper\Data                        $helper       
     * @param \Ves\Megamenu\Model\Menu                         $menu         
     * @param array                                            $data         
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Ves\Megamenu\Helper\Data $helper,
        \Ves\Megamenu\Model\Menu $menu,
        array $data = []
        ) {
        parent::__construct($context, $data);
        $this->_helper = $helper;
        $this->_menu = $menu;

    }

    public function _toHtml(){
        if(!$this->getTemplate()){
            $this->setTemplate("Ves_Megamenu::widget/menu.phtml");
        }
        $html = $menu = '';
        if ($menuId = $this->getData('id')) {
            $menu = $this->_menu->load((int)$menuId);
            if ($menu->getId() != $menuId) {
                return;
            }
        }elseif($alias = $this->getData('alias')){
            $storeId = $this->_storeManager->getStore()->getId();
            $menu = $this->_menu->setStore($storeId)->load($alias);
            if ($menu->getAlias() != $alias) {
                return;
            }
        }
        if($menu && $menu->getStatus()){
            $this->setData("menu", $menu);
        }
        return parent::_toHtml();
    }
}