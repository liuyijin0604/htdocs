<style>
.settings h5, div.toggle {
	float: left;
}
.settings h5 {
	padding: 0;
	margin: 5px 0 20px 20px;
}
.settings br {
	clear: both;
}
</style>

<div class="content-padded settings">
<h2>Settings</h2>
<form id="settings-form" action="" method="post">

	<div class="toggle<?=@$user->extra['wma_settings']['stock'] ? ' active' : ''?>">
		<div class="toggle-handle"></div>
	</div>
	<h5>Stock</h5>
	<input type="hidden" name="wma_settings[stock]" value="<?=@$user->extra['wma_settings']['stock']?>" />
	<br />

	<div class="toggle<?=@$user->extra['wma_settings']['history'] ? ' active' : ''?>">
		<div class="toggle-handle"></div>
	</div>
	<h5>History</h5>
	<input type="hidden" name="wma_settings[history]" value="<?=@$user->extra['wma_settings']['history']?>" />
	<br />

	<div class="toggle<?=@$user->extra['wma_settings']['dashboard'] ? ' active' : ''?>">
		<div class="toggle-handle"></div>
	</div>
	<h5>Dashboard</h5>
	<input type="hidden" name="wma_settings[dashboard]" value="<?=@$user->extra['wma_settings']['dashboard']?>" />
	<br />

	<div class="toggle<?=@$user->extra['wma_settings']['batch_picking'] ? ' active' : ''?>">
		<div class="toggle-handle"></div>
	</div>
	<h5>Batch Picking</h5>
	<input type="hidden" name="wma_settings[batch_picking]" value="<?=@$user->extra['wma_settings']['batch_picking']?>" />
	<br />

	<div class="toggle<?=@$user->extra['wma_settings']['batch_sorting'] ? ' active' : ''?>">
		<div class="toggle-handle"></div>
	</div>
	<h5>Batch Sorting</h5>
	<input type="hidden" name="wma_settings[batch_sorting]" value="<?=@$user->extra['wma_settings']['batch_sorting']?>" />
	<br />

	<div class="toggle<?=@$user->extra['wma_settings']['adhoc_tasks'] ? ' active' : ''?>">
		<div class="toggle-handle"></div>
	</div>
	<h5>Adhoc Tasks</h5>
	<input type="hidden" name="wma_settings[adhoc_tasks]" value="<?=@$user->extra['wma_settings']['adhoc_tasks']?>" />
	<br />

	<div class="toggle<?=@$user->extra['wma_settings']['rts_check'] ? ' active' : ''?>">
		<div class="toggle-handle"></div>
	</div>
	<h5>RTS Check</h5>
	<input type="hidden" name="wma_settings[rts_check]" value="<?=@$user->extra['wma_settings']['rts_check']?>" />
	<br />

	<div class="toggle<?=@$user->extra['wma_settings']['quick_label'] ? ' active' : ''?>">
		<div class="toggle-handle"></div>
	</div>
	<h5>Quick Label</h5>
	<input type="hidden" name="wma_settings[quick_label]" value="<?=@$user->extra['wma_settings']['quick_label']?>" />
	<br />

	<button type="submit" class="btn btn-primary btn-block">Save</button>
</form>
</div>

<script>
$(function() {
	$('.settings .toggle').on('click', function() {
		if ($(this).prop('class').match(/active/)) {
			$(this).next().next().val(1);
		} else {
			$(this).next().next().val(0);
		}
	});

	$('#settings-form').on('success', function() {
		window.location.reload();
	});
});
</script>