<form class="form-inline form-publish" method="POST" action="<?php echo action('\Barryvdh\TranslationManager\Controller@postPublish', $group) ?>" data-remote="true" role="form" data-confirm="Are you sure you want to publish the translations group '<?php echo $group ?>? This will overwrite existing language files.">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <button type="submit" class="btn btn-info" data-disable-with="Publishing.." >Publish translations</button>
    <a href="<?= action('\Barryvdh\TranslationManager\Controller@getIndex') ?>" class="btn btn-default">Back</a>
</form>