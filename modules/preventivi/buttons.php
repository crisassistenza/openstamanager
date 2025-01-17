<?php

include_once __DIR__.'/../../core.php';

echo'
<button type="button" class="btn btn-primary" onclick="if( confirm(\'Duplicare questo preventivo?\') ){ $(\'#copia-preventivo\').submit(); }"> <i class="fa fa-copy"></i> '.tr('Duplica preventivo').'</button>';

$disabled = $record['is_fatturabile'] || $record['is_completato'];
if (!$disabled) {
    echo '
	<button type="button" class="btn btn-warning" onclick="if(confirm(\'Vuoi creare un nuova revisione?\')){$(\'#crea-revisione\').submit();}"><i class="fa fa-edit"></i> '.tr('Crea nuova revisione...').'</button>';
}

// Creazione altri documenti
echo '
<div style="margin-left:4px;" class="dropdown pull-right">
    <button class="btn btn-info dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" '.($disabled ? '' : 'disabled').' >
        <i class="fa fa-magic"></i>&nbsp;'.tr('Crea').'...
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu dropdown-menu-right">
		<li>
		    <a class="'.($disabled ? '' : 'disabled').'" data-href="'.$structure->fileurl('crea_documento.php').'?id_module='.$id_module.'&id_record='.$id_record.'&documento=ordine" data-toggle="modal" data-title="'.tr('Crea ordine').'">
                <i class="fa fa-file-o"></i>&nbsp;'.tr('Ordine').'
            </a>
		</li>
		
		<li>
            <a class="'.($disabled ? '' : 'disabled').'" data-href="'.$structure->fileurl('crea_documento.php').'?id_module='.$id_module.'&id_record='.$id_record.'&documento=fattura" data-toggle="modal" data-title="'.tr('Crea fattura').'">
                <i class="fa fa-file"></i>&nbsp;'.tr('Fattura').'
            </a>
        </li>
	</ul>
</div>';

// Duplica preventivo
echo '
<form action="" method="post" id="copia-preventivo">
    <input type="hidden" name="backto" value="record-edit">
    <input type="hidden" name="op" value="copy">
</form>';

// Crea revisione
echo '
<form action="" method="post" id="crea-revisione">
	<input type="hidden" name="backto" value="record-edit">
	<input type="hidden" name="op" value="add_revision">
	<input type="hidden" name="id_record" value="'.$id_record.'">
</form>';
