<?php

namespace Ves\Themesettings\Plugin;

class ResultPage 
{

    /**
     * Adding the default catalog_product_view_type_ handles as well
     * 
     * @param \Magento\Framework\View\Result\Page $subject
     * @param array $parameters
     * @param type $defaultHandle
     * @return type
     */
    public function beforeAddPageLayoutHandles(
        \Magento\Framework\View\Result\Page $subject, 
        array $parameters = [], 
        $defaultHandle = null)
    {
        
        $arrayKeys = array_keys($parameters);
        if ((count($arrayKeys) == 3) && 
                in_array('id', $arrayKeys) && 
                in_array('sku', $arrayKeys) && 
                in_array('type', $arrayKeys)) {
            
            return [$parameters, 'catalog_product_view'];
        }
    }

}
