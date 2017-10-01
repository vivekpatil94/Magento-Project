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
 * @package    Ves_BlockBuilder
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\BaseWidget\Helper;

use Magento\Framework\Filesystem\File\ReadFactory;
use Magento\Framework\Filesystem\DriverPool;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Url;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Group Collection
     */
    protected $_groupCollection;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $_storeManager;

    /**
     * BaseWidget config node per website
     *
     * @var array
     */
    protected $_config = [];

    /**
     * Template filter factory
     *
     * @var \Magento\Catalog\Model\Template\Filter\Factory
     */
    protected $_templateFilterFactory;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    protected $_readFactory;
     /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filesystem;
    protected $_coreRegistry;
    /** @var UrlBuilder */
    protected $actionUrlBuilder;
    protected $_moduleList;

    /**
     * Filter manager
     *
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filter;

	public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Filter\FilterManager $filter,
        Url $actionUrlBuilder,
        ReadFactory $readFactory
        ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_filterProvider = $filterProvider;
        $this->_readFactory = $readFactory;
        $this->_filesystem = $filesystem;
        $this->_coreRegistry = $registry;
        $this->actionUrlBuilder = $actionUrlBuilder;
        $this->_moduleList      = $moduleList;
        $this->filter = $filter;
    }
    

    /**
     * Return brand config value by key and store
     *
     * @param string $key
     * @param \Magento\Store\Model\Store|int|string $store
     * @return string|null
     */
    public function getConfig($key, $default = null, $group = "vesbasewidget", $store = null)
    {
        $store = $this->_storeManager->getStore($store);
        $websiteId = $store->getWebsiteId();

        $result = $this->scopeConfig->getValue(
            $group.'/'.$key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store);
        return $result;
    }

    public function getCoreRegistry(){
        return $this->_coreRegistry;
    }
    
    public function filter($str)
    {
        $html = $this->_filterProvider->getPageFilter()->filter($str);
        return $html;
    }

    public function getRootDirPath( $path_type = "") {
        $path_type = $path_type?$path_type:DirectoryList::PUB;
        return $this->_filesystem->getDirectoryRead($path_type)->getAbsolutePath();
    }

    public function getDefaultProductLayout() {
        $result = "";
        $folder = $this->getRootDirPath()."pagebuilder".DIRECTORY_SEPARATOR."product_profiles".DIRECTORY_SEPARATOR;
        $filepath = $folder."default_layout.json";
        $file_name = "default_layout.json";

        if(!file_exists($filepath)) {
            $filepath = $folder."default.json";
            $file_name = "default.json";
            if(!file_exists($filepath)) {
                $filepath = false;
            }
        }
        $result = $this->readSampleFile( $file_name, $filepath );
        return $result;
    }

    /**
    * @string type: block, page, product
    *
    */
    public function getSampleLayoutParams( $type = "block" ) {
        $result = array();
        $file_ext = ".json";
        $folder = $this->getRootDirPath()."pagebuilder".DIRECTORY_SEPARATOR;

        switch ($type) {
            case 'product':
                $folder .= "product_profiles".DIRECTORY_SEPARATOR;//Load sample profile of product when we are managing product layout builder
                break;
            case 'page':
                $folder .= "page_profiles".DIRECTORY_SEPARATOR;//Load sample profile of page when we are managing page layout builder
                break;
            default:
                $folder .= "block_profiles".DIRECTORY_SEPARATOR;//Load sample profile of block builder
                break;
        }

        $dirs = glob( $folder.'*'.$file_ext );
        if($dirs) { //load 
            foreach($dirs as $dir) {
                $file_name = basename( $dir );
                $filepath = $folder.$file_name;
                $file_name = str_replace(array(" ","."), "-", $file_name);
                $result[$file_name] = $this->readSampleFile($file_name, $filepath);
            }
        }

        return $result;
    }

    public function getBackupLayouts($folder_name = "vespagebuilder") {
        $result = array();
        $file_ext = ".json";
        $folder = $this->getRootDirPath(DirectoryList::VAR_DIR)."{$folder_name}".DIRECTORY_SEPARATOR;

        $dirs = glob( $folder.'*'.$file_ext );
        if($dirs) { //load 
            foreach($dirs as $dir) {
                $file_name = basename( $dir );
                $filepath = $folder.$file_name;
                $file_name = str_replace(array(" ","."), "-", $file_name);
                $result[$file_name] = $this->readSampleFile($file_name, $filepath);
            }
        }

        return $result;
    }

    public function readSampleFile($file_name, $filepath = "") {
        $result = "";
        if($filepath) {
            $fileReader = $this->_readFactory->create($filepath, DriverPool::FILE);
            $result = $fileReader->readAll($file_name);
        }
        return $result;
    }

    public function getGenerateWidgetUrl() {
        return $this->actionUrlBuilder->getDirectUrl( "vespagebuilder/ajax/widget" );
    }

    public function checkModuleInstalled($moduleName){
        return $this->_moduleList->has($moduleName);
    }

    public function truncateString($string, $maxLength, $etc = '', $remainder = '', $breakWords = true ) {
        $truncatedValue = $this->filter->truncate($string, ['length' => $maxLength, 'etc' => $etc, 'remainder' => $remainder, 'breakWords' => $breakWords]);

        return $truncatedValue;
    }
}