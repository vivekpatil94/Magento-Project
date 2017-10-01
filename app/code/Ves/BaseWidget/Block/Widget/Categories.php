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
 * @package    Ves_BaseWidget
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\BaseWidget\Block\Widget;
use Ves\BaseWidget\Block\AbstractWidget;
use Magento\Catalog\Api\CategoryRepositoryInterface;

class Categories extends AbstractWidget
{	
	protected $_storeManager;
	protected $_blockModel;
	protected $_dataFilterHelper;
	protected $_imageHelper;
	/**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Cms\Model\Block $blockModel,
		/*\Magento\Store\Model\StoreManagerInterface $storeManager,*/
		\Ves\BaseWidget\Helper\Data $dataHelper,
		\Ves\BaseWidget\Helper\Image $imageHelper,
		CategoryRepositoryInterface $categoryRepository,
		array $data = []
		) {
		parent::__construct($context, $blockModel, $dataHelper, $data);

		$this->_blockModel = $blockModel;
		$this->_dataFilterHelper = $dataHelper;
		$this->_imageHelper = $imageHelper;
		/*$this->_storeManager = $storeManager;*/
		$this->categoryRepository = $categoryRepository;

		if($this->hasData("template")) {
        	$my_template = $this->getData("template");
        } elseif($this->hasData("template_layout")){
        	$my_template = $this->getData("template_layout");
        }else{
 			$my_template = "widget/categories.phtml";
 		}

        $this->setTemplate($my_template);
	}

	public function getBaseMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    public function getCategoryInfo( $categoryId = 0 ){
    	if(!$categoryId)
    		return false;

    	try {
            $category = $this->categoryRepository->get($categoryId, $this->_storeManager->getStore()->getId());
        } catch (NoSuchEntityException $e) {
            return false;
        }

        return $category;
    }

	public function getCategoryImage($category = null, $width = 300, $height = 300)
	{
		if(empty($category) && !is_object($category)) return "";

		$_file_name = $category->getImage();
		
		$_file_path = $this->getBaseMediaUrl() ."catalog/category/".$_file_name;
		
		if($_file_name) {
			return $this->_imageHelper->resizeImage($_file_path, (int)$width, (int)$height);
		}
		return "";
	} 

	public function _toHtml(){
		if(!$this->getDataFilterHelper()->getConfig('general/show')) return;

		$widget_heading = $this->getConfig("title");
		$cms = "";

 		$cms_block_id = $this->getConfig('cmsblock');
 		if($cms_block_id){
 			$cms = $this->_blockModel->load($cms)->getContent();
		 	$cms = $this->_dataFilterHelper->filter($cms);
 		}

		$catsid = $this->getConfig("catsid");
		$pretext = $this->getConfig("pretext");
		$pretext = str_replace(" ","+",$pretext);
		if(base64_decode($pretext, true) == true){
		   	$pretext = base64_decode($pretext);
		} else {
			$pretext = $this->getConfig("pretext");
		}

		$this->assign('pretext', $pretext);
		$this->assign('cms', $cms);
		$this->assign('catsid', $catsid);
		$this->assign('autoplay', $this->getConfig("autoplay"));
		$this->assign('interval', $this->getConfig("interval"));
		$this->assign('image_width', $this->getConfig("image_width"));
		$this->assign('image_height', $this->getConfig("image_height"));
		$this->assign('enable_numbproduct', $this->getConfig("enable_numbproduct"));
		$this->assign('enable_carousel', $this->getConfig("enable_carousel"));
		$this->assign('itemsperpage', $this->getConfig("page_limit"));
		$this->assign('show_navigator', $this->getConfig("show_navigator"));
		$this->assign('cols', $this->getConfig("cols"));
		$this->assign('cate_image', $this->getConfig("cate_image"));
		$this->assign('enable_image', $this->getConfig("enable_image"));
		$this->assign('widget_heading', $widget_heading);
		$this->assign('addition_cls', $this->getConfig('addition_cls'));
		$this->assign('stylecls', $this->getConfig('stylecls'));
		$this->assign('animation', $this->getConfig('animation'));

		return parent::_toHtml();
	}
	
}