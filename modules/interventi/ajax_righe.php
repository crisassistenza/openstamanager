<?php

use Modules\Interventi\Intervento;

if (file_exists(__DIR__.'/../../../core.php')) {
    include_once __DIR__.'/../../../core.php';
} else {
    include_once __DIR__.'/../../core.php';
}

$show_prezzi = Auth::user()['gruppo'] != 'Tecnici' || (Auth::user()['gruppo'] == 'Tecnici' && setting('Mostra i prezzi al tecnico'));

$intervento = Intervento::find($id_record);
$righe = $intervento->getRighe();

if (!$righe->isEmpty()) {
    echo '
<table class="table table-striped table-hover table-condensed table-bordered">
    <thead>
        <tr>
            <th>'.tr('Descrizione').'</th>
            <th class="text-center" width="8%">'.tr('Q.tà').'</th>
            <th class="text-center" width="15%">'.tr('Prezzo di acquisto').'</th>';

    if ($show_prezzi) {
        echo '
            <th class="text-center" width="15%">'.tr('Prezzo di vendita').'</th>
            <th class="text-center" width="10%">'.tr('Iva').'</th>
            <th class="text-center" width="15%">'.tr('Imponibile').'</th>';
    }

    if (!$record['flag_completato']) {
        echo '
            <th class="text-center"  width="120" class="text-center">'.tr('#').'</th>';
    }
    echo '
        </tr>
    </thead>

    <tbody>';

    foreach ($righe as $riga) {
        $r = $riga->toArray();

        $extra = '';
        $mancanti = $riga->isArticolo() ? $riga->missing_serials_number : 0;
        if ($mancanti > 0) {
            $extra = 'class="warning"';
        }
        $descrizione = (!empty($riga->articolo) ? $riga->articolo->codice.' - ' : '').$riga['descrizione'];

        echo '
        <tr '.$extra.'>
            <td>
                '.Modules::link($riga->isArticolo() ? Modules::get('Articoli')['id'] : null, $riga->isArticolo() ? $riga['idarticolo'] : null, $descrizione);

        if ($riga->isArticolo()) {
            if (!empty($mancanti)) {
                echo '
                <br><b><small class="text-danger">'.tr('_NUM_ serial mancanti', [
                    '_NUM_' => $mancanti,
                ]).'</small></b>';
            }

            $serials = $riga->serials;
            if (!empty($serials)) {
                echo '
                <br>'.tr('SN').': '.implode(', ', $serials);
            }
        }

        echo '
            </td>';

        // Quantità
        echo '
            <td class="text-right">
                '.Translator::numberToLocale($r['qta'], 'qta').' '.$r['um'].'
            </td>';

        //Costo unitario
        echo '
            <td class="text-right">
                '.moneyFormat($riga->prezzo_unitario_acquisto).'
            </td>';

        if ($show_prezzi) {
            // Prezzo unitario
            echo '
            <td class="text-right">
                '.moneyFormat($riga->prezzo_unitario_vendita);

            if (abs($r['sconto_unitario']) > 0) {
                $text = $r['sconto_unitario'] > 0 ? tr('sconto _TOT_ _TYPE_') : tr('maggiorazione _TOT_ _TYPE_');

                echo '
                <br><small class="label label-danger">'.replace($text, [
                    '_TOT_' => Translator::numberToLocale(abs($r['sconto_unitario'])),
                    '_TYPE_' => ($r['tipo_sconto'] == 'PRC' ? '%' : currency()),
                ]).'</small>';
            }

            echo '
            </td>';

            echo '
            <td class="text-right">
                '.moneyFormat($r['iva']).'
            </td>';

            // Prezzo di vendita
            echo '
            <td class="text-right">
                '.moneyFormat($riga->imponibile).'
            </td>';
        }

        // Pulsante per riportare nel magazzino centrale.
        // Visibile solo se l'intervento non è stato nè fatturato nè completato.
        if (!$record['flag_completato']) {
            $link = $riga->isSconto() ? $structure->fileurl('row-edit.php') : $structure->fileurl('add_righe.php');
            $link = $riga->isArticolo() ? $structure->fileurl('add_articolo.php') : $link;

            echo '
            <td class="text-center">';

            if ($r['abilita_serial']) {
                echo '
                <button type="button" class="btn btn-info btn-xs" data-toggle="tooltip" onclick="launch_modal(\''.tr('Modifica articoli').'\', \''.$rootdir.'/modules/fatture/add_serial.php?id_module='.$id_module.'&id_record='.$id_record.'&idarticolo='.$r['idriga'].'&idriga='.$r['id'].'\');">
                    <i class="fa fa-barcode"></i>
                </button>';
            }

            echo '
                <button type="button" class="btn btn-warning btn-xs" data-toggle="tooltip" onclick="launch_modal(\''.tr('Modifica').'\', \''.$link.'?id_module='.$id_module.'&id_record='.$id_record.'&idriga='.$r['id'].'\');">
                    <i class="fa fa-edit"></i>
                </button>

                <button type="button" class="btn btn-danger btn-xs" data-toggle="tooltip" onclick="if(confirm(\''.tr('Eliminare questa riga?').'\')){ '.($riga->isArticolo() ? 'ritorna_al_magazzino' : 'elimina_riga').'( \''.$r['id'].'\' ); }">
                    <i class="fa fa-trash"></i>
                </button>
            </td>';
        }
        echo '
        </tr>';
    }

    echo '
    </tbody>
</table>';
} else {
    echo '
<p>'.tr('Nessuna riga presente').'.</p>';
}

?>

<script type="text/javascript">
    function elimina_riga( id ){
        $.post(globals.rootdir + '/modules/interventi/actions.php', { op: 'delriga', idriga: id }, function(data, result){
            if( result=='success' ){
                //ricarico l'elenco delle righe
                $('#righe').load( globals.rootdir + '/modules/interventi/ajax_righe.php?id_module=<?php echo $id_module; ?>&id_record=<?php echo $id_record; ?>');

                $('#costi').load(globals.rootdir + '/modules/interventi/ajax_costi.php?id_module=<?php echo $id_module; ?>&id_record=<?php echo $id_record; ?>');
            }
        });
    }

    function ritorna_al_magazzino( id ){
        $.post(globals.rootdir + '/modules/interventi/actions.php', {op: 'unlink_articolo', idriga: id, id_record: '<?php echo $id_record; ?>', id_module: '<?php echo $id_module; ?>' }, function(data, result){
            if( result == 'success' ){
                // ricarico l'elenco degli articoli
                $('#righe').load( globals.rootdir + '/modules/interventi/ajax_righe.php?id_module=<?php echo $id_module; ?>&id_record=<?php echo $id_record; ?>');

                $('#costi').load(globals.rootdir + '/modules/interventi/ajax_costi.php?id_module=<?php echo $id_module; ?>&id_record=<?php echo $id_record; ?>');
            }
        });
    }
</script>
