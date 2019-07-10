<?php /* Smarty version 2.6.18, created on 2019-07-10 13:50:40
         compiled from charts.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_siderbar', 'charts.html', 251, false),)), $this); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<!-- Required Stylesheets -->
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['media']; ?>
/css/reset.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['media']; ?>
/css/text.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['media']; ?>
/css/fonts/ptsans/stylesheet.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['media']; ?>
/css/fluid.css" media="screen" />

<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['media']; ?>
/css/mws.style.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['media']; ?>
/css/icons/icons.css" media="screen" />

<!-- Demo and Plugin Stylesheets -->
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['media']; ?>
/css/demo.css" media="screen" />

<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['media']; ?>
/plugins/colorpicker/colorpicker.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['media']; ?>
/plugins/jimgareaselect/<?php echo $this->_tpl_vars['media']; ?>
/css/imgareaselect-default.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['media']; ?>
/plugins/fullcalendar/fullcalendar.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['media']; ?>
/plugins/fullcalendar/fullcalendar.print.css" media="print" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['media']; ?>
/plugins/tipsy/tipsy.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['media']; ?>
/plugins/sourcerer/Sourcerer-1.2.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['media']; ?>
/plugins/jgrowl/jquery.jgrowl.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['media']; ?>
/plugins/spinner/spinner.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['media']; ?>
/css/jui/jquery.ui.css" media="screen" />

<!-- Theme Stylesheet -->
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['media']; ?>
/css/mws.theme.css" media="screen" />

<!-- JavaScript Plugins -->

<script type="text/javascript" src="<?php echo $this->_tpl_vars['media']; ?>
/js/jquery-1.7.1.min.js"></script>

<script type="text/javascript" src="<?php echo $this->_tpl_vars['media']; ?>
/plugins/jimgareaselect/jquery.imgareaselect.min.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['media']; ?>
/plugins/jquery.dualListBox-1.3.min.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['media']; ?>
/plugins/jgrowl/jquery.jgrowl.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['media']; ?>
/plugins/jquery.filestyle.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['media']; ?>
/plugins/fullcalendar/fullcalendar.min.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['media']; ?>
/plugins/jquery.dataTables.js"></script>
<!--[if lt IE 9]>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['media']; ?>
/plugins/flot/excanvas.min.js"></script>
<![endif]-->
<script type="text/javascript" src="<?php echo $this->_tpl_vars['media']; ?>
/plugins/flot/jquery.flot.min.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['media']; ?>
/plugins/flot/jquery.flot.pie.min.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['media']; ?>
/plugins/flot/jquery.flot.stack.min.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['media']; ?>
/plugins/flot/jquery.flot.resize.min.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['media']; ?>
/plugins/colorpicker/colorpicker.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['media']; ?>
/plugins/tipsy/jquery.tipsy.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['media']; ?>
/plugins/sourcerer/Sourcerer-1.2.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['media']; ?>
/plugins/jquery.placeholder.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['media']; ?>
/plugins/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['media']; ?>
/plugins/jquery.mousewheel.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['media']; ?>
/plugins/spinner/ui.spinner.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['media']; ?>
/js/jquery-ui.js"></script>

<script type="text/javascript" src="<?php echo $this->_tpl_vars['media']; ?>
/js/mws.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['media']; ?>
/js/demo.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['media']; ?>
/js/themer.js"></script>

<script type="text/javascript" src="<?php echo $this->_tpl_vars['media']; ?>
/js/demo.charts.js"></script>

<title>MWS Admin - Charts</title>

</head>

<body>

	<div id="mws-themer">
    	<div id="mws-themer-hide"></div>
        <div id="mws-themer-content">
        	<div class="mws-themer-section">
	        	<label for="mws-theme-presets">Presets</label> <select id="mws-theme-presets"></select>
            </div>
            <div class="mws-themer-separator"></div>
            <div class="mws-themer-section">
                <ul>
                    <li><span>Base Color</span> <div id="mws-base-cp" class="mws-cp-trigger"></div></li>
                    <li><span>Text Color</span> <div id="mws-text-cp" class="mws-cp-trigger"></div></li>
                    <li><span>Text Glow Color</span> <div id="mws-textglow-cp" class="mws-cp-trigger"></div></li>
                </ul>
            </div>
            <div class="mws-themer-separator"></div>
            <div class="mws-themer-section">
            	<ul>
                    <li><span>Text Glow Opacity</span> <div id="mws-textglow-op"></div></li>
                </ul>
            </div>
            <div class="mws-themer-separator"></div>
            <div class="mws-themer-section">
	            <button class="mws-button red small" id="mws-themer-getcss">Get CSS</button>
            </div>
        </div>
        <div id="mws-themer-css-dialog">
        	<div class="mws-form">
            	<div class="mws-form-row" style="padding:0;">
		        	<div class="mws-form-item">
                    	<textarea cols="auto" rows="auto" readonly="readonly"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

	<div id="mws-header" class="clearfix">
    	<div id="mws-logo-container">
        	<div id="mws-logo-wrap">
            	<img src="<?php echo $this->_tpl_vars['media']; ?>
/images/mws-logo.png" alt="mws admin" />
			</div>
        </div>
        
        <div id="mws-user-tools" class="clearfix">
        	<div id="mws-user-notif" class="mws-dropdown-menu">
            	<a href="#" class="mws-i-24 i-alert-2 mws-dropdown-trigger">Notifications</a>
                <span class="mws-dropdown-notif">35</span>
                <div class="mws-dropdown-box">
                	<div class="mws-dropdown-content">
                        <ul class="mws-notifications">
                        	<li class="read">
                            	<a href="#">
                                    <span class="message">
                                        Lorem ipsum dolor sit amet consectetur adipiscing elit, et al commore
                                    </span>
                                    <span class="time">
                                        January 21, 2012
                                    </span>
                                </a>
                            </li>
                        	<li class="read">
                            	<a href="#">
                                    <span class="message">
                                        Lorem ipsum dolor sit amet
                                    </span>
                                    <span class="time">
                                        January 21, 2012
                                    </span>
                                </a>
                            </li>
                        	<li class="unread">
                            	<a href="#">
                                    <span class="message">
                                        Lorem ipsum dolor sit amet
                                    </span>
                                    <span class="time">
                                        January 21, 2012
                                    </span>
                                </a>
                            </li>
                        	<li class="unread">
                            	<a href="#">
                                    <span class="message">
                                        Lorem ipsum dolor sit amet
                                    </span>
                                    <span class="time">
                                        January 21, 2012
                                    </span>
                                </a>
                            </li>
                        </ul>
                        <div class="mws-dropdown-viewall">
	                        <a href="#">View All Notifications</a>
                        </div>
                    </div>
                </div>
            </div>
            <div id="mws-user-message" class="mws-dropdown-menu">
            	<a href="#" class="mws-i-24 i-message mws-dropdown-trigger">Messages</a>
                <span class="mws-dropdown-notif">35</span>
                <div class="mws-dropdown-box">
                	<div class="mws-dropdown-content">
                        <ul class="mws-messages">
                        	<li class="read">
                            	<a href="#">
                                    <span class="sender">John Doe</span>
                                    <span class="message">
                                        Lorem ipsum dolor sit amet consectetur adipiscing elit, et al commore
                                    </span>
                                    <span class="time">
                                        January 21, 2012
                                    </span>
                                </a>
                            </li>
                        	<li class="read">
                            	<a href="#">
                                    <span class="sender">John Doe</span>
                                    <span class="message">
                                        Lorem ipsum dolor sit amet
                                    </span>
                                    <span class="time">
                                        January 21, 2012
                                    </span>
                                </a>
                            </li>
                        	<li class="unread">
                            	<a href="#">
                                    <span class="sender">John Doe</span>
                                    <span class="message">
                                        Lorem ipsum dolor sit amet
                                    </span>
                                    <span class="time">
                                        January 21, 2012
                                    </span>
                                </a>
                            </li>
                        	<li class="unread">
                            	<a href="#">
                                    <span class="sender">John Doe</span>
                                    <span class="message">
                                        Lorem ipsum dolor sit amet
                                    </span>
                                    <span class="time">
                                        January 21, 2012
                                    </span>
                                </a>
                            </li>
                        </ul>
                        <div class="mws-dropdown-viewall">
	                        <a href="#">View All Messages</a>
                        </div>
                    </div>
                </div>
            </div>
            <div id="mws-user-info" class="mws-inset">
            	<div id="mws-user-photo">
                	<img src="<?php echo $this->_tpl_vars['media']; ?>
/example/profile.jpg" alt="User Photo" />
                </div>
                <div id="mws-user-functions">
                    <div id="mws-username">
                        Hello, John Doe
                    </div>
                    <ul>
                    	<li><a href="#">Profile</a></li>
                        <li><a href="#">Change Password</a></li>
                        <li><a href="index-2.html">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div id="mws-wrapper">
		<div id="mws-sidebar-stitch"></div>
		<div id="mws-sidebar-bg"></div>
        <div id="mws-sidebar">
        	<div id="mws-searchbox" class="mws-inset">
            	<form action="http://www.malijuwebshop.com/themes/mws-admin/charts.html">
                	<input type="text" class="mws-search-input" />
                    <input type="submit" class="mws-search-submit" />
                </form>
            </div>
            <?php echo smarty_function_html_siderbar(array(), $this);?>

        </div>
        
        <div id="mws-container" class="clearfix">
            <div class="container">
            
            	<div class="mws-report-container clearfix">
                	<a class="mws-report" href="#">
                    	<span class="mws-report-icon mws-ic ic-building"></span>
                        <span class="mws-report-content">
                        	<span class="mws-report-title">Floors Climbed</span>
                            <span class="mws-report-value">324</span>
                        </span>
                    </a>

                	<a class="mws-report" href="#">
                    	<span class="mws-report-icon mws-ic ic-sport"></span>
                        <span class="mws-report-content">
                        	<span class="mws-report-title">Calories Burned</span>
                            <span class="mws-report-value down">74%</span>
                        </span>
                    </a>

                	<a class="mws-report" href="#">
                    	<span class="mws-report-icon mws-ic ic-walk"></span>
                        <span class="mws-report-content">
                        	<span class="mws-report-title">Kilometers Walked</span>
                            <span class="mws-report-value">14</span>
                        </span>
                    </a>
                    
                	<a class="mws-report" href="#">
                    	<span class="mws-report-icon mws-ic ic-bug"></span>
                        <span class="mws-report-content">
                        	<span class="mws-report-title">Bugs Fixed</span>
                            <span class="mws-report-value up">22%</span>
                        </span>
                    </a>
                    
                	<a class="mws-report" href="#">
                    	<span class="mws-report-icon mws-ic ic-car"></span>
                        <span class="mws-report-content">
                        	<span class="mws-report-title">Cars Repaired</span>
                            <span class="mws-report-value">77</span>
                        </span>
                    </a>
                </div>
                
            	<div class="mws-panel grid_8">
                	<div class="mws-panel-header">
                    	<span class="mws-i-24 i-graph">Line Charts</span>
                    </div>
                    <div class="mws-panel-body">
                    	<div class="mws-panel-content">
                    		<div id="mws-line-chart" style="width:100%; height:300px; "></div>
                    	</div>
                    </div>
                </div>
                
            	<div class="mws-panel grid_8">
                	<div class="mws-panel-header">
                    	<span class="mws-i-24 i-bar-graph">Bar Chart</span>
                    </div>
                    <div class="mws-panel-body">
                    	<div class="mws-panel-content">
                    		<div id="mws-bar-chart" style="width:100%; height:300px; "></div>
                    	</div>
                    </div>
                </div>
                
            	<div class="mws-panel grid_4">
                	<div class="mws-panel-header">
                    	<span class="mws-i-24 i-chart">Pie Chart 1</span>
                    </div>
                    <div class="mws-panel-body">
                    	<div class="mws-panel-content">
                    		<div id="mws-pie-1" style="width:100%; height:300px; "></div>
                    	</div>
                    </div>
                </div>
                
            	<div class="mws-panel grid_4">
                	<div class="mws-panel-header">
                    	<span class="mws-i-24 i-chart-2">Pie Chart 2</span>
                    </div>
                    <div class="mws-panel-body">
                    	<div class="mws-panel-content">
                    		<div id="mws-pie-2" style="width:100%; height:300px; "></div>
                    	</div>
                    </div>
                </div>                
            </div>
            
            <div id="mws-footer">
            	Copyright &copy; 2014.Company name All rights reserved.More Templates <a href="http://www.cssmoban.com/" target="_blank" title="模板之家">模板之家</a> - Collect from <a href="http://www.cssmoban.com/" title="网页模板" target="_blank">网页模板</a>
            </div>
            
        </div>
    </div>


</body>
</html>