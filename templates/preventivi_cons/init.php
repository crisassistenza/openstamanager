<?php

use Modules\Preventivi\Preventivo;

$documento = Preventivo::find($id_record);
$records = $documento->interventi;

$id_cliente = $documento['idanagrafica'];
$id_sede = $documento['idsede'];

$pricing = $options['pricing'];
