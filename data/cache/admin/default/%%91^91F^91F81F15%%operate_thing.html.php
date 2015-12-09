<?php /* Smarty version 2.6.18, created on 2015-12-03 17:21:08
         compiled from operate_thing.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_headnav', 'operate_thing.html', 35, false),array('function', 'html_account', 'operate_thing.html', 52, false),array('function', 'html_siderbar', 'operate_thing.html', 56, false),)), $this); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>事件分析</title>
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />    
    
    <link href="<?php echo $this->_tpl_vars['media']; ?>
/css/bootstrap.min.css" rel="stylesheet" />
    <link href="<?php echo $this->_tpl_vars['media']; ?>
/css/bootstrap-responsive.min.css" rel="stylesheet" />
    
    
    <link href="<?php echo $this->_tpl_vars['media']; ?>
/css/font-awesome.css" rel="stylesheet" />
    
    <link href="<?php echo $this->_tpl_vars['media']; ?>
/css/adminia.css" rel="stylesheet" /> 
    <link href="<?php echo $this->_tpl_vars['media']; ?>
/css/adminia-responsive.css" rel="stylesheet" /> 
    
    
    <link href="<?php echo $this->_tpl_vars['media']; ?>
/css/pages/faq.css" rel="stylesheet" /> 

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
	
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>

<body>
	
<div class="navbar navbar-fixed-top">
	
	<div class="navbar-inner">
		
		<?php echo smarty_function_html_headnav(array(), $this);?>
 <!-- /container -->
		
	</div> <!-- /navbar-inner -->
	
</div> <!-- /navbar -->




<div id="content">
	
	<div class="container">
		
		<div class="row">
			
			<div class="span3">
				
				<?php echo smarty_function_html_account(array(), $this);?>
<!-- /account-container -->
				
				<hr />
				
				<?php echo smarty_function_html_siderbar(array(), $this);?>
		
				
				
				<hr />
				
				<br />
		
			</div> <!-- /span3 -->
			
			
			
			<div class="span9">
				
				<h1 class="page-title">
					<i class="icon-pushpin"></i>
					事件分析					
				</h1>
				
				<div class="widget">
														
					<div class="widget-content">
						<form class="form-inline" align="right">
						  <div class="form-group">
							<input type="text" class="form-control" name="ftime" placeholder="起始日期">——
							<input type="text" class="form-control" name="ftimend" placeholder="结束日期">
						    <button type="submit" class="btn btn-default">查找</button>
							<button type="button" class="btn btn-default">添加</button>
					      </div>
					  	</form>
						<div class="table-responsive">
						  <table class="table">
						  <thead>
							<tr>
								<th width="210px">
									事件
								</th>
								<th>
									起点
								</th>
								<th>
									起点时间
								</th>
								<th>
									终点
								</th>
								<th>
									终点时间
								</th>
								<th>
									行情幅度
								</th>
								<th>
									时间间隔
								</th>
								<th>
									星期
								</th>
								<th>
									日期
								</th>
							</tr>
							</thead>
							<tbody>
							<tr>
								<td width="210px">
									美联储
								</td>
								<td>
									45
								</td>
								<td>
									20:00
								</td>
								<td>
									47
								</td>
								<td>
									23:00
								</td>
								<td>
									2美元
								</td>
								<td>
									90分钟
								</td>
								<td>
									周二
								</td>
								<td>
									2015-6-6
								</td>
							</tr>
								<tr>
								<td width="210px">
									美联储
								</td>
								<td>
									45
								</td>
								<td>
									20:00
								</td>
								<td>
									47
								</td>
								<td>
									23:00
								</td>
								<td>
									2美元
								</td>
								<td>
									90分钟
								</td>
								<td>
									周二
								</td>
								<td>
									2015-6-6
								</td>
							</tr>
								<tr>
								<td width="210px">
									美联储
								</td>
								<td>
									45
								</td>
								<td>
									20:00
								</td>
								<td>
									47
								</td>
								<td>
									23:00
								</td>
								<td>
									2美元
								</td>
								<td>
									90分钟
								</td>
								<td>
									周二
								</td>
								<td>
									2015-6-6
								</td>
							</tr>
							</tbody>
						  </table>
						</div>
										
					</div> <!-- /widget-content -->
					
				</div> <!-- /widget -->
				
				
				
			</div> <!-- /span9 -->
			
			
		</div> <!-- /row -->
		
	</div> <!-- /container -->
	
</div> <!-- /content -->
					
	
<div id="footer">
	
	<div class="container">				
		<hr />
		<p>&copy; CAICAI2015</p>
	</div> <!-- /container -->
	
</div> <!-- /footer -->


    

<!-- Le javascript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="<?php echo $this->_tpl_vars['media']; ?>
/css/jquery-1.7.2.min.js"></script>


<script src="<?php echo $this->_tpl_vars['media']; ?>
/css/bootstrap.js"></script>
<script src="<?php echo $this->_tpl_vars['media']; ?>
/css/faq.js"></script>

<script>

$(function () {
	
	$('.faq-list').goFaq ();
	
});

</script>

  </body>
</html>