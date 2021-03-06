<?php /* Smarty version 2.6.18, created on 2015-10-28 16:56:22
         compiled from operate_chart.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_headnav', 'operate_chart.html', 34, false),array('function', 'html_account', 'operate_chart.html', 51, false),array('function', 'html_siderbar', 'operate_chart.html', 55, false),)), $this); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>Charts - Bootstrap Admin</title>
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />    
    <link href="<?php echo $this->_tpl_vars['media']; ?>
/css/amcharts.css" rel="stylesheet" />
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
/css/jquery.visualize.css" rel="stylesheet" />

    <link href="<?php echo $this->_tpl_vars['media']; ?>
/global/plugins/bootstrap-toastr/toastr.min.css" rel="stylesheet" />
    <link href="<?php echo $this->_tpl_vars['media']; ?>
/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />  
    <link href="<?php echo $this->_tpl_vars['media']; ?>
/css/pages/plans.css" rel="stylesheet" /> 

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
				
				<!--<div class="sidebar-extra">
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud.</p>
				</div>  .sidebar-extra -->
				
				<br />
		
			</div> <!-- /span3 -->
			
			
			
			<div class="span9">
				
				<h1 class="page-title">
					<i class="icon-signal"></i>
					Charts					
				</h1>
				<!--date-->
				<div class="widget">
					
					<div class="widget-header">
						<h3>日期</h3>
					</div> <!-- /widget-header -->
														
					<div class="widget-content">
						<form id="edit-profile" class="form-horizontal" method="post" action=""/>
						<div class="controls" id="actdatepicker">
							<input type="text" class="input-medium" name="startdate" value="<?php echo $this->_tpl_vars['startdate']; ?>
" />-----
							<input type="text" class="input-medium" name="endate" value="<?php echo $this->_tpl_vars['endate']; ?>
" />
							<button type="button" class="btn btn-primary savebtn">查询</button>
						</div>
						</form>
										
					</div> <!-- /widget-content -->
					
				</div> <!-- /widget -->
				
				
				<!--pic1-->
				<div class="widget">
					
					<div class="widget-header">
						<h3>最高最低点</h3>
					</div> <!-- /widget-header -->
														
					<div class="widget-content">
						
						<div id="chartdiv1" class="chart-holder"></div> <!-- /donut-chart -->
						
						
										
					</div> <!-- /widget-content -->
					
				</div> <!-- /widget -->
				
				<!--pic2-->
				<div class="widget">
					
					<div class="widget-header">
						<h3>开盘收盘价</h3>
					</div> <!-- /widget-header -->
														
					<div class="widget-content">
						
						<div id="chartdiv2" class="chart-holder"></div> <!-- /donut-chart -->
						
						
										
					</div> <!-- /widget-content -->
					
				</div> <!-- /widget -->
				
				<!--pic3-->
				<div class="widget">
					
					<div class="widget-header">
						<h3>最高最低幅度</h3>
					</div> <!-- /widget-header -->
														
					<div class="widget-content">
						
						<div id="chartdiv3" class="chart-holder"></div> <!-- /donut-chart -->
						
						
										
					</div> <!-- /widget-content -->
					
				</div> <!-- /widget -->
				
				<!--pic4-->
				<div class="widget">
					
					<div class="widget-header">
						<h3>开盘收盘幅度</h3>
					</div> <!-- /widget-header -->
														
					<div class="widget-content">
						
						<div id="chartdiv4" class="chart-holder"></div> <!-- /donut-chart -->
						
						
										
					</div> <!-- /widget-content -->
					
				</div> <!-- /widget -->
				
			</div> <!-- /span9 -->
			
			
		</div> <!-- /row -->
		
	</div> <!-- /container -->
	
</div> <!-- /content -->
					
	
<div id="footer">
	
	<div class="container">				
		<hr />
	</div> <!-- /container -->
	
</div> <!-- /footer -->


    

<!-- Le javascript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="<?php echo $this->_tpl_vars['media']; ?>
/js/jquery-1.7.2.min.js"></script>
<script src="<?php echo $this->_tpl_vars['media']; ?>
/js/excanvas.min.js"></script>
<script src="<?php echo $this->_tpl_vars['media']; ?>
/js/bootstrap.js"></script>
<script src="<?php echo $this->_tpl_vars['media']; ?>
/js/amcharts.js"></script>
<script src="<?php echo $this->_tpl_vars['media']; ?>
/js/bootstrap-datetimepicker.js"></script>
<script src="<?php echo $this->_tpl_vars['media']; ?>
/js/bootstrap-datetimepicker.zh-CN.js"></script>
<script src="<?php echo $this->_tpl_vars['media']; ?>
/js/assets/global/plugins/jquery-validation/js/jquery.validate.min.js?v=1.0.0"></script>
<script src="<?php echo $this->_tpl_vars['media']; ?>
/js/assets/global/plugins/jquery-form/jquery-form.min.js"></script>
<script src="<?php echo $this->_tpl_vars['media']; ?>
/global/plugins/bootstrap-toastr/toastr.min.js"></script>
<script type="text/javascript">
<!--pic1-->
	var chart;
	var chartData_1 =<?php echo $this->_tpl_vars['history']; ?>
;
	
	AmCharts.ready(function () {

		// SERIAL CHART    
		chart = new AmCharts.AmSerialChart();
		chart.pathToImages = "<?php echo $this->_tpl_vars['media']; ?>
/images/amcharts/";
	   chart.zoomOutButton = {
		   backgroundColor: '#000000',
		   backgroundAlpha: 0.15
		};
		chart.backgroundColor= '#FFFFFF';
		chart.backgroundAlpha= 0.95;
		chart.dataProvider = chartData_1;
		chart.categoryField = "date";
		chart.addTitle("最高最低点", 15);
		// AXES
		// X轴
		var categoryAxis = chart.categoryAxis;
		categoryAxis.labelRotation = 45;
		categoryAxis.dashLength = 1;
		categoryAxis.gridAlpha = 0.15;
		categoryAxis.axisColor = "#DADADA";

		// Y轴               
		var valueAxis = new AmCharts.ValueAxis();
		valueAxis.axisColor = "#DADADA";
		valueAxis.dashLength = 1;
		valueAxis.axisThickness=1;
		valueAxis.precision=2;
		//valueAxis.stackType="0%";
		//valueAxis.minimum = 40.01;
		//valueAxis.integersOnly=false;
	   // valueAxis.logarithmic = true; // this line makes axis logarithmic
		chart.addValueAxis(valueAxis);
		
		var graph = new AmCharts.AmGraph();
		graph.type = "smoothedLine";
		graph.bullet = "round";
		graph.bulletColor = "#f04848";
		graph.bulletBorderColor = "#f04848";
		graph.bulletBorderThickness = 2;
		graph.bulletSize = 2;
		graph.balloonText = "最高: [[top_high]]";
		graph.title = "最高";
		graph.valueField = "top_high";
		graph.lineThickness = 2;
		graph.lineColor = "#f04848";
		chart.addGraph(graph);
		
		var graph = new AmCharts.AmGraph();
		graph.type = "smoothedLine";
		graph.bullet = "round";
		graph.bulletColor = "#00cc0B";
		graph.bulletBorderColor = "#00cc0B";
		graph.bulletBorderThickness = 2;
		graph.bulletSize = 2;
		graph.balloonText = "最低: [[top_low]]";
		graph.title = "最低";
		graph.valueField = "top_low";
		graph.lineThickness = 2;
		graph.lineColor = "#00cc0B";
		chart.addGraph(graph);
		
		// CURSOR
		var chartCursor = new AmCharts.ChartCursor();
		chartCursor.cursorPosition = "mouse";
		chart.addChartCursor(chartCursor);

		// SCROLLBAR
		var chartScrollbar = new AmCharts.ChartScrollbar();
		chart.addChartScrollbar(chartScrollbar);
			
		 // LEGEND
		var legend = new AmCharts.AmLegend();
		legend.markerType = "circle";
		chart.addLegend(legend);
		// WRITE
		chart.write("chartdiv1");
	});
	
<!--pic2-->
	var chart;
	var chartData_2 =<?php echo $this->_tpl_vars['history']; ?>
;
	
	AmCharts.ready(function () {

		// SERIAL CHART    
		chart = new AmCharts.AmSerialChart();
		chart.pathToImages = "<?php echo $this->_tpl_vars['media']; ?>
/images/amcharts/";
	   chart.zoomOutButton = {
		   backgroundColor: '#000000',
		   backgroundAlpha: 0.15
		};
		chart.backgroundColor= '#FFFFFF';
		chart.backgroundAlpha= 0.95;
		chart.dataProvider = chartData_2;
		chart.categoryField = "date";
		chart.addTitle("开盘收盘价", 15);
		// AXES
		// X轴
		var categoryAxis = chart.categoryAxis;
		categoryAxis.labelRotation = 45;
		categoryAxis.dashLength = 1;
		categoryAxis.gridAlpha = 0.15;
		categoryAxis.axisColor = "#DADADA";

		// Y轴               
		var valueAxis = new AmCharts.ValueAxis();
		valueAxis.axisColor = "#DADADA";
		valueAxis.dashLength = 1;
		valueAxis.axisThickness=1;
		valueAxis.precision=2;
		//valueAxis.stackType="0%";
		//valueAxis.minimum = 40.01;
		//valueAxis.integersOnly=false;
	   // valueAxis.logarithmic = true; // this line makes axis logarithmic
		chart.addValueAxis(valueAxis);
		
		var graph = new AmCharts.AmGraph();
		graph.type = "smoothedLine";
		graph.bullet = "round";
		graph.bulletColor = "#FCD202";
		graph.bulletBorderColor = "#FCD202";
		graph.bulletBorderThickness = 2;
		graph.bulletSize = 2;
		graph.balloonText = "开盘: [[top_open]]";
		graph.title = "开盘";
		graph.valueField = "top_open";
		graph.lineThickness = 2;
		graph.lineColor = "#FCD202";
		chart.addGraph(graph);
		
		var graph = new AmCharts.AmGraph();
		graph.type = "smoothedLine";
		graph.bullet = "round";
		graph.bulletColor = "#00cc0B";
		graph.bulletBorderColor = "#00cc0B";
		graph.bulletBorderThickness = 2;
		graph.bulletSize = 2;
		graph.balloonText = "收盘: [[top_close]]";
		graph.title = "收盘";
		graph.valueField = "top_close";
		graph.lineThickness = 2;
		graph.lineColor = "#00cc0B";
		chart.addGraph(graph);
		
		// CURSOR
		var chartCursor = new AmCharts.ChartCursor();
		chartCursor.cursorPosition = "mouse";
		chart.addChartCursor(chartCursor);

		// SCROLLBAR
		var chartScrollbar = new AmCharts.ChartScrollbar();
		chart.addChartScrollbar(chartScrollbar);
			
		 // LEGEND
		var legend = new AmCharts.AmLegend();
		legend.markerType = "circle";
		chart.addLegend(legend);
		// WRITE
		chart.write("chartdiv2");
	});
	
	<!--pic3-->
	var chart;

var chartData_3 = <?php echo $this->_tpl_vars['history']; ?>
;

AmCharts.ready(function () {
	// SERIAL CHART
	chart = new AmCharts.AmSerialChart();
	chart.dataProvider = chartData_3;
	chart.categoryField = "date";
	chart.startDuration = 1;
	chart.backgroundColor= '#FFFFFF';
	chart.backgroundAlpha= 0.95;
	chart.addTitle("最高最低幅度", 15);
	chart.addListener('clickGraphItem', function(a,b){
		//alert(a.item);
	})
	// AXES
	// category
	var categoryAxis = chart.categoryAxis;
	categoryAxis.labelRotation = 45; // this line makes category values to be rotated
	categoryAxis.gridAlpha = 0;
	categoryAxis.fillAlpha = 1;
	categoryAxis.fillColor = "#FAFAFA";
	categoryAxis.gridPosition = "start";

	// value
	var valueAxis = new AmCharts.ValueAxis();
	valueAxis.dashLength = 5;
	valueAxis.title = "行情幅度"
	valueAxis.axisAlpha = 0;
	chart.addValueAxis(valueAxis);

	// GRAPH
	var graph = new AmCharts.AmGraph();
	graph.valueField = "highwidth";
	//graph.colorField = "color";
	graph.lineColor = "#9400D3";
	graph.labelText = "[[highwidth]]";
	graph.balloonText = "[[date]]: [[highwidth]]";
	graph.type = "column";
	graph.urlField = "url";
	//graph.urlTarget = "_blank";
	graph.lineAlpha = 0;
	graph.fillAlphas = 1;
	chart.addGraph(graph);

	// WRITE
	chart.write("chartdiv3");
});
	
	
	<!--pic4-->
	var chart;

var chartData_4= <?php echo $this->_tpl_vars['history']; ?>
;

AmCharts.ready(function () {
	// SERIAL CHART
	chart = new AmCharts.AmSerialChart();
	chart.dataProvider = chartData_3;
	chart.categoryField = "date";
	chart.startDuration = 1;
	chart.backgroundColor= '#FFFFFF';
	chart.backgroundAlpha= 0.95;
	chart.addTitle("开盘收盘幅度", 15);
	chart.addListener('clickGraphItem', function(a,b){
		//alert(a.item);
	})
	// AXES
	// category
	var categoryAxis = chart.categoryAxis;
	categoryAxis.labelRotation = 45; // this line makes category values to be rotated
	categoryAxis.gridAlpha = 0;
	categoryAxis.fillAlpha = 1;
	categoryAxis.fillColor = "#FAFAFA";
	categoryAxis.gridPosition = "start";

	// value
	var valueAxis = new AmCharts.ValueAxis();
	valueAxis.dashLength = 5;
	valueAxis.title = "行情幅度"
	valueAxis.axisAlpha = 0;
	chart.addValueAxis(valueAxis);

	// GRAPH
	var graph = new AmCharts.AmGraph();
	graph.valueField = "openwidth";
	graph.colorField = "color";
	graph.labelText = "[[openwidth]]";
	graph.balloonText = "[[date]]: [[openwidth]]";
	graph.type = "column";
	graph.urlField = "url";
	//graph.urlTarget = "_blank";
	graph.lineAlpha = 0;
	graph.fillAlphas = 1;
	chart.addGraph(graph);

	// WRITE
	chart.write("chartdiv4");
});
</script>
<script>
$(document).ready(function(){
	// 时间控件初始化
	var datepicker = $('#actdatepicker')
	datepicker.children('input').datetimepicker({
	   language:'zh-CN',
	   format: 'yyyy-mm-dd',
	   pickerPosition: "bottom-right",
		weekStart: 1,
		todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		minView: 2,
		forceParse: 0
	});

	// 时间控件初始化
	var timepicker = $('#timepicker')
	timepicker.children('input').datetimepicker({
	   language:'zh-CN',
	   format: 'hh:ii',
	   pickerPosition: "bottom-right",
		format: 'hh:ii',
         startView:1
	});
	
	
	$("#edit-profile").on('click', '.savebtn', function() {
		if ($('#edit-profile').valid()) {
			$('#edit-profile').submit();
		}
	});
	
 }); 
 
 
 
 
       
</script>
</body>
</html>