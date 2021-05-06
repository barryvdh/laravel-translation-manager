<form action="<?php echo action('\Barryvdh\TranslationManager\Controller@postAdd', array($group)) ?>" method="POST"
      role="form">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <div class="form-group">
        <label>Add new keys to this group</label>
        <textarea class="form-control" rows="3" name="keys"
                  placeholder="Add 1 key per line, without the group prefix"></textarea>
    </div>
    <div class="form-group">
        <input type="submit" value="Add keys" class="btn btn-primary">
    </div>
</form>
<div class="row">
    <div class="col-sm-2">
        <span class="btn btn-default enable-auto-translate-group">Use Auto Translate</span>
    </div>
</div>
<form class="form-add-locale autotranslate-block-group hidden" method="POST" role="form"
      action="<?php echo action('\Barryvdh\TranslationManager\Controller@postTranslateMissing') ?>">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="base-locale">Base Locale for Auto Translations</label>
                <select name="base-locale" id="base-locale" class="form-control">
                    <?php foreach ($locales as $locale): ?>
                    <option value="<?= $locale ?>"><?= $locale ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="new-locale">Enter target locale key</label>
                <input type="text" name="new-locale" class="form-control" id="new-locale"
                       placeholder="Enter target locale key"/>
            </div>
            <?php if(!config('laravel_google_translate.google_translate_api_key')): ?>
            <p>
                <code>Translating using stichoza/google-translate-php. If you would like to use Google Translate API
                    enter your Google Translate API key to config file laravel_google_translate</code>
            </p>
            <?php endif; ?>
            <div class="form-group">
                <input type="hidden" name="with-translations" value="1">
                <input type="hidden" name="file" value="<?= $group ?>">
                <button type="submit" class="btn btn-default btn-block" data-disable-with="Adding..">Auto translate
                    missing translations
                </button>
            </div>
        </div>
    </div>
</form>
<hr>
<h4>Total: <?= $numTranslations ?>, changed: <?= $numChanged ?></h4>
<table class="table">
    <thead>
    <tr>
        <th width="15%">Key</th>
        <?php foreach ($locales as $locale): ?>
        <th><?= $locale ?></th>
        <?php endforeach; ?>
        <?php if ($deleteEnabled): ?>
        <th>&nbsp;</th>
        <?php endif; ?>
    </tr>
    </thead>
    <tbody>

    <?php foreach ($translations as $key => $translation): ?>
    <tr id="<?php echo htmlentities($key, ENT_QUOTES, 'UTF-8', false) ?>">
        <td @if( config('translation-manager.warn_in_code', false ) && \Barryvdh\TranslationManager\Models\Translation::sourceLocations( $group, $key )->count() == 0 ) class="bg-danger" @endif><?php echo htmlentities($key, ENT_QUOTES, 'UTF-8', false) ?>
            <a href="<?php echo route('translation-manager.translation', [ "groupKey" => $group, "translationKey" => $key ]) ?>" ><span class="glyphicon glyphicon-new-window"></span></a>
        </td>
        <?php foreach ($locales as $locale): ?>
        <?php $t = isset($translation[$locale]) ? $translation[$locale] : null ?>

        <td>
            <a href="#edit"
               class="editable status-<?php echo $t ? $t->status : 0 ?> locale-<?php echo $locale ?>"
               data-locale="<?php echo $locale ?>"
               data-name="<?php echo $locale."|".htmlentities($key, ENT_QUOTES, 'UTF-8', false) ?>"
               id="username" data-type="textarea" data-pk="<?php echo $t ? $t->id : 0 ?>"
               data-url="<?php echo $editUrl ?>"
               data-title="Enter translation"><?php echo $t ? htmlentities($t->value, ENT_QUOTES, 'UTF-8',
                    false) : '' ?></a>
        </td>
        <?php endforeach; ?>
        <?php if ($deleteEnabled): ?>
        <td>
            <a href="<?php echo action('\Barryvdh\TranslationManager\Controller@postDelete', [$group, $key]) ?>"
               class="delete-key"
               data-confirm="Are you sure you want to delete the translations for '<?php echo htmlentities($key,
                   ENT_QUOTES, 'UTF-8', false) ?>?"><span
                        class="glyphicon glyphicon-trash"></span></a>
        </td>
        <?php endif; ?>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>