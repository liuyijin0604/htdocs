<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="wkhtmltopdf" content="--dpi 100 --page-width 80 --page-height 40 -T 2 -R 2 -B 2 -L 2 -O Portrait" win-only="--disable-smart-shrinking" />
<meta name="wkhtmltoimage" content="--disable-smart-width --width 500 --quality 80" />
<title>身份证实名验证</title>
<style type="text/css">
*{ margin: 0; padding: 0; letter-spacing: normal !important; }
body{ font-family: Arial, sans-serif; font-size: 16px; text-rendering: optimize-speed; width: 480px; padding: 10px; }
table td{ padding: 5px; border-bottom: 1px solid #e1e1e1; }
td.tbg {background: #e1e1e1; }
</style>
</head>
<body width="500">
<table width="100%">
<tr><td colspan="3" class="tbg">公安部身份认证查询</td></tr>
<tr><td align="center" width="130">姓名</td><td width="220"><input type="text" style="width:80%" value="<?=$name;?>" /></td><td rowspan="2" valign="middle" align="center"><button style="padding: 5px 10px;">查询</button></td></tr>
<tr><td align="center">18位身份证号</td><td><input type="text" style="width:80%" value="<?=$idno;?>" /></td></tr>
<tr><td colspan="3" class="tbg" style="font-size:1.2em;">公安部身份验证查询结果</td></tr>
<tr><td align="center">姓名</td><td colspan="2"><?=$name;?></td></tr>
<tr><td align="center">身份证号</td><td colspan="2"><?=$idno;?></td></tr>
<tr><td align="center">公安部返回结果</td><td colspan="2"><b>认证成功</b></td></tr>
<tr><td colspan="3" height="40" class="tbg" align="center">上海加数信息科技有限公司版权所有</td></tr>
</table>
</body>
</html>
