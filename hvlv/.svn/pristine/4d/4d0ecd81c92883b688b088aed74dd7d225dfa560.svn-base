<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
$high = 100 + sizeof($r->eitems['g']) * 10;
?>
<meta name="wkhtmltopdf" content="--dpi 100 --page-width 56 --page-height <?=$high;?> -T 2 -R 2 -B 2 -L 2 -O Portrait" win-only="--disable-smart-shrinking" />
<meta name="wkhtmltoimage" content="--disable-smart-width --width 400 --quality 80" />
<title>Receipt Woolworth</title>
<style type="text/css">
*{ margin: 0; padding: 0; letter-spacing: normal !important; }
body{ font-family: 'DejaVu Sans Mono', sans-serif; font-size: 14px; text-rendering: optimize-speed; width: 360px; padding: 20px; }
table td{ padding: 5px; }
hr { border: none; border-top: 1px dashed #000; margin: 5px 0;}
</style>
</head>
<body width="400">
<?php
$date = date('H:i d/m/Y', strtotime($d['date']));
$strs = [1136 => ['Ulladulla', '116 Princes Hwy', '(02) 4454 6900'],
1173 => ['Batemans Bay ', 'Cnr Vesper And North Sts', '(02) 4478 4004'],
1266 => ['Merimbula', '72-76 Main St', '(02) 6497 6000'],
1267 => ['Narooma', 'Cnr Wilcocks Ave And Princes Hwy', '(02) 4473 3000'],
1372 => ['Bega', 'Cnr of Auckland and Carp St', '(02) 6494 8502'],
1389 => ['Moruya', '61-63 Queen St', '(02) 4474 8900'],
1431 => ['Bermagui', '1-9 Young St', '(02) 6497 8900'],
1674 => ['Tura Beach ', 'Cnr Tura Beach Dr & Saphire Coast Dr', '(02) 6497 6003'],
1102 => ['Vincentia', 'CNR Naval College and The Wool Rd ', '(02) 4428 2500'],
1114 => ['Mittagong West', 'Cnr Roscoe St And Old Hume Hwy', '(02) 4868 7204'],
1119 => ['Nowra', '9-13 Kinghorne St', '(02) 4448 2500'],
1162 => ['Bowral', 'Cnr Bong Bong Rd & Banyette St', '(02) 4868 7207'],
1163 => ['Kiama', 'Kiama View Shop. Ctr, Terralong St', '(02) 4232 6400'],
1197 => ['Shellharbour', 'New Lake Entrance Rd', '(02) 4276 6018'],
1436 => ['Culburra Beach ', '8-22 Weston St', '(02) 4448 2506'],
1470 => ['Shellharbour ', 'Shellharbour Stocklands Centre, Lake Entrance Rd ', '(02) 4276 6035'],
1648 => ['Nowra Stocklands', 'Shop 2, Stockland Nowra Shop. Ctr, 32-60 East St', '(02) 4448 2503'],
1030 => ['Wollongong', '63 Burelli St', '(02) 4276 6006'],
1065 => ['Corrimal', 'Stockland Corrimal Shop. Ctr, 193 Princes Hwy & Cnr Railway St', '(02) 4276 6009'],
1069 => ['Warilla', 'Shellharbour Rd', '(02) 4276 6012'],
1190 => ['Dapto', 'Dapto Mall, Cnr Princes Hwy And Moombara St', '(02) 4276 6015'],
1293 => ['Fairy Meadow', '66 Princes Hwy', '(02) 4276 6021'],
1302 => ['Albion Park', 'Cnr Terry & Russell St', '(02) 4232 6403'],
1387 => ['Unanderra', 'Cnr Victoria St And Princes HWay', '(02) 4276 6024'],
1392 => ['Bulli', 'Cnr Princess Hwy & Molloy St', '(02) 4276 6040'],
1632 => ['Figtree', 'Cnr Princes Hwy And The Ave', '(02) 4276 6030'],
1107 => ['Campbelltown', 'Macarthur Sq Shop. Ctr, Cnr Gilchrist Dr And Kellicar Rd', '(02) 4646 9306'],
1110 => ['Rosemeadow', 'Cnr Copperfield And Thomas Rose Drs', '(02) 4646 9309'],
1135 => ['Narellan', 'Narellan Town Centre, Camden VAlly Way', '(02) 4646 9312'],
1185 => ['Camden', 'Cnr Oxley & Argyle St', '(02) 4651 4400'],
1224 => ['Campbelltown Mall', '271  Queen St', '(02) 4646 9315'],
1287 => ['Tahmoor', 'Cnr Rememberance Dr And Thirlmere Way', '(02) 4677 6200'],
1351 => ['Campbelltown Market Fair ', 'Cnr Tindall St And Kellicar And Narellan Rds', '(02) 4646 9318'],
1388 => ['Mt Annan', 'Main St', '(02) 4646 9321'],
1603 => ['Spring Farm', '254 Richardson Rd', '(02) 4651 4403'],
1091 => ['Eagle Vale', 'Eagle Vale MarketPl, Cnr Gould And Feldspar Sts', '(02) 8785 3612'],
1138 => ['Casula', '607 Hume Hwy', '(02) 8785 3648'],
1144 => ['Liverpool', 'Westfield Shop. Ctr, Cnr Campbell & Northumberland St', '(02) 8785 3618'],
1204 => ['Ingleburn', 'Cnr Nardoo & Norfolk St', '(02) 8785 3624'],
1216 => ['Miller', 'Cartwright Ave', '(02) 8785 3627'],
1317 => ['Macquarie Fields', 'Shop 50, Glenquarie Shop. Ctr', '(02) 8785 3633'],
1479 => ['Minto', '10 Brookfield Rd', '(02) 8785 3670'],
1758 => ['Carnes Hill', 'Cnr Cowpasture And Kurajong Rds', '(02) 8785 3645'],
1997 => ['Oran Park', 'Oran Park Town Centre', '(02) 4646 9324'],
1089 => ['Hawker', 'Springvale Dr', '(02) 6132 9302'],
1139 => ['Belconnen', 'Benjamin Way', '(02) 6132 9825'],
1203 => ['Goulburn', 'Cnr Sloane & Verner St', '(02) 4824 5000'],
1279 => ['Gungahlin', '31 Hibberson St', '(02) 6132 9846'],
1419 => ['Canberra Airport', 'Majura Park Centre 28 Spitfire Ave', '(02) 6132 9860'],
1457 => ['Bonner', '61 Mabo Bvd', '(02) 6132 9863'],
1661 => ['Franklin', 'Cnr Flemington Rd and Nullarbour Ave', '(02) 6132 9305'],
1080 => ['Queanbeyan', 'Cnr Crawford & Antill St', '(02) 6132 9813'],
1126 => ['Cooma', '12-20 Vale St', '(02) 6455 5300'],
1134 => ['Calwell', 'Calwell Shop. Ctr, Johnson Dr', '(02) 6132 9819'],
1198 => ['Tuggeranong', 'Tuggeranong Hyperdome, Cnr Anketell & Reed St', '(02) 6132 9834'],
1206 => ['Erindale', 'Comrie St', '(02) 6132 9837'],
1275 => ['Conder', 'Cnr Tharwa Dr And Box Hill Ave', '(02) 6132 9843'],
1401 => ['Jindabyne', 'Shop 1, Nuggets Crossing', '(02) 6448 8000'],
1407 => ['Jerrabomberra', '17 Limestone Dr', '(02) 6132 9855'],
1073 => ['Dickson', '1 Dickson Pl', '(02) 6132 9810'],
1118 => ['Woden', 'Cnr Hindmarsh And Melrose Drs', '(02) 6132 9816'],
1137 => ['Mawson', 'Mawson Pl', '(02) 6132 9822'],
1161 => ['Weston Creek', 'Cnr Brierly And Mahoney Cres', '(02) 6132 9828'],
1194 => ['Kambah', 'Marconi Cres', '(02) 6132 9831'],
1261 => ['Kippax', 'Kippax Fair Hardwick Cres', '(02) 6132 9840'],
1343 => ['Dunlop', 'Cnr ShoobRdge Cct & Lance Hill Ave', '(02) 6132 9849'],
1358 => ['Charnwood ', 'Charnwood Pl', '(02) 6132 9852'],
1152 => ['Griffith', 'Cnr Banna Ave And Bonegilla Rd', '(02) 6969 6002'],
1156 => ['Griffith North', 'Burrell Pl', '(02) 6969 6005'],
1209 => ['Wagga Wagga', '87 Baylis St', '(02) 6932 5102'],
1254 => ['Temora', '104-110 Hoskins St', '(02) 6977 5050'],
1273 => ['Wagga Wagga North', '30 Gurwood St', '(02) 6932 5105'],
1280 => ['Kooringal', 'Kooringal Mall Shop 41- 51 Lake Albert Rd', '(02) 6932 5108'],
1308 => ['Leeton', 'Cnr Palm And Wamoon Ave', '(02) 6981 3500'],
1133 => ['Young', 'Boorowa St', '(02) 6381 3102'],
1181 => ['Parkes', '299 Clarinda St', '(02) 6862 7202'],
1196 => ['Cootamundra', 'Cnr Parker & Bourke St', '(02) 6942 5000'],
1268 => ['Cowra', '3-9 Railway Lane', '(02) 6349 8120'],
1307 => ['Forbes', '134 Rankin St', '(02) 6850 8400'],
1325 => ['Yass', 'Cnr Comur & Polding St', '(02) 6118 7000'],
1346 => ['Tumut', 'Cnr Herlihy & Fitzroy St', '(02) 6941 2100'],
1922 => ['Gundagai ', '246-250 Sheridan St', '(02) 6981 5000'],
1086 => ['Mudgee', '88 Mortimer St', '(02) 6370 5502'],
1120 => ['Orange', '197-203 Anson St', '(02) 6363 5102'],
1121 => ['Dubbo', 'Orana Mall MarketPl, Cnr Mitchell Hwy And Wheelers Lane', '(02) 6881 7604'],
1150 => ['Riverdale', 'Macquarie St', '(02) 6881 7607'],
1167 => ['Bathurst', 'William St', '(02) 6330 8004'],
1304 => ['Delroy Park', 'Cnr Minore And Baird Dr, Delroy Park', '(02) 6881 7610'],
1310 => ['Bathurst City Centre', '210 Howick St', '(02) 6330 8007'],
1313 => ['North Orange', 'Telopea Way, Warratah Park,', '(02) 6363 5105'],
1446 => ['Wellington', '81 Arthur St', '(02) 6845 5100'],
1087 => ['Penrith ', '569-589 High St', '(02) 4723 2504'],
1154 => ['Emu Plains', 'Lennox Shop. Ctr, Cnr Great Western Hwy And Lawson St', '(02) 4723 2507'],
1242 => ['Lithgow', '224 Mort St', '(02) 6351 7900'],
1328 => ['Penrith South', 'Southlands Shop. Ctr 2 Birmingham Rd', '(02) 4723 2510'],
1350 => ['Glenmore Park', 'Cnr Town Tce And Glenmore Parkway', '(02) 4723 2513'],
1556 => ['Cranebrook', '80 - 98 Borrowdale Way', '(02) 4723 2519'],
1681 => ['Leura', '152-160 Leura Mall', '(02) 4345 4500'],
1787 => ['Katoomba', 'Cnr Parke & Waratah St,', '(02) 4345 4503'],
1140 => ['St Clair', 'St Clair Shop. Ctr, Cnr Bennett Rd And Endeavour St', '(02) 9677 6423'],
1143 => ['Mt Druitt', 'Westfields, 49 Luxford Rd', '(02) 9677 6429'],
1189 => ['Plumpton', 'Plumpton Market Pl, Cnr Jersey And Hyatts Rds', '(02) 9677 6435'],
1210 => ['Richmond', 'Cnr Lennox & Paget St', '(02) 4588 9004'],
1375 => ['Windsor', 'Windsor Town Shop. Ctr, Kable St', '(02) 4588 9007'],
1609 => ['Greenway Village', 'Shop M1 799 Richmond Rd', '(02) 9677 6471'],
1625 => ['Emerton', 'Emerton Village Shop. Ctr, Cnr Jersey Rd And Bunting St', '(02) 9677 6453'],
1645 => ['St Marys', 'St Marys Village Shop. Ctr, Shop 302, Charles Hackett Dr', '(02) 9677 6456'],
1956 => ['Jordan Springs', ' Cnr Lakeside Pde & Jordan Springs Bvd', '(02) 9450 6724'],
1106 => ['Parramatta', 'Westfield Shop. Ctr Campbell St', '(02) 8633 2907'],
1123 => ['Rosehill', '28-30 Oak St', '(02) 8633 2913'],
1251 => ['Berala', '15-16 Woodburn Rd', '(02) 8565 9286'],
1258 => ['Merrylands', '209 Pitt St', '(02) 8633 2944'],
1315 => ['Newington', '1 Ave Of Americas', '(02) 8565 9221'],
1366 => ['Ermington', 'Betty Cuthbert Ave', '(02) 8633 2922'],
1400 => ['Auburn', 'Cnr Queen And Park St', '(02) 8565 9230'],
1759 => ['Granville', 'Cnr Louis &  Blaxcell St', '(02) 8633 2937'],
1958 => ['Greystanes', '655 Merrylands Rd', '(02) 8633 2947'],
1109 => ['Cabramatta', 'Cnr Railway Pde And Hughes St', '(02) 8709 4306'],
1145 => ['Fairfield', 'Neeta City Shop. Ctr, Cnr Nelson & Smart St', '(02) 8709 4312'],
1184 => ['Bonnyrigg', 'Bonnyrigg Ave', '(02) 8785 3621'],
1226 => ['Fairfield Heights', '186 The Bvd', '(02) 8709 4329'],
1306 => ['Cecil Hills', 'Cnr Sandringham Dr And Fedore Rd', '(02) 8785 3630'],
1368 => ['Pemulwuy', 'Cnr Old Prospect Rd  & Greystanes Rd', '(02) 8633 2925'],
1386 => ['Wetherill Park', 'Cnr Restwell Rd and Polding St', '(02) 8785 3636'],
1646 => ['Green Valley', 'Wilson Rd', '(02) 8785 3642'],
1108 => ['Marayong', 'Quakers Ct, Cnr Falmouth And Quaker Rds', '(02) 9677 6420'],
1175 => ['Seven Hills', 'The Hills Centre, Cnr Federal Rd And Prospect Hwy', '(02) 9677 6432'],
1183 => ['Wentworthville', 'Cnr Great Western Hwy And Lane St', '(02) 8633 2916'],
1288 => ['Blacktown', 'Westpoint Shop. Ctr, Cnr Alpha And Patrick Sts', '(02) 9677 6438'],
1323 => ['Prospect', 'Cnr Flushcombe Rd And Myrtle St', '(02) 9677 6441'],
1338 => ['Kings Langley', 'Cnr James Cook Dr And Ravenhill St', '(02) 9677 6444'],
1348 => ['Glenwood', 'Glenwood Park Dr', '(02) 9677 6450'],
1413 => ['Toongabbie', 'Cnr Junia Ave And Cornelia Rd', '(02) 8633 2934'],
1004 => ['Punchbowl', '1-9 the Bvd', '(02) 8565 9306'],
1008 => ['Chester Hill', 'Chester Sq Shop. Ctr, 1-13 Leicester St', '(02) 8709 4303'],
1097 => ['Riverwood', '247 Belmore Rd', '(02) 8522 7709'],
1104 => ['Chullora', '355 Waterloo Rd', '(02) 9308 7397'],
1127 => ['Moorebank', 'Moorebank Shop. Vge, 136 Stockton Ave', '(02) 8785 3615'],
1131 => ['Bankstown', 'Centro Shop. Ctr, Lady Cutler Ave', '(02) 8709 4309'],
1147 => ['Bass Hill', '753 Hume Hwy', '(02) 8709 4315'],
1402 => ['Revesby', 'Cnr Marco And Polo Aves', '(02) 8709 4324'],
1598 => ['Lidcombe', '92 Parramatta Rd ', '(02) 8565 9330'],
1092 => ['Menai', 'Allison Cres', '(02) 8522 7706'],
1269 => ['Engadine ', 'Cnr Princes Hwy And Waratah St', '(02) 9548 7100'],
1284 => ['Hurstville', 'Cnr Park & Cross Sts', '(02) 8565 9341'],
1615 => ['Sylvania', 'Southgate Shop. Ctr, Cnr Princes Hwy And Port Hacking Rd', '(02) 8522 7721'],
1767 => ['Caringbah', 'Cnr President Ave And High St', '(02) 8522 7724'],
1864 => ['Kingsgrove', 'Mashman Ave', '(02) 8565 9313'],
1932 => ['Lakemba', '2 - 26 Haldon St', '(02) 8565 9374'],
1934 => ['Mortdale', '84D Roberts Ave', '(02) 8565 9309'],
1942 => ['Miranda', 'Miranda Fair Shop. Ctr, 600 The Kingsway', '(02) 8522 7729'],
1128 => ['Wolli Creek', '78-96 Arncliffe St', '(02) 8565 9281'],
1231 => ['Hillsdale', 'South Point Shop. Ctr, 238-262 Bunnerong Rd', '(02) 8565 9209'],
1252 => ['Rockdale', 'Rockdale Plaza, Cnr Princes Hwy And Rockdale Plaza Dr', '(02) 8565 9260'],
1257 => ['Eastlakes', 'Shop 10, Bkk Shop. Ctr, Evans Ave', '(02) 8565 9215'],
1412 => ['Eastgardens ', 'Eastgardens Shop. Ctr, 152 Bunnerong Rd', '(02) 8565 9233'],
1442 => ['Mascot', '55 Church Ave', '(02) 8565 9333'],
1638 => ['Green Square Town Centre', '20 - 26 Ebsworth St', '(02) 8565 9365'],
1766 => ['Kogarah', 'Kogarah Town Centre, Railway Pde', '(02) 8565 9272'],
1770 => ['Matraville', 'Cnr Bunnerong Rd & Daunt Ave Matraville', '(02) 8565 9294'],
1050 => ['Alexandria', '10 Fountain St', '(02) 8565 9353'],
1248 => ['Town Hall', 'Cnr Park & George St', '(02) 8565 9275'],
1416 => ['Double Bay', 'Cnr Kiaora Lane and Kiaora Rd', '(02) 8736 7472'],
1474 => ['Redfern ', '261-265 Chalmers St', '(02) 8565 9278'],
1557 => ['Bondi Junction ', 'Westfield Shop. Ctr, 530 Oxford St', '(02) 8565 9239'],
1631 => ['Randwick', 'Shop 7 Randwick Village Shppng Cntr, Cnr Belmore Rd, Avoca And Short Sts', '(02) 8565 9248'],
1800 => ['Mascot DOS', 'Dedicated On Line Store Only', '(02) 8736 7464'],
1905 => ['Broadway ', '26-60 BRdway', '(02) 8736 7450'],
1034 => ['Canterbury', '2A Charles St', '(02) 8565 9347'],
1061 => ['Campsie', '68-72 Evaline St', '(02) 9308 7395'],
1149 => ['Marrickville', '463 Illawarra Rd', '(02) 8565 9200'],
1188 => ['Balmain', '276 Darling St', '(02) 8565 9203'],
1213 => ['Ashfield', 'Cnr Knox & Norton St', '(02) 8565 9206'],
1332 => ['Burwood', 'Shop 6, Level 3, Westfield Shop. Ctr, Burwood Rd', '(02) 8565 9224'],
1614 => ['Burwood Plaza', 'Burwood Plaza Shop. Ctr, 42-50 Railway Pde', '(02) 8565 9242'],
1624 => ['Strathfield', 'Shop 19, Strathfield Plaza, 11 The Bvd', '(02) 8565 9245'],
1647 => ['Leichhardt Marketplace', 'Leichhardt Market Pl, Cnr Marion & Flood St', '(02) 8565 9251'],
1649 => ['Marrickville Metro', 'Marrickville Shop. Ctr, 34 Victoria Rd', '(02) 8565 9254'],
1056 => ['Narrabeen', '12 Lagoon St', '(02) 9450 6706'],
1153 => ['Dee Why', 'Oaks Ave', '(02) 9308 7370'],
1262 => ['Glenrose', '56 - 58 Glen St', '(02) 8565 9344'],
1282 => ['Warringah Mall (Brookvale)', 'Pittwater Rd', '(02) 9308 7382'],
1573 => ['Balgowlah', '17 - 31 Roseberry St', '(02) 8565 9291'],
1621 => ['Warriewood', 'Warriewood Sq Cnr Jacksons And Boondale Rd', '(02) 9450 6718'],
1623 => ['Avalon', '74 Old Barenjoey Rd', '(02) 9973 8900'],
1771 => ['Mona Vale', '25-29 Park St', '(02) 9450 6721'],
1063 => ['Frenchs Forest', 'Forestway Shop. Ctr, Cnr Russell Ave And Forest Way', '(02) 9308 7355'],
1099 => ['St Ives', 'St Ives Shop. Vge,Cnr Mona Vale Rd And Memorial Ave', '(02) 9308 7361'],
1103 => ['Lane Cove', 'Cnr Longueville Rd And Austin St', '(02) 9308 7364'],
1113 => ['Neutral Bay Village', '1-7 Rangers Rd', '(02) 9308 7367'],
1157 => ['Neutral Bay', '43-51 Grosvenor St', '(02) 9308 7373'],
1160 => ['Gordon', '808 Pacific Hwy', '(02) 9308 7376'],
1199 => ['Northbridge', 'Cnr Sailors Bay Rd And Eastern VAlly Way', '(02) 9308 7379'],
1318 => ['Crows Nest', '10 Falcon St', '(02) 8565 9336'],
1122 => ['Carlingford', 'Carlingford Ct Pennant Hills Rd', '(02) 8633 2910'],
1129 => ['Macquarie Ryde', 'Macquarie Shop. Ctr, Cnr Waterloo And Herring Rds', '(02) 9308 7337'],
1200 => ['Eastwood ', 'Eastwood Centre, 160 Rowe St', '(02) 9308 7340'],
1249 => ['Thornleigh', '2 The Comenarra Parkway', '(02) 9450 6709'],
1294 => ['Hornsby', 'Westfield Shop. Ctr, Cnr Pacific Hwy And Edgeworth David Ave', '(02) 9450 6712'],
1339 => ['Top Ryde', 'Cnr Devlin St And Blaxland Rd', '(02) 9308 7343'],
1341 => ['West Ryde', '14 Anthony Rd', '(02) 9308 7346'],
1364 => ['Beecroft', 'Cnr of Hannah St and Beecroft Rd', '(02) 9450 6727'],
1607 => ['Cherrybrook Village', 'Shop 17, Cherrybrook Village, Shepherds Dr', '(02) 9450 6715'],
1761 => ['Marsfield', 'Cnr Epping And Balaclava Rds', '(02) 9308 7352'],
1090 => ['Rouse Hill', 'Rouse Hill Town Centre, Cnr White Hart Dr And Caddies Bvd', '(02) 9677 6417'],
1105 => ['Baulkham Hills', 'Cnr Old Northern And Olive Sts', '(02) 8633 2904'],
1115 => ['Glenorie', '936-938 Old Northern Rd', '(02) 9652 4025'],
1272 => ['The Ponds', 'Cnr Riverbank Dr & The Ponds Bvd', '(02) 9677 6462'],
1297 => ['Dural', 'Round Crn, 494-500 Old Northern Rd', '(02) 9652 4028'],
1347 => ['Kellyville', '88 Wrights Rd', '(02) 9677 6447'],
1422 => ['Norwest Circa Shopping Centre ', '1 Circa Bvde', '(02) 9677 6408'],
1561 => ['Kellyville North', 'Cnr Withers & Hezlett Rds', '(02) 9677 6468'],
1757 => ['Winston Hills', 'Winston Hills Mall, Caroline Chisholm Dr', '(02) 9677 6459'],
1941 => ['Schofields', 'Railway Tce and Pelican Rds', '(02) 9677 6491'],
1085 => ['Bateau Bay', 'Bateau Bay Sq, 12 Bay Village Rd', '(02) 4343 9704'],
1100 => ['Gosford', '40-46 William St', '(02) 4343 9707'],
1111 => ['Tuggerah', 'Cobbs Rd', '(02) 4356 4504'],
1159 => ['Woy Woy', 'Peninsula Plaza, 63 Blackwall Rd', '(02) 4343 9710'],
1192 => ['Erina', 'Karalta Rd', '(02) 4343 9713'],
1380 => ['Umina', 'Crn Of West And Trafalgar St', '(02) 4343 9716'],
1397 => ['Morisset ', 'Crn Dora, Doyalson And Yambo St', '(02) 4978 2405'],
1572 => ['Lake Munmorah', 'Tall Timbers Rd', '(02) 4356 4510'],
1574 => ['Lisarow', 'Parsons Rd', '(02) 4343 9721'],
1634 => ['Lake Haven', 'Lake Haven Shopping, Centre Lake Haven Dr', '(02) 4356 4507'],
1165 => ['Mt Hutton', 'Cnr Wilson Rd And Rosalind St', '(02) 4902 2714'],
1172 => ['Glendale', 'Cnr Main And Lake Rds', '(02) 4902 2717'],
1207 => ['Kotara', 'Cnr Park Ave And Northcott Dr', '(02) 4902 2720'],
1228 => ['Cardiff ', 'Cnr Macquarie And Main Rds', '(02) 4902 2723'],
1244 => ['Swansea', '18 Josephson St', '(02) 4978 2402'],
1316 => ['Charlestown Square ', 'Charlestown Sq Canberra St', '(02) 4902 2726'],
1326 => ['Belmont', 'Cnr Macquarie & Singleton St', '(02) 4902 2729'],
1344 => ['Toronto', 'Cnr Pemell St And Brighton Aves', '(02) 4902 2732'],
1415 => ['Newcastle West', '23 Steel St', '(02) 4902 2738'],
1101 => ['Mayfield', 'Cnr Maitland Rd And Valencia St', '(02) 4902 2708'],
1117 => ['Jesmond', 'Blue Gum Rd', '(02) 4902 2711'],
1171 => ['Raymond Terrace', '39-41 Port Stephens Cnr Glenelg St', '(02) 4983 7004'],
1174 => ['Salamander Bay', 'Bagnalls Beach Rd', '(02) 4919 5000'],
1245 => ['Raymond Terrace North', 'Cnr Port Stephens And Bourke Sts MarketPl', '(02) 4983 7007'],
1495 => ['Medowie', '39-47 Ferodale Rd', '(02) 4919 5019'],
1680 => ['Gloucester', '111 - 115 Church St', '(02) 6537 2200'],
1753 => ['Warabrook', '3 Angophora Dr', '(02) 4902 2741'],
1785 => ['Nelson Bay', 'Cnr Stockton St & Donald St ', '(02) 4919 5003'],
1112 => ['Cessnock', 'Cessnock Plaza, Cnr Keene & Cooper St', '(02) 4998 5002'],
1148 => ['Maitland', 'Pender Pl Shop. Ctr, Cnr Church & Elgin St', '(02) 4015 6304'],
1164 => ['Singleton', 'Gowrie St Mall Gowrie St', '(02) 6572 6002'],
1179 => ['Green Hills', 'Stockland Centre, 1 Molly Morgan Dr', '(02) 4015 6307'],
1208 => ['Rutherford', 'Cnr Alexandra Ave And Hillview St', '(02) 4015 6310'],
1236 => ['Muswellbrook', 'Cnr Brook & Sowerby St', '(02) 6541 7902'],
1410 => ['Aberglasslyn', 'Cnr Aberglasslyn and McKeachie Dr', '(02) 4015 6350'],
1168 => ['Gunnedah', '119 - 129 Conadilly St', '(02) 6748 3100'],
1191 => ['Coonabarabran', '35 Dalgarno St', '(02) 5881 6000'],
1195 => ['Tamworth', 'Cnr William And Denne St', '(02) 5776 5702'],
1305 => ['Narrabri North', '181-189 Maitland St', '(02) 6790 9000'],
1335 => ['Moree', '215 Balo St', '(02) 6751 3300'],
1345 => ['Scone', '35 Main St', '(02) 6521 5000'],
1685 => ['Tamworth East', '502 - 504 Peel St', '(02) 5776 5705'],
1170 => ['Kempsey', 'Cnr Forth And Smith Sts', '(02) 6561 3002'],
1177 => ['Forster', 'Cnr The Lakes Way And Breese Pde', '(02) 6539 8000'],
1178 => ['Port Macquarie', 'Cnr Bay And Park Sts', '(02) 5525 5202'],
1205 => ['Taree', 'Cnr Albert Stokes And Manning Sts', '(02) 5594 6202'],
1221 => ['Lakewood', '10 Botanic Dr', '(02) 6538 3100'],
1311 => ['Nambucca Heads', 'Cnr Fraser And Back Sts', '(02) 6598 4100'],
1321 => ['Lake Cathie', 'Lot 2 Ocean Dr', '(02) 5525 5205'],
1434 => ['Macksville', 'Cnr Pacific Hwy And Boundary St', '(02) 6598 4103'],
1961 => ['Tuncurry', 'Cnr Peel and Kent St ', '(02) 6539 8003'],
1124 => ['Park Beach Plaza (Coffs Harbour)', 'Cnr Pacific Hwy And Park Beach Rd', '(02) 6690 8705'],
1132 => ['Toormina', 'Centro Shop. Ctr, Toormina Rd', '(02) 6690 8707'],
1180 => ['Coffs Harbour', '7 Park Ave', '(02) 6690 8710'],
1253 => ['Armidale ', 'Cnr Jessie And Beardy Sts', '(02) 6771 8002'],
1259 => ['Glen Innes', 'Cnr Wentworth And Grey Sts', '(02) 6739 7000'],
1327 => ['Woolgoolga', 'Cnr Pacific Hwy, Pullen & Mackay Sts', '(02) 6690 8750'],
1353 => ['Inverell', 'Cnr Vivian And Sweaney Sts', '(02) 6721 7102'],
2689 => ['Grafton', '42 Duke St', '(02) 6641 5502'],
];
$str = array_rand($strs);
?>
<p align="center">
<img src="https://os.toplogistics.com.au/images/ww_logo.png" width="260" style="margin: 25px 0 10px 0;" /><br />
<b><?=$str.' '.$strs[$str][0].' PH: '.$strs[$str][2];?></b><br />
<?=$strs[$str][1];?><br />
TAX INVOICE - ABN 34 007 873 118
</p>
<table width="100%">
<tr><td>&nbsp;</td><td align="right" width="40"><b>$</b> &nbsp;</td></tr>
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

	$st = sprintf('%.2f', floatval($r->eitems['q'][$i]) * $v);
	echo '<tr><td>', $r->receiptItemName($i), ' x ', $r->eitems['q'][$i], '</td><td align="right" valign="top">',$st,'</td></tr>';
	$ti += floatval($r->eitems['q'][$i]);
	$tot += $st;
}
?>
<tr><td><?=$ti;?> SUBTOTAL</td><td align="right">$<?=$tot;?></td></tr>
<tr><td style="font-size:1.6em"><b>TOTAL</b></td><td align="right" style="font-size:1.6em"><b>$<?=$tot;?></b></td></tr>
<tr><td colspan="2"><div style="margin: 10px 30px 0 50px; line-height: 0.6em;"><pre>-----------------------------<br />
  WOOLWORTHS           <?=$str;?><br />
  <?=substr(strtoupper($strs[$str][0]).str_pad(' ', 25), 0, 22);?>NSW<br />
MERCH ID:     611000<?=rand(100000000,999999999);?><br />
TERM ID:             W<?=rand(1000000,9999999);?><br />
CARD:..00..............<?=rand(1000,9999)?> T<br />
CREDIT:                CREDIT<br />
AID:           A00000000<?=rand(10000,99999)?><br />
PURCHASE:          <?=substr('      $'.$tot, -10);?><br />
       --------------<br />
TOTAL:             <?=substr('      $'.$tot, -10);?><br />
APPROVED                   00<br />
<?=$date;?>      <?=$d['no'];?><br />
-----------------------------</pre>
</div></td></tr>
<tr><td>EFT</td><td align="right" valign="top"><?=$tot;?></td></tr>
<tr><td>Change</td><td align="right" valign="top">$0.00</td></tr>
</table>
<br />
<br />

<table width="100%">
<tr><td colspan="2">#Taxable Items</td></tr>
<tr><td>TOTAL include GST</td><td align="right" valign="top">$0.00</td></tr>
<tr><td colspan="2"><br />
******* Thanks for picking Woolies *******<br /><br /></td></tr>
<tr><td><?=$str.' &nbsp;  0'.rand(10,30).' &nbsp;&nbsp; '.$d['no'];?></td><td align="right" valign="top"><?=$date;?></td></tr>
</table>
<br />
</body>
</html>
