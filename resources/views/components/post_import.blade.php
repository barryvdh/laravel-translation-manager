<form class="form-import" method="POST" action="<?php echo action('\Barryvdh\TranslationManager\Controller@postImport') ?>" data-remote="true" role="form">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <div class="form-group">
        <div class="row">
            <div class="col-sm-3">
                <select name="replace" class="form-control">
                    <option value="0">Append new translations</option>
                    <option value="1">Replace existing translations</option>
                </select>
            </div>
            <div class="col-sm-2">
            <button type="submit" class="btn btn-success btn-block"  data-disable-with="Loading..">Import groups</button>
            </div>
        </div>
    </div>
</form>
<form class="form-find" method="POST" action="<?php echo action('\Barryvdh\TranslationManager\Controller@postFind') ?>" data-remote="true" role="form" data-confirm="Are you sure you want to scan you app folder? All found translation keys will be added to the database.">
    <div class="form-group">
        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
        <button type="submit" class="btn btn-info" data-disable-with="Searching.." >Find translations in files</button>
    </div>
</form>