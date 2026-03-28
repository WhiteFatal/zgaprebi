<?php
if(!defined('FILE_SECURITY_KEY')) die('Access Denied!');

$news = sql::getRows("SELECT * FROM `_cn_mod_news` WHERE `active` = 1 ORDER BY `Id` DESC");
$tales = sql::getRows("SELECT * FROM `_cn_mod_tales` WHERE `active` = 1 ORDER BY `Id` DESC");

ob_clean();
header("Content-type: application/xml");
print("<?xml version=\"1.0\" encoding=\"UTF-8\"?>");
print("<urlset
      xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\"
      xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
      xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\">");
	print("<url>");
		print("<loc>http://zgaprebi.ge/</loc>");
		print("<changefreq>always</changefreq>");
	print("</url>");
	print("<url>");
		print("<loc>http://zgaprebi.ge/home/</loc>");
		print("<changefreq>always</changefreq>");
	print("</url>");
	print("<url>");
		print("<loc>http://zgaprebi.ge/news/</loc>");
		print("<changefreq>always</changefreq>");
	print("</url>");
	foreach($news as $v){
		print("<url>");
			print("<loc>http://zgaprebi.ge".get_link('p=news&story='.$v['cat_Id'].'&seotitle='.$v['title'])."</loc>");
			print("<changefreq>always</changefreq>");
		print("</url>");
	}
	print("<url>");
		print("<loc>http://zgaprebi.ge/home/</loc>");
		print("<changefreq>always</changefreq>");
	print("</url>");
	print("<url>");
		print("<loc>http://zgaprebi.ge/tales/</loc>");
		print("<changefreq>always</changefreq>");
	print("</url>");
	foreach($tales as $v){
		print("<url>");
			print("<loc>http://zgaprebi.ge".get_link('p=tale&story='.$v['cat_Id'].'&seotitle='.$v['title'])."</loc>");
			print("<changefreq>always</changefreq>");
		print("</url>");
	}
	print("<url>");
		print("<loc>http://zgaprebi.ge/contact/</loc>");
		print("<changefreq>always</changefreq>");
	print("</url>");
	print("<url>");
		print("<loc>http://zgaprebi.ge/tbc-pay/</loc>");
		print("<changefreq>always</changefreq>");
	print("</url>");
print("</urlset>");
die();
?>