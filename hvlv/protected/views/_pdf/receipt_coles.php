<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
$high = 100 + sizeof($r->eitems['g']) * 10;
?>
<meta name="wkhtmltopdf" content="--dpi 100 --page-width 56 --page-height <?=$high;?> -T 2 -R 2 -B 2 -L 2 -O Portrait" win-only="--disable-smart-shrinking" />
<meta name="wkhtmltoimage" content="--disable-smart-width --width 400 --quality 80" />
<title>Receipt Coles</title>
<style type="text/css">
*{ margin: 0; padding: 0; letter-spacing: normal !important; }
body{ font-family: 'DejaVu Sans Mono', sans-serif; font-size: 20px; text-rendering: optimize-speed; width: 360px; padding: 20px; }
table td{ padding: 5px; }
hr { border: none; border-top: 1px solid #000; margin: 5px 0;}
</style>
</head>
<body width="400">
<?php
$ts = strtotime($d['date']);
$date = date('d-M-Y', $ts);
$time = date('H:i', $ts);
$strs = [496 => ['ALEXANDRIA', '02 8974 5200'],
617 => ['LAVINGTON', '03 6025 4877'],
675 => ['DENILIQUIN', '03 5881 8001'],
693 => ['ALBURY', '02 6041 5377'],
702 => ['STANHOPE GARDENS', '02 8883 1366'],
703 => ['COOMA', '02 6455 3000'],
704 => ['WAGGA', '02 6921 5532'],
706 => ['RHODES', '02 9743 3066'],
709 => ['WINMALEE', '02 4754 3811'],
710 => ['WORLD SQUARE ', '02 9283 1589'],
711 => ['PICTON', '02 4677 0655'],
713 => ['HARRINGTON PARK', ''],
716 => ['FIGTREE', '02 4229 9288'],
717 => ['TURRAMURRA', '02 9449 2582'],
719 => ['SMITHFIELD', '02 9616 0800'],
720 => ['ROSELANDS', '02 9759 0960'],
723 => ['LIVERPOOL', '02 9734 7050'],
724 => ['LANE COVE', '02 9427 0199'],
725 => ['GLENFIELD', '02 6971 3055'],
726 => ['MT DRUITT', '02 9675 2688'],
728 => ['PYRMONT', '02 8572 6450'],
737 => ['MAROUBRA JUNCTION', '02 9344 6066'],
739 => ['BEGA', '02 6492 2233'],
744 => ['LINDFIELD', '02 9416 7496'],
745 => ['MANLY VALE', '02 9949 1600'],
746 => ['WARATAH', '02 4967 1577'],
749 => ['FAIRFIELD', '02 9726 9577'],
750 => ['EARLWOOD', '02 9558 1374'],
752 => ['TUGGERAH', '02 4351 6863'],
754 => ['KINGS CROSS', '02 9358 6247'],
756 => ['WINDSOR', '02 4577 2354'],
757 => ['TUMUT', '02 6947 1543'],
760 => ['ASQUITH', '02 9476 3422'],
761 => ['WEST PENNANT HILLS', '02 9481 9988'],
762 => ['RAMSGATE', '02 9529 5911'],
766 => ['MIRANDA WESTFIELD', '02 9540 6200'],
768 => ['EPPING', '02 9869 2844'],
770 => ['ROSE BAY', ''],
772 => ['SYLVANIA', '02 9522 5200'],
774 => ['NORTH ROCKS', '02 9872 2333'],
775 => ['MACQUARIE FIELDS', '02 9605 5087'],
778 => ['DAPTO', '02 4261 4111'],
780 => ['NOWRA', '02 4421 3554'],
781 => ['TOUKLEY', '02 4396 4002'],
782 => ['HURSTVILLE', '02 9570 6622'],
783 => ['KAREELA', '02 9528 6639'],
784 => ['DEE WHY', '02 9981 3999'],
785 => ['CAMDEN', '02 4655 7946'],
786 => ['WYOMING', '02 4328 4313'],
788 => ['DUBBO', '02 6882 6250'],
790 => ['MIRANDA', '02 9525 9744'],
791 => ['ASHFIELD', '02 9799 5300'],
792 => ['RICHMOND', '02 4578 3111'],
793 => ['ENGADINE', '02 9520 5344'],
795 => ['CHATSWOOD', '02 9975 3001'],
796 => ['KINGS LANGLEY', '02 9674 3111'],
797 => ['WARRIEWOOD', '02 9913 1400'],
798 => ['THE ENTRANCE', '02 4332 7454'],
800 => ['RANDWICK', '02 9398 4013'],
801 => ['LITHGOW', '02 6352 1966'],
802 => ['GOULBURN', '02 4821 7260'],
804 => ['BOWRAL', '02 4861 3477'],
805 => ['SURRY HILLS', '02 9698 5005'],
807 => ['SWANSEA', '02 4971 1655'],
809 => ['BONDI ', '02 9389 5187'],
811 => ['BATEMANS BAY', '02 4472 1913'],
813 => ['NEWCASTLE JUNCTION', '02 4969 3723'],
814 => ['NEWPORT', ''],
815 => ['MANLY CORSO', '02 9932 5500'],
817 => ['ST MARYS', '02 9673 4111'],
819 => ['GOROKAN', '02 4392 7966'],
820 => ['QUEANBEYAN', '02 6299 2469'],
821 => ['WEST GOSFORD', '02 4324 5622'],
822 => ['WARRAWONG', '02 4276 1355'],
823 => ['BELMONT', '02 4945 0355'],
824 => ['WALLSEND', '02 4951 8344'],
825 => ['CASTLE HILL', '02 9680 2856'],
827 => ['DOONSIDE', '02 9679 9099'],
829 => ['NEUTRAL BAY', '02 9908 3099'],
831 => ['BRIGHTON LE SANDS', ''],
832 => ['GLADESVILLE', '02 9816 5695'],
833 => ['KOTARA', '02 4952 7066'],
834 => ['HINCHINBROOK', '02 9826 8055'],
835 => ['ORANGE', '2 6061 2666'],
836 => ['BATHURST', '02 6032 9566'],
838 => ['GRIFFITH', '02 6964 5455'],
839 => ['BROADWAY', '02 9281 0844'],
840 => ['WYNYARD', '02 9299 4769'],
841 => ['CARINGBAH', '02 9540 9352'],
842 => ['WATTLE GROVE', '02 9731 1044'],
843 => ['WINSTON HILLS', ''],
844 => ['WINDSOR', '02 4577 1000'],
845 => ['ULLADULLA', '02 4455 1722'],
846 => ['LEICHARDT', '02 9572 6466'],
847 => ['ILLAWONG', '02 9543 6766'],
849 => ['BIRKENHEAD', ''],
850 => ['TORONTO', '02 4959 8850'],
851 => ['NORWEST', '02 9659 6467'],
852 => ['CHATSWOOD CHASE', '02 9413 4549'],
853 => ['BONDI', '02 9369 3714'],
854 => ['FORESTVILLE', '02 9975 3255'],
856 => ['ST LEONARDS', '02 9966 4233'],
857 => ['GEORGE STREET', '02 9221 4770'],
860 => ['HORNSBY', '02 9482 1422'],
861 => ['BURWOOD', ''],
862 => ['BLACKTOWN', '02 9676 3525'],
864 => ['CESSNOCK', '02 4991 6172'],
865 => ['CONCORD', '02 9844 6202'],
869 => ['PENINSULA MANLY', '02-9976 6829'],
872 => ['CARLINGFORD', ''],
874 => ['CAMBRIDGE GARDENS', '02 4729 3600'],
879 => ['EDGEWORTH', '02 4953 0511'],
880 => ['WARNERS BAY', '02-4947 3800'],
882 => ['SAN REMO', '02 4390 6100'],
883 => ['KELLYVILLE', '02 8814 6900'],
884 => ['MONAVALE', '02-9997 3877'],
885 => ['ERINA', '02 4365 6025'],
886 => ['Balgowlah', '02 9934 9800'],
887 => ['PARRAMATTA ENTRADA', '02 9933 0600'],
890 => ['CORRIMAL', '02-4283 7622'],
893 => ['GLENDALE', '02 4954 6922'],
896 => ['PARRAMATTA', '02 9635 4179'],
897 => ['WARRINGAH', '02-9907 3303'],
898 => ['GREEN HILLS', '02 4933 4766'],
899 => ['EDGECLIFF', '02-9328 7978'],
901 => ['FORSTER', '02 6555 7266'],
903 => ['PENRITH', '02 4722 2100'],
904 => ['BATEAU BAY', '02 4334 5388'],
905 => ['WOY WOY', '02 4344 6989'],
906 => ['CAMPBELLTOWN', '02 4625 9622'],
908 => ['CASUALA', '02 9821 4688'],
909 => ['PAGEWOOD', '02 9314 0022'],
920 => ['LIGHTHOUSE BEACH', ''],
921 => ['KATOOMBA', '02 4780 8500'],
923 => ['OATLEY WEST', '02 8558 9500'],
924 => ['ROUSE HILL', ''],
925 => ['WYONG', ''],
927 => ['FAIRY MEADOW', '02 4223 9300'],
929 => ['BEROWA HEIGHTS', '02 9456 2513'],
936 => ['EDENSOR PARK', '02 8786 0500'],
939 => ['SCONE', ''],
940 => ['FLETCHER', '02 4941 6300'],
942 => ['HURSTVILLE STATION', '02 8558 9700'],
944 => ['DEE WHY GRAND', '02 9919 0100'],
945 => ['SINGLETON', '02 6575 4300'],
946 => ['RUTHERFORD', ''],
950 => ['MORRISET', '02 4973 7100'],
951 => ['BAULKHAM HILLS', '02 8852 9700'],
953 => ['CHIPPING NORTON', ''],
965 => ['WESTMEAD', '02 8837 7700'],
967 => ['TAREE', '02 6539 0200'],
968 => ['WEST RYDE', '02 9804 4400'],
970 => ['ROPES CROSSING', '02 9421 4900'],
987 => ['WATERLOO', '02 9549 3700'],
989 => ['TANILBA BAY', '02 4980 5300'],
992 => ['WADALBA', '02 4392 0019'],
993 => ['MARKET TOWN', '02 4926 4494'],
995 => ['CHARLESTOWN', ''],
999 => ['KURRI KURRI', '02 4936 5100'],
3478 => ['ROSELANDS', '02 9759 2855'],
4177 => ['INGLEBURN', '02 8797 9600'],
4178 => ['JEWELLSTOWN', '02 4948 5744'],
4387 => ['BERKELEY', '02 4222 1000'],
4401 => ['BALLINA', '02 6686 9377'],
4444 => ['INVERELL', '02 6722 3811'],
4456 => ['GRAFTON CENTRAL', '02 6641 8100'],
4459 => ['TWEED CITY', '07 5506 4000'],
4460 => ['CASUARINA', '02 8398 5900'],
4473 => ['MOREE', '02 6752 3877'],
4474 => ['TWEED HEADS', '7 5536 3977'],
4477 => ['LISMORE', '02 6621 7421'],
4485 => ['GRAFTON', '02 6642 5131'],
4505 => ['MURWILLUMBAH', '02 6672 4213'],
4522 => ['GUNNEDAH', '02 6741 7600'],
4535 => ['GOONELLABAH', '02 6625 0023'],
4551 => ['YAMBA', '02 6603 0500'],
4577 => ['BANORA POINT', '07 5506 3900'],
4788 => ['FIVE DOCK', '02 8752 6200'],
4789 => ['SUTHERLAND', '02 9521 9400'],
4812 => ['CLEMTON PARK', '02 8398 3400'],
4813 => ['MACARTHUR SQUARE', '02 4629 2100'],
4960 => ['BROKEN HILL', '08 8080 0100'],
5587 => ['NORTH SYDNEY', '02 8912 3400'],
5589 => ['BANORA POINT', '07 5523 4975'],
5590 => ['GLENMORE PARK', '02 4749 0800'],
5597 => ['GLEN INNES', '02 6732 2067'],
5598 => ['OLD BAR', '02 6557 3100'],
5678 => ['SOUTH WEST ROCKS', '02 6566 6645'],
5679 => ['Coffs Harbour', '03 8544 6824'],
5680 => ['KEMPSEY', '02 6560 1400'],
5681 => ['MOONEE BEACH', '02 6656 4788'],
5682 => ['TOORMINA', '02 6658 1444'],
5683 => ['LIGHTHOUSE BEACH', '02 6582 4951'],
5684 => ['LAKE INNES', '02 6581 2333'],
5685 => ['LAURIETON', '02 6559 8500'],
5686 => ['PORT MACQUARIE', '02 6588 0700'],
5689 => ['GREEN POINT', '02 4365 0722'],
5690 => ['THORNTON', '02 4966 5511'],
5691 => ['MOSS VALE', '02 4869 5417'],
5692 => ['TEA GARDENS', '02 4997 2603'],
5693 => ['SOUTH GRAFTON', '02 6642 1688'],
5694 => ['CASINO', '02 6662 5800'],
5695 => ['TENTERFIELD', '02 6736 4922'],
5696 => ['KILLARNEY VALE', '02 4332 7492'],
5697 => ['EDEN', '02 6496 4800'],
5698 => ['THIRROUL', '02 4267 1587'],
5714 => ['WELLINGTON', '02 6845 4400'],
5720 => ['MOUNT HUTTON', '&nbsp;'],
5726 => ['CHITTAWAY POINT', '02 4388 4744'],
5730 => ['KINCUMBER', '02 4369 5566'],
5733 => ['UMINA BEACH', '02 4341 0416'],
5736 => ['HELENSBURGH', '02 4294 9567'],
5749 => ['COWRA', '02 6342 3283'],
5753 => ['MUDGEE', '02 6072 7155'],
5755 => ['NARROMINE', '02 6889 4703'],
5757 => ['PARKES', '02 6863 6200'],
5759 => ['BUDGEWOI', '02 4390 6000'],
5769 => ['MOUNT ANNAN', '02 4636 9300'],
5770 => ['FAIRFIELD WEST', '02 9616 5100'],
5771 => ['BONNELLS BAY', '02 4973 7800'],
5773 => ['CHATSWOOD CHASE', '02 8448 5200'],
5776 => ['STH MUSWELLBROOK', '02 6542 3000'],
5777 => ['NORTH RICHMOND', '02 4571 3091'],
5778 => ['WINGHAM', '02 6591 1300'],
5791 => ['MACQUARIE', '02 8870 2300'],
5792 => ['Greenacre', '(02) 8709 0400'],
5793 => ['MERRYLANDS', '02 8868 1200'],
5796 => ['TAMWORTH SOUTH', '02 6760 1400'],
5797 => ['SHELLHARBOUR', '02 4295 8300'],
5800 => ['NARELLAN', '02 4645 7100'],
5801 => ['TOP RYDE', '02 8878 8100'],
5806 => ['WILLOWDALE', '02 8763 8100'],
7554 => ['LISAROW', '02 4328 0000'],
7555 => ['MEDOWIE', '02 4982 9600'],
7558 => ['REVESBY', '02 8723 6400'],
7565 => ['SALAMANDER BAY', '02 4919 2500'],
7569 => ['EAST VILLAGE', '02 8344 6200'],
7579 => ['NARRANDERA', '02 6959 2388'],
7587 => ['WOLLONGONG', '02 4220 6700'],
7591 => ['VINCENTIA', '02 4441 7396'],
7596 => ['WETHERILL PARK', ''],
8760 => ['ARMIDALE', '02 6771 4777'],
8761 => ['TAMWORTH', '02 6766 4856'],
8769 => ['NARRABRI', '02 6799 2500'],
8770 => ['TAMWORTH NORTH', '02 6766 3488'],
];
$mgrs = ['David', 'John', 'Nick', 'Sally', 'Lisa', 'Peter', 'Emily'];
$sbys = ['Assisted Checkout', 'Dave', 'Joe', 'Mike', 'Milly', 'Elise', 'Zoe', 'Pete', 'Sam'];
$str = array_rand($strs);
?>
<p align="center" style="font-size:0.8em">Coles Supermarkets Australia Pty Ltd<br />
Tax Invoice ABN: 54 004 189 708<br />
<img src="https://os.toplogistics.com.au/images/coles_logo.png" width="200" style="margin:15px 0" />
</p>
<hr />
<table width="100%" style="font-size: 0.8em">
<tr><td colspan="2">Store: <?=$str.' - '.$strs[$str][0];?></td></tr>
<tr><td colspan="2">Store Manager: <?=$mgrs[array_rand($mgrs)];?></td></tr>
<tr><td colspan="2">Phone: <?=$strs[$str][1];?></td></tr>
<tr><td colspan="2">Served By: <?=$sbys[array_rand($sbys)];?></td></tr>
<tr><td colspan="2" align="right">Receipt: <?=$d['no'];?></td></tr>
<tr><td>Date: <?=$date;?></td><td align="right" width="40%">Time: <?=$time;?></td></tr>
</table>
<hr />
<table width="100%">
<tr><td>&nbsp;</td><td align="center" width="40"><b>$</b></td></tr>
<?php
$ti = 0;
$tot = 0;
$poc_map = ['CNCA2' => 'CNCA2', 'CNJM2' => 'CNJMN', 'CNCS2' => 'CNCSX'];
if(isset($poc_map[$r->consol->poc])) $r->consol->poc = $poc_map[$r->consol->poc];

foreach($r->eitems['g'] as $i => $g){
	if(!empty($r->mdata['altItems']['v'][$i])){
		$v = $r->mdata['altItems']['v'][$i];
	}else{
		$pd = empty($r->eitems['pid'][$i])? false : ExProdb::model()->findByPk($r->eitems['pid'][$i]);
		$v = empty($pd)? floatval($r->eitems['t'][$i]) : (empty($pd->mdata['price_'.$r->consol->poc])? $pd->price : $pd->mdata['price_'.$r->consol->poc]);
	}
	$v = $v / $r->consol->exrate;

	$st = sprintf('%.2f', floatval($r->eitems['q'][$i]) * floatval($v));
	echo '<tr><td>', $r->receiptItemName($i), ' x ', $r->eitems['q'][$i], '</td><td align="right" valign="top">',$st,'</td></tr>';
	$ti += floatval($r->eitems['q'][$i]);
	$tot += $st;
}
?>
<tr><td><b>Total for <?=$ti;?> items</b></td><td align="right"><b>$<?=$tot;?></b></td></tr>
<tr><td>EFT</td><td align="right">$<?=$tot;?></td></tr>
<tr><td><b>GST INCLUDED IN TOTAL</b></td><td align="right"><b>$0.00</b></td></tr>
</table>
<br />
<table width="100%" style="font-size: 0.8em">
<tr><td align="center">Coles</td><td align="right" width="40%">NSW AU</td></tr>
<tr><td><?=$date.' '.$time;?></td><td align="right">245<?=rand(10000,99999);?> N<?=rand(100,999);?>B<?=rand(0,9);?></td></tr>
<tr><td>CREDIT ACCOUNT</td><td align="right">MASTERCARD</td></tr>
<tr><td>PURCHASE</td><td align="right">AUD$ <?=$tot;?></td></tr>
<tr><td>RRN 00112<?=rand(1000000,9999999);?></td><td align="right">(00) APPROVED</td></tr>
<tr><td colspan="2"><?=$tot<100? 'NO PIN OR SIGNATURE REQUIRED' : 'AUTH 0'.rand(10000,99999);?></td></tr>
</table>
<br />
<br />
<p align="center" style="font-size: 0.8em">Please retain receipt for refund<br />
or exchange purposes<br />
% = Taxable items</p>
</body>
</html>
