<?php 
	header('HTTP/1.1 404 Not Found'); 
	header('status: 404 Not Found');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>China Wholesale - Cheap Free Shipping Wedding Dresses, Gowns, Bridesmaid Dresses, Prom Dresses, Intimate Apparel, Cosplay Costumes and Sexy Shoes   - Milanoo.com</title>
<style type="text/css">
*{ margin:0; padding:0;}
.weihu{ width:650px; height:280px; border:#FFC485 dashed 1px; margin:0 auto; margin-top:50px; font-family:Arial, Helvetica, sans-serif;}
.weihu h2{ height:40px; padding-top:20px; padding-left:20px; color:#784208; font-size:24px;}
.weihu img{ margin:20px 0 0 10px; float:left;}
.weihu p{ line-height:30px; font-size:14px; padding-top:30px; padding-right:10px; color:#000;}
.weihu h3{ clear:both; float:none; font-weight:normal; font-size:14px; color:#666; text-align:center;}
</style>
</head>

<body>
<div class="weihu">
<h2>404 Error!!</h2>
<img src="/gantanhao.jpg" />
<p>Sorry, the page you're looking for cannot be found.<br/>
You will be redirected to our homepage in <span id='sec' style=" color:blue;">5</span> seconds, or you can go to <a href="http://www.milanoo.com">MILANOO.COM</a>  directly. </p>
<h3>Copyright Notice @ 2008-2010 Milanoo.com All rights reserved </h3></div>
<script>
 var sec = document.getElementById('sec');
 var time=5; 
 function secFunc(){
	--time;
	if(time==0){
		location.href='<?php echo ROOT_URL?>';	
	}else{
		sec.innerHTML=time;
	} 
 }
 setInterval('secFunc()',1000);
</script>
</body>
</html>
<?php exit();?>