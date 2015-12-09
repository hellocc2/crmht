<?php /* Smarty version 2.6.18, created on 2015-12-03 17:21:13
         compiled from operate_history.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_headnav', 'operate_history.html', 9, false),array('function', 'html_account', 'operate_history.html', 25, false),array('function', 'html_siderbar', 'operate_history.html', 29, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

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
					行情回顾					
				</h1>
				
				<div class="widget">
														
					<div class="widget-content">
						<form class="form-inline" align="right">
						  <div class="form-group">
							<input type="text" class="form-control" name="ftime" placeholder="起始日期">——
							<input type="text" class="form-control" name="ftimend" placeholder="结束日期">
							<select class="form-control" style="width:90px">
							  <option value="1">开盘点</option>
							  <option value="2">结算点</option>
							  <option value="3">最高点</option>
							  <option value="4">最低点</option>
							  <option value="5">次高点</option>
							  <option value="6">次低点</option>
							</select>
						    <button type="submit" class="btn btn-default">查找</button>
							<button type="button" class="btn btn-default"><a href="/index.php?module=history&action=Add">添加</a></button>
					      </div>
					  	</form>
						<div class="table-responsive">
						  <table class="table">
						  <thead>
							<tr>
								<th>
									日期
								</th>
								<th>
									星期
								</th>
								<th>
									时间
								</th>
								<th>
									方向
								</th>
								<th>
									点位
								</th>
								<th>
									幅度
								</th>
								<th>
									备注
								</th>
								<th>
									开盘
								</th>
								<th>
									结算
								</th>
								<th>
									事件
								</th>
								<th>
									操作
								</th>
							</tr>
							</thead>
							<tbody>
							<tr>
								<td >
									2015-6-6
								</td>
								<td>
									周二
								</td>
								<td>
									9:00
								</td>
								<td>
									空
								</td>
								<td>
									45.4
								</td>
								<td>
									2美元
								</td>
								<td>
									最高
								</td>
								<td>
									否
								</td>
								<td>
									否
								</td>
								<td>
									无
								</td>
								<td>
									编辑| 删除
								</td>
							</tr>
							<tr>
								<td >
									2015-6-6
								</td>
								<td>
									周二
								</td>
								<td>
									9:00
								</td>
								<td>
									空
								</td>
								<td>
									45.4
								</td>
								<td>
									2美元
								</td>
								<td>
									次高
								</td>
								<td>
									否
								</td>
								<td>
									否
								</td>
								<td>
									EIA
								</td>
								<td>
									编辑| 删除
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

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</html>