<style type="text/css">
hr {
	margin: 10px;
}
.chattickets {
	border: 1px solid grey;
	height: 670px;
	overflow: auto;
	padding-top: 10px;
}
.ticket {
	margin: 0 10px;
	cursor: pointer;
}
.ticketcontent {
	word-break: break-all;
	text-overflow: ellipsis;
	color: #CCCCCC;

	display: -webkit-box;
	-webkit-box-orient: vertical;
	-webkit-line-clamp: 3;
	overflow:hidden;
}
</style>

<div class="row chattickets">
	<?php if (!empty($tickets)) { ?>
		<?php foreach ($tickets as $ticket) { ?>
		<div id="ticket_<?=$ticket->id?>" class="ticket">
			<?php
				echo '<span>' . $ticket->no . '</span><br />';

				$shipments = 'Shipments: ';
				foreach ($ticket->shipments as $shipment) {
					$shipments .= $shipment->hbn . ', ';
				}
				echo '<span class="ticketcontent">' . substr($shipments, 0, strlen($shipments) - 2) . '</span>';
			?>
		</div>
		<hr>
		<?php } ?>
	<?php } else { ?>
	<h2 class="ticket">No tickets</h2>
	<?php } ?>
</div>