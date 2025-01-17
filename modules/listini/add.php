<?php

include_once __DIR__.'/../../core.php';

?><form action="" method="post" id="add-form">
	<input type="hidden" name="op" value="add">
	<input type="hidden" name="backto" value="record-edit">

	<div class="row">
		<div class="col-md-6">
			{[ "type": "text", "label": "<?php echo tr('Nome'); ?>", "name": "nome", "required": 1 ]}
		</div>

		<div class="col-md-6">
			{[ "type": "number", "label": "<?php echo tr('Rincaro/sconto'); ?>", "name": "prc_guadagno", "required": 1, "value": "0", "icon-after": "%", "help": "<?php echo tr('Il valore positivo indica uno sconto: per applicare una percentuale di rincaro inserire un valore negativo'); ?>" ]}
		</div>
	</div>

	<!-- PULSANTI -->
	<div class="row">
		<div class="col-md-12 text-right">
			<button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> <?php echo tr('Aggiungi'); ?></button>
		</div>
	</div>
</form>
