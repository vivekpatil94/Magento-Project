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
 * @package    Ves_Productlist
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\BaseWidget\Block\Widget;

class CategoryTab extends \Magento\Catalog\Block\Product\AbstractProduct implements \Magento\Widget\Block\BlockInterface
{

    /**
     * Instance of pager block
     *
     * @var \Magento\Catalog\Block\Product\Widget\Html\Pager
     */
    protected $pager;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * Catalog product visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $catalogProductVisibility;

    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * Report Product collection factory
     *
     * @var \Magento\Reports\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_reportCollection;

    /**
     * @var \Magento\Rule\Model\Condition\Sql\Builder
     */
    protected $sqlBuilder;

    /**
     * @var \Magento\CatalogWidget\Model\Rule
     */
    protected $rule;

    /**
     * @var \Magento\Widget\Helper\Conditions
     */
    protected $conditionsHelper;

    /**
     * @var \Ves\Productlist\Model\Product
     */
    protected $_productModel;

    /**
     * @var \Magento\Cms\Model\Block
     */
    protected $_blockModel;

    protected $_conditionCollection;

    /** \Magento\Catalog\Model\Category */
    protected $_categoryModel;

   /**
    * @param \Magento\Catalog\Block\Product\Context                    $context                  
    * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory 
    * @param \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $reportCollection         
    * @param \Magento\Catalog\Model\Product\Visibility                 $catalogProductVisibility 
    * @param \Magento\Framework\App\Http\Context                       $httpContext              
    * @param \Magento\Rule\Model\Condition\Sql\Builder                 $sqlBuilder               
    * @param \Magento\CatalogWidget\Model\Rule                         $rule                     
    * @param \Magento\Widget\Helper\Conditions                         $conditionsHelper         
    * @param \Ves\BaseWidget\Model\Product                            $productModel             
    * @param \Magento\Cms\Model\Block                                  $blockModel               
    * @param array                                                     $data                     
    */
   public function __construct(
    \Magento\Catalog\Block\Product\Context $context,
    \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
    \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $reportCollection,
    \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
    \Magento\Framework\App\Http\Context $httpContext,
    \Ves\BaseWidget\Model\Product $productModel,
    \Magento\Cms\Model\Block $blockModel,
    \Magento\Catalog\Model\Category $categoryModel,
    array $data = []
    ) {
    $this->_productCollectionFactory = $productCollectionFactory;
    $this->_reportCollection = $reportCollection;
    $this->_catalogProductVisibility = $catalogProductVisibility;
    $this->httpContext = $httpContext;
    $this->_productModel = $productModel;
    $this->_blockModel = $blockModel;
    $this->_categoryModel = $categoryModel;
    parent::__construct(
        $context,
        $data
        );
}

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addData([
            'cache_lifetime' => 86400,
            'cache_tags' => [\Ves\BaseWidget\Model\Product::CACHE_CATEGORY_TAG,
            ], ]);
    }

    /**
     * Get key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $conditions = $this->getData('tabs');
        if($tabs = $this->getConfig('tabs')){
            $conditions = $conditions.".".md5($tabs);
        }
        return [
        'VES_BASEWIDGET_CATEGORYTAB_WIDGET',
        $this->_storeManager->getStore()->getId(),
        $this->_design->getDesignTheme()->getId(),
        $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP),
        $conditions
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeToHtml()
    {
        $template = $this->getConfig('block_template');
        $cur_template = $this->getTemplate();
        if($template){
            $this->setTemplate($template);
        }elseif(!$cur_template){
            $this->setTemplate('widget/product_tabs/categorytab.phtml');
        }
        $this->_eventManager->dispatch(
            'ves_basewidget_collection',
            ['tab' => $this]
        );
        return parent::_beforeToHtml();
    }

    public function getConfig($key, $default = '')
    {
        if($this->hasData($key) && $this->getData($key))
        {
            return $this->getData($key);
        }
        return $default;
    }

    public function getCmsBlockModel(){
        return $this->_blockModel;
    }

    public function getTabs(){
        $tabs = $this->getConfig('tabs');
        if($tabs){
            if(base64_decode($tabs, true) == true){
                $tabs = str_replace(" ", "+", $tabs);
                $tabs = base64_decode($tabs);
                if(base64_decode($tabs, true) == true) {
                    $tabs = base64_decode($tabs);
                }
            }
            $tabs = unserialize($tabs);
            if(isset($tabs['__empty'])) unset($tabs['__empty']);
            usort($tabs,function($a, $b){
                if ($a["position"] == $b["position"]) {
                    return 0;
                }
                return ($a["position"] < $b["position"]) ? -1 : 1;
            }); 
            return $tabs;
        }
        return false;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Magento\Catalog\Model\Product::CACHE_TAG];
    }

    public function getCategory($category_id){
        $category = $this->_categoryModel->load($category_id);
        return $category;
    }

    public function getAjaxUrl(){
        return $this->getUrl('productlist/index/categoryProducts');
    }

    public function getProductsBySource($source_key, $config = []){
        $config['pagesize'] = $this->getConfig('number_item',12);
        $collection = $this->_productModel->getProductBySource($source_key, $config);
        return $collection;
    }

    public function getProductHtml($data){
        $template = 'Ves_BaseWidget::widget/product_tabs/items.phtml';

        $html = $this->getLayout()->createBlock('Ves\BaseWidget\Block\ProductList')->setData($data)->setTemplate($template)->toHtml();
        return $html;
    }
}
