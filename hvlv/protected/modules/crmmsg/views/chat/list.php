<style type="text/css">
.panel {
	border-radius: 0 !important;
	border: 0;
	margin: 0 !important;
}
.panel-heading {
	border-radius: 0 !important;
}
.panel-heading a {
	text-decoration: none;
}
#active_panel a {
	color: #FFFFFF;
}
#closed_panel a {
	color: #000000;
}
.panel-body {
	max-height: 594px;
	overflow: auto;
	padding: 0;
}
.chatlist {
	border: 1px solid grey;
	height: 670px;
	overflow: hidden;
}
#next {
	width: 100%;
	border: 0;
	outline: 0;
	opacity: 0.5;
	display: none;
}
</style>

<div class="row chatlist">
	<div class="panel-group" id="list_panels">
		<div class="panel panel-primary" id="active_panel">
			<div class="panel-heading">
				<a data-toggle="collapse" data-parent="#list_panels" href="#collapseOne">
					<h3 class="panel-title">Active</h3>
				</a>
			</div>
			<div id="collapseOne" class="panel-collapse collapse in">
				<div class="panel-body">
				</div>
			</div>
		</div>
		<div class="panel panel-danger" id="closed_panel">
			<div class="panel-heading">
				<a data-toggle="collapse" data-parent="#list_panels" href="#collapseTwo">
					<h3 class="panel-title">Closed</h3>
				</a>
			</div>
			<div id="collapseTwo" class="panel-collapse collapse">
				<div class="panel-body">
				</div>
				<button class="btn btn-success" id="next"><span class="glyphicon glyphicon-arrow-down"></span></button>
			</div>
		</div>
	</div>
</div>