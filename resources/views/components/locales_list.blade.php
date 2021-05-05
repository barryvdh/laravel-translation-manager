<fieldset>
    <legend>Supported locales</legend>
    <p>
        Current supported locales:
    </p>
    <form  class="form-remove-locale" method="POST" role="form" action="<?php echo action('\Barryvdh\TranslationManager\Controller@postRemoveLocale') ?>" data-confirm="Are you sure to remove this locale and all of data?">
        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
        <ul class="list-locales">
        <?php foreach($locales as $locale): ?>
            <li>
                <div class="form-group">
                    <button type="submit" name="remove-locale[<?php echo $locale ?>]" class="btn btn-danger btn-xs" data-disable-with="...">
                        &times;
                    </button>
                    <?php echo $locale ?>

                </div>
            </li>
        <?php endforeach; ?>
        </ul>
    </form>
    <form class="form-add-locale" method="POST" role="form" action="<?php echo action('\Barryvdh\TranslationManager\Controller@postAddLocale') ?>">
        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
        <div class="form-group">
            <p>
                Enter new locale key:
            </p>
            <div class="row">
                <div class="col-sm-3">
                    <input type="text" name="new-locale" class="form-control" />
                </div>
                <div class="col-sm-2">
                    <button type="submit" class="btn btn-default btn-block"  data-disable-with="Adding..">Add new locale</button>
                </div>
            </div>
        </div>
    </form>
</fieldset>
<fieldset>
    <legend>Export all translations</legend>
    <form class="form-inline form-publish-all" method="POST" action="<?php echo action('\Barryvdh\TranslationManager\Controller@postPublish', '*') ?>" data-remote="true" role="form" data-confirm="Are you sure you want to publish all translations group? This will overwrite existing language files.">
        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
        <button type="submit" class="btn btn-primary" data-disable-with="Publishing.." >Publish all</button>
    </form>
</fieldset>