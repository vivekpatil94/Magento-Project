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
namespace Ves\BaseWidget\Block\Adminhtml\Form\Field;

use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * HTML select element block with customer groups options
 */
class Sourcelist extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * Customer groups cache
     *
     * @var array
     */
    private $_sourceList;

    /**
     * Flag whether to add group all option or no
     *
     * @var bool
     */
    protected $_addGroupAllOption = true;

    /**
     * @var GroupManagementInterface
     */
    protected $groupManagement;

    /**
     * @var GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param GroupManagementInterface $groupManagement
     * @param GroupRepositoryInterface $groupRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param array $data
     */
    public function __construct(
    	\Magento\Framework\View\Element\Context $context,
    	GroupManagementInterface $groupManagement,
    	GroupRepositoryInterface $groupRepository,
    	SearchCriteriaBuilder $searchCriteriaBuilder,
    	array $data = []
    	) {
    	parent::__construct($context, $data);

    	$this->groupManagement = $groupManagement;
    	$this->groupRepository = $groupRepository;
    	$this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Retrieve allowed customer groups
     *
     * @param int $groupId return name by customer group id
     * @return array|string
     */
    protected function _getSourceList($groupId = null)
    {
    	if ($this->_sourceList === null) {
            $this->_sourceList                      = [];
            $this->_sourceList['latest']       = __('Latest');
            $this->_sourceList['new_arrival']  = __('New Arrival');
            $this->_sourceList['special']      = __('Special');
            $this->_sourceList['most_popular'] = __('Most Popular');
            $this->_sourceList['best_seller']  = __('Best Seller');
            $this->_sourceList['top_rated']    = __('Top Rated');
            $this->_sourceList['random']       = __('Random');
            $this->_sourceList['featured']     = __('Featured');
            $this->_sourceList['deals']        = __('Deals');
    	}
    	return $this->_sourceList;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
    	return $this->setName($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
    	if (!$this->getOptions()) {
    		foreach ($this->_getSourceList() as $groupId => $groupLabel) {
    			$this->addOption($groupId, addslashes($groupLabel));
    		}
    	}
    	return parent::_toHtml();
    }
}