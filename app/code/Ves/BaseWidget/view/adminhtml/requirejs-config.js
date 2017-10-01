/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
 var config = {
 	map: {
 		"*": {
 			vesBaseTinyMceWysiwygSetup: "Ves_BaseWidget/js/wysiwyg/tiny_mce/setup",
 			vesBaseBootstrap: "Ves_BaseWidget/js/bootstrap336/bootstrap",
 			vesBaseColorPicker: "Ves_BaseWidget/js/jquery/jquery.colorpicker",
 			vesBaseElfinderSetup: "Ves_BaseWidget/js/elfinder/js/elfinder.min",
 			vesBaseCommon: "Ves_BaseWidget/js/common",
 			vesBaseJqueryUi: "Ves_BaseWidget/js/jquery/ui/jquery-ui.min",
 			vesBasejQueryCookie: "Ves_BaseWidget/js/jquery/jquery.cookie",
 			vesJqueryNestable: "Ves_BaseWidget/js/jquery/jquery.nestable",
 			vesMageWidget: "Ves_BaseWidget/js/builder/widget",
			vesPageBuilder: "Ves_BaseWidget/js/builder/script",
			vesBootBox: "Ves_BaseWidget/js/jquery/bootbox.min",
			jqueryUi: "jquery/jquery-ui"
 		}
 	},
 	/*
    paths: {
        "jquery/ui": "jquery/jquery-ui"
    },*/
    deps: [
    	'mage/adminhtml/wysiwyg/widget'
    ]
 };