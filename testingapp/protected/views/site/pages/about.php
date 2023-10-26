<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name . ' - About';
$this->breadcrumbs=array(
	'About',
);
?>
<!-- <h1>About</h1>

<p>This is a "static" page. You may change the content of this page
by updating the file <code><?php echo __FILE__; ?></code>.</p>
 -->

 <div class="container">
		<img src="images/cute.jpg" alt="Cute" class="image">
		<div class="text">
			<span>Hello, I'm Cute!</span>
			<div class="heart"></div>
		</div>
</div>

<style>
		.container {
			text-align: center;
			margin-top: 100px;
		}
		.image {
			max-width: 50%;
			border-radius: 50%;
			box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
		}
		.text {
			margin-top: 30px;
			font-size: 24px;
			color: #333;
		}
		.heart {
			width: 50px;
			height: 50px;
			background-color: #ff6b6b;
			transform: rotate(45deg);
			position: relative;
			margin-top: 30px;
			margin-left: 5px;
			margin-right: 5px;
		}
		.heart:before,
		.heart:after {
			content: "";
			width: 50px;
			height: 50px;
			background-color: #ff6b6b;
			border-radius: 50%;
			position: absolute;
		}
		.heart:before {
			border-radius: 50% 50% 50% 50%;
			top: -25px;
			left: 0;
		}
		.heart:after {
			border-radius: 50% 50% 50% 50%;
			top: 0;
			left: -25px;
		}
		.text:hover .heart:before,
		.text:hover .heart:after {
			background-color: #ff4d4d;
		}
	</style>