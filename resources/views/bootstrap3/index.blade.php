@extends(config('translation-manager.layout'))
@php($controller='\Barryvdh\TranslationManager\Controller')

@push('documentTitle')
    Translation Manager
@endpush

@push('styles')
    <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css"
          rel="stylesheet"/>
    <style>
        a.status-1 {
            font-weight: bold;
        }
    </style>
@endpush

@push('scripts')
    <script
        src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
    @include('translation-manager::jsScript')

@endpush

@section('content')
    <header class="navbar navbar-static-top navbar-inverse" id="top" role="banner">
        <div class="container-fluid">
            <div class="navbar-header">
                <button class="navbar-toggle collapsed" type="button" data-toggle="collapse"
                        data-target=".bs-navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a href="<?php echo action($controller . '@getIndex') ?>" class="navbar-brand">
                    Translation Manager
                </a>
            </div>
        </div>
    </header>
    <div class="container-fluid">
        <p>Warning, translations are not visible until they are exported back to the app/lang file, using <code>php
                artisan translation:export</code> command or publish button.</p>
        <div class="alert alert-success success-import" style="display:none;">
            <p>Done importing, processed <strong class="counter">N</strong> items! Reload this page to refresh the
                groups!</p>
        </div>
        <div class="alert alert-success success-find" style="display:none;">
            <p>Done searching for translations, found <strong class="counter">N</strong> items!</p>
        </div>
        <div class="alert alert-success success-publish" style="display:none;">
            <p>Done publishing the translations for group '<?php echo $group ?>'!</p>
        </div>
        <div class="alert alert-success success-publish-all" style="display:none;">
            <p>Done publishing the translations for all group!</p>
        </div>
        <?php if(Session::has('successPublish')) : ?>
        <div class="alert alert-info">
            <?php echo Session::get('successPublish'); ?>
        </div>
        <?php endif; ?>
        <p>
        <?php if(!isset($group)) : ?>
        <form class="form-import" method="POST" action="<?php echo action($controller . '@postImport') ?>"
              data-remote="true" role="form">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-4">
                        <select name="replace" class="form-control">
                            <option value="0">Append new translations</option>
                            <option value="1">Replace existing translations</option>
                        </select>
                    </div>
                    <div class="col-sm-5 col-md-4">
                        <button type="submit" class="btn btn-success btn-block" data-disable-with="Loading..">Import
                            groups
                        </button>
                    </div>
                </div>
            </div>
        </form>
        <form class="form-find" method="POST" action="<?php echo action($controller . '@postFind') ?>"
              data-remote="true" role="form"
              data-confirm="Are you sure you want to scan you app folder? All found translation keys will be added to the database.">
            <div class="form-group">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <button type="submit" class="btn btn-info" data-disable-with="Searching..">Find translations in files
                </button>
            </div>
        </form>
        <?php endif; ?>
        <?php if(isset($group)) : ?>
        <form class="form-inline form-publish" method="POST"
              action="<?php echo action($controller . '@postPublish', $group) ?>" data-remote="true" role="form"
              data-confirm="Are you sure you want to publish the translations group '<?php echo $group ?>? This will overwrite existing language files.">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <button type="submit" class="btn btn-info" data-disable-with="Publishing..">Publish translations</button>
            <a href="<?= action($controller . '@getIndex') ?>" class="btn btn-default">Back</a>
        </form>
        <?php endif; ?>

        <form role="form" method="POST" action="<?php echo action($controller . '@postAddGroup') ?>">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <div class="form-group">
                <p>Choose a group to display the group translations. If no groups are visisble, make sure you have run
                    the migrations and imported the translations.</p>
                <select name="group" id="group" class="form-control group-select">
                    <?php foreach($groups as $key => $value): ?>
                    <option
                        value="<?php echo $key ?>"<?php echo $key == $group ? ' selected' : '' ?>><?php echo $value ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Enter a new group name and start edit translations in that group</label>
                <input type="text" class="form-control" name="new-group"/>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-default" name="add-group" value="Add and edit keys"/>
            </div>
        </form>
        <?php if($group): ?>
        <form action="<?php echo action($controller . '@postAdd', array($group)) ?>" method="POST" role="form">
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
                <td><?php echo htmlentities($key, ENT_QUOTES, 'UTF-8', false) ?></td>
                <?php foreach ($locales as $locale): ?>
                <?php $t = isset($translation[$locale]) ? $translation[$locale] : null ?>

                <td>
                    <a href="#edit"
                       class="editable status-<?php echo $t ? $t->status : 0 ?> locale-<?php echo $locale ?>"
                       data-locale="<?php echo $locale ?>"
                       data-name="<?php echo $locale . "|" . htmlentities($key, ENT_QUOTES, 'UTF-8', false) ?>"
                       id="username" data-type="textarea" data-pk="<?php echo $t ? $t->id : 0 ?>"
                       data-url="<?php echo $editUrl ?>"
                       data-title="Enter translation"><?php echo $t ? htmlentities($t->value, ENT_QUOTES, 'UTF-8', false) : '' ?></a>
                </td>
                <?php endforeach; ?>
                <?php if ($deleteEnabled): ?>
                <td>
                    <a href="<?php echo action($controller . '@postDelete', [$group, $key]) ?>"
                       class="delete-key"
                       data-confirm="Are you sure you want to delete the translations for '<?php echo htmlentities($key, ENT_QUOTES, 'UTF-8', false) ?>?"><span
                            class="glyphicon glyphicon-trash"></span></a>
                </td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <fieldset>
            <legend>Supported locales</legend>
            <p>
                Current supported locales:
            </p>
            <form class="form-remove-locale" method="POST" role="form"
                  action="<?php echo action($controller . '@postRemoveLocale') ?>"
                  data-confirm="Are you sure to remove this locale and all of data?">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <ul class="list-locales">
                    <?php foreach($locales as $locale): ?>
                    <li>
                        <div class="form-group">
                            <button type="submit" name="remove-locale[<?php echo $locale ?>]"
                                    class="btn btn-danger btn-xs" data-disable-with="...">
                                &times;
                            </button>
                            <?php echo $locale ?>

                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </form>
            <form class="form-add-locale" method="POST" role="form"
                  action="<?php echo action($controller . '@postAddLocale') ?>">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <div class="form-group">
                    <p>
                        Enter new locale key:
                    </p>
                    <div class="row">
                        <div class="col-sm-3">
                            <input type="text" name="new-locale" class="form-control"/>
                        </div>
                        <div class="col-sm-3">
                            <button type="submit" class="btn btn-default btn-block" data-disable-with="Adding..">Add new
                                locale
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </fieldset>
        <fieldset>
            <legend>Export all translations</legend>
            <form class="form-inline form-publish-all" method="POST"
                  action="<?php echo action($controller . '@postPublish', '*') ?>" data-remote="true" role="form"
                  data-confirm="Are you sure you want to publish all translations group? This will overwrite existing language files.">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <button type="submit" class="btn btn-primary" data-disable-with="Publishing..">Publish all</button>
            </form>
        </fieldset>

        <?php endif; ?>
    </div>
@stop
