<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Translation Manager</title>
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
    <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
    <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
    <script>//https://github.com/rails/jquery-ujs/blob/master/src/rails.js
        (function(e,t){if(e.rails!==t){e.error("jquery-ujs has already been loaded!")}var n;var r=e(document);e.rails=n={linkClickSelector:"a[data-confirm], a[data-method], a[data-remote], a[data-disable-with]",buttonClickSelector:"button[data-remote], button[data-confirm]",inputChangeSelector:"select[data-remote], input[data-remote], textarea[data-remote]",formSubmitSelector:"form",formInputClickSelector:"form input[type=submit], form input[type=image], form button[type=submit], form button:not([type])",disableSelector:"input[data-disable-with], button[data-disable-with], textarea[data-disable-with]",enableSelector:"input[data-disable-with]:disabled, button[data-disable-with]:disabled, textarea[data-disable-with]:disabled",requiredInputSelector:"input[name][required]:not([disabled]),textarea[name][required]:not([disabled])",fileInputSelector:"input[type=file]",linkDisableSelector:"a[data-disable-with]",buttonDisableSelector:"button[data-remote][data-disable-with]",CSRFProtection:function(t){var n=e('meta[name="csrf-token"]').attr("content");if(n)t.setRequestHeader("X-CSRF-Token",n)},refreshCSRFTokens:function(){var t=e("meta[name=csrf-token]").attr("content");var n=e("meta[name=csrf-param]").attr("content");e('form input[name="'+n+'"]').val(t)},fire:function(t,n,r){var i=e.Event(n);t.trigger(i,r);return i.result!==false},confirm:function(e){return confirm(e)},ajax:function(t){return e.ajax(t)},href:function(e){return e.attr("href")},handleRemote:function(r){var i,s,o,u,a,f,l,c;if(n.fire(r,"ajax:before")){u=r.data("cross-domain");a=u===t?null:u;f=r.data("with-credentials")||null;l=r.data("type")||e.ajaxSettings&&e.ajaxSettings.dataType;if(r.is("form")){i=r.attr("method");s=r.attr("action");o=r.serializeArray();var h=r.data("ujs:submit-button");if(h){o.push(h);r.data("ujs:submit-button",null)}}else if(r.is(n.inputChangeSelector)){i=r.data("method");s=r.data("url");o=r.serialize();if(r.data("params"))o=o+"&"+r.data("params")}else if(r.is(n.buttonClickSelector)){i=r.data("method")||"get";s=r.data("url");o=r.serialize();if(r.data("params"))o=o+"&"+r.data("params")}else{i=r.data("method");s=n.href(r);o=r.data("params")||null}c={type:i||"GET",data:o,dataType:l,beforeSend:function(e,i){if(i.dataType===t){e.setRequestHeader("accept","*/*;q=0.5, "+i.accepts.script)}if(n.fire(r,"ajax:beforeSend",[e,i])){r.trigger("ajax:send",e)}else{return false}},success:function(e,t,n){r.trigger("ajax:success",[e,t,n])},complete:function(e,t){r.trigger("ajax:complete",[e,t])},error:function(e,t,n){r.trigger("ajax:error",[e,t,n])},crossDomain:a};if(f){c.xhrFields={withCredentials:f}}if(s){c.url=s}return n.ajax(c)}else{return false}},handleMethod:function(r){var i=n.href(r),s=r.data("method"),o=r.attr("target"),u=e("meta[name=csrf-token]").attr("content"),a=e("meta[name=csrf-param]").attr("content"),f=e('<form method="post" action="'+i+'"></form>'),l='<input name="_method" value="'+s+'" type="hidden" />';if(a!==t&&u!==t){l+='<input name="'+a+'" value="'+u+'" type="hidden" />'}if(o){f.attr("target",o)}f.hide().append(l).appendTo("body");f.submit()},formElements:function(t,n){return t.is("form")?e(t[0].elements).filter(n):t.find(n)},disableFormElements:function(t){n.formElements(t,n.disableSelector).each(function(){n.disableFormElement(e(this))})},disableFormElement:function(e){var t=e.is("button")?"html":"val";e.data("ujs:enable-with",e[t]());e[t](e.data("disable-with"));e.prop("disabled",true)},enableFormElements:function(t){n.formElements(t,n.enableSelector).each(function(){n.enableFormElement(e(this))})},enableFormElement:function(e){var t=e.is("button")?"html":"val";if(e.data("ujs:enable-with"))e[t](e.data("ujs:enable-with"));e.prop("disabled",false)},allowAction:function(e){var t=e.data("confirm"),r=false,i;if(!t){return true}if(n.fire(e,"confirm")){r=n.confirm(t);i=n.fire(e,"confirm:complete",[r])}return r&&i},blankInputs:function(t,n,r){var i=e(),s,o,u=n||"input,textarea",a=t.find(u);a.each(function(){s=e(this);o=s.is("input[type=checkbox],input[type=radio]")?s.is(":checked"):s.val();if(!o===!r){if(s.is("input[type=radio]")&&a.filter('input[type=radio]:checked[name="'+s.attr("name")+'"]').length){return true}i=i.add(s)}});return i.length?i:false},nonBlankInputs:function(e,t){return n.blankInputs(e,t,true)},stopEverything:function(t){e(t.target).trigger("ujs:everythingStopped");t.stopImmediatePropagation();return false},disableElement:function(e){e.data("ujs:enable-with",e.html());e.html(e.data("disable-with"));e.bind("click.railsDisable",function(e){return n.stopEverything(e)})},enableElement:function(e){if(e.data("ujs:enable-with")!==t){e.html(e.data("ujs:enable-with"));e.removeData("ujs:enable-with")}e.unbind("click.railsDisable")}};if(n.fire(r,"rails:attachBindings")){e.ajaxPrefilter(function(e,t,r){if(!e.crossDomain){n.CSRFProtection(r)}});r.delegate(n.linkDisableSelector,"ajax:complete",function(){n.enableElement(e(this))});r.delegate(n.buttonDisableSelector,"ajax:complete",function(){n.enableFormElement(e(this))});r.delegate(n.linkClickSelector,"click.rails",function(r){var i=e(this),s=i.data("method"),o=i.data("params"),u=r.metaKey||r.ctrlKey;if(!n.allowAction(i))return n.stopEverything(r);if(!u&&i.is(n.linkDisableSelector))n.disableElement(i);if(i.data("remote")!==t){if(u&&(!s||s==="GET")&&!o){return true}var a=n.handleRemote(i);if(a===false){n.enableElement(i)}else{a.error(function(){n.enableElement(i)})}return false}else if(i.data("method")){n.handleMethod(i);return false}});r.delegate(n.buttonClickSelector,"click.rails",function(t){var r=e(this);if(!n.allowAction(r))return n.stopEverything(t);if(r.is(n.buttonDisableSelector))n.disableFormElement(r);var i=n.handleRemote(r);if(i===false){n.enableFormElement(r)}else{i.error(function(){n.enableFormElement(r)})}return false});r.delegate(n.inputChangeSelector,"change.rails",function(t){var r=e(this);if(!n.allowAction(r))return n.stopEverything(t);n.handleRemote(r);return false});r.delegate(n.formSubmitSelector,"submit.rails",function(r){var i=e(this),s=i.data("remote")!==t,o,u;if(!n.allowAction(i))return n.stopEverything(r);if(i.attr("novalidate")==t){o=n.blankInputs(i,n.requiredInputSelector);if(o&&n.fire(i,"ajax:aborted:required",[o])){return n.stopEverything(r)}}if(s){u=n.nonBlankInputs(i,n.fileInputSelector);if(u){setTimeout(function(){n.disableFormElements(i)},13);var a=n.fire(i,"ajax:aborted:file",[u]);if(!a){setTimeout(function(){n.enableFormElements(i)},13)}return a}n.handleRemote(i);return false}else{setTimeout(function(){n.disableFormElements(i)},13)}});r.delegate(n.formInputClickSelector,"click.rails",function(t){var r=e(this);if(!n.allowAction(r))return n.stopEverything(t);var i=r.attr("name"),s=i?{name:i,value:r.val()}:null;r.closest("form").data("ujs:submit-button",s)});r.delegate(n.formSubmitSelector,"ajax:send.rails",function(t){if(this==t.target)n.disableFormElements(e(this))});r.delegate(n.formSubmitSelector,"ajax:complete.rails",function(t){if(this==t.target)n.enableFormElements(e(this))});e(function(){n.refreshCSRFTokens()})}})(jQuery)
    </script>
    <style>
        a.status-1{
            font-weight: bold;
        }
    </style>
    <script>
        jQuery(document).ready(function($){
            $('.editable').editable().on('hidden', function(e, reason){
                var locale = $(this).data('locale');
                if(reason === 'save'){
                    $(this).removeClass('status-0').addClass('status-1');
                }
                if(reason === 'save' || reason === 'nochange') {
                    var $next = $(this).closest('tr').next().find('.editable.locale-'+locale);
                    setTimeout(function() {
                        $next.editable('show');
                    }, 300);
                }
            });

            $('.group-select').on('change', function(){
                window.location.href = '<?= action($controller.'@getIndex') ?>/'+$(this).val();
            });


            $('a.delete-key').on('ajax:success', function (e, data) {
                $(this).closest('tr').remove();
            });

            $('.form-import').on('ajax:success', function (e, data) {
                $('div.success-import strong.counter').text(data.counter);
                $('div.success-import').slideDown();
            });

            $('.form-find').on('ajax:success', function (e, data) {
                $('div.success-find strong.counter').text(data.counter);
                $('div.success-find').slideDown();
            });

            $('.form-publish').on('ajax:success', function (e, data) {
                $('div.success-publish').slideDown();
            });

        })
    </script>
</head>
<body>
<div style="width: 80%; margin: auto;">
    <h1>Translation Manager</h1>
    <p>Warning, translations are not visible until they are exported back to the app/lang file, using 'php artisan translation:export' command or publish button.</p>
    <div class="alert alert-success success-import" style="display:none;">
        <p>Done importing, processed <strong class="counter">N</strong> items! Reload this page to refresh the groups!</p>
    </div>
    <div class="alert alert-success success-find" style="display:none;">
        <p>Done searching for translations, found <strong class="counter">N</strong> items!</p>
    </div>
    <div class="alert alert-success success-publish" style="display:none;">
        <p>Done publishing the translations for group '<?= $group ?>'!</p>
    </div>
    <?php if(Session::has('successPublish')) : ?>
        <div class="alert alert-info">
            <?php echo Session::get('successPublish'); ?>
        </div>
    <?php endif; ?>
    <p>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#searchModel" style="float:right; display:inline">
            Search
        </button>
        <?php if($editMode == "FULL" && !isset($group)) : ?>
    <form class="form-inline form-import" method="POST" action="<?= action($controller.'@postImport') ?>" data-remote="true" role="form">
        <select name="replace" class="form-control">
            <option value="0">Append new translations</option>
            <option value="1">Replace existing translations</option>
        </select>
        <button type="submit" class="btn btn-success"  data-disable-with="Loading..">Import groups</button>
    </form>
    <form class="form-inline form-find" method="POST" action="<?= action($controller.'@postFind') ?>" data-remote="true" role="form" data-confirm="Are you sure you want to scan you app folder? All found translation keys will be added to the database.">
        <button type="submit" class="btn btn-info" data-disable-with="Searching.." >Find translations in files</button>
    </form>
<?php endif; ?>
    <?php if($editMode == "FULL" && isset($group)) : ?>
        <form class="form-inline form-publish" method="POST" action="<?= action($controller.'@postPublish', $group) ?>" data-remote="true" role="form" data-confirm="Are you sure you want to publish the translations group '<?= $group ?>? This will overwrite existing language files.">
            <button type="submit" class="btn btn-info" data-disable-with="Publishing.." >Publish translations</button>
        </form>
    <?php endif; ?>
    </p>
    <form role="form">
        <div class="form-group">
            <?= Form::select('group', $groups, $group, array('class'=>'form-control group-select')) ?>
        </div>
    </form>
    <?php if($group): ?>
        <?php if($editMode == "FULL") : ?>
        <form action="<?= action($controller.'@postAdd', array($group)) ?>" method="POST"  role="form">
            <textarea class="form-control" rows="3" name="keys" placeholder="Add 1 key per line, without the group prefix"></textarea>
            <input type="submit" value="Add keys" class="btn btn-primary">
        </form>
        <?php endif; ?>

        <h4>Total: <?= $numTranslations ?>, changed: <?= $numChanged ?></h4>
        <table class="table">
            <thead>
            <tr>
                <th width="15%">Key</th>
                <?php foreach($locales as $locale): ?>
                    <th><?= e($locale) ?></th>
                <?php endforeach; ?>
                <?php if($deleteEnabled): ?>
                    <th>&nbsp;</th>
                <?php endif; ?>
            </tr>
            </thead>
            <tbody>

            <?php foreach($translations as $key => $translation): ?>
                <tr id="<?= e($key) ?>">
                    <td><?= e($key) ?></td>
                    <?php foreach($locales as $locale): ?>
                        <?php $t = isset($translation[$locale]) ? $translation[$locale] : null?>

                        <td>
                            <a href="#edit" class="editable status-<?= $t ? e($t->status) : 0 ?> locale-<?= e($locale) ?>" data-locale="<?= e($locale) ?>" data-name="<?= $locale . "|" . e($key) ?>" id="username" data-type="textarea" data-pk="<?= $t ? $t->id : 0 ?>" data-url="<?= action($controller.'@postEdit', array($group)) ?>" data-title="Enter translation"><?= $t ? e($t->value) : '' ?></a>
                        </td>
                    <?php endforeach; ?>
                    <?php if($editMode == "FULL" && $deleteEnabled): ?>
                        <td>
                            <a href="<?= action($controller.'@postDelete', [$group, $key]) ?>" class="delete-key" data-method="POST" data-remote="true" data-confirm="Are you sure you want to delete the translations for '<?= $key ?>?"><span class="glyphicon glyphicon-trash"></span></a>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>

            </tbody>
        </table>
    <?php else: ?>
        <p>Choose a group to display the group translations. If no groups are visisble, make sure you have run the migrations and imported the translations.</p>

    <?php endif; ?>
</div>

<!-- Search Modal -->
<div class="modal fade" id="searchModel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Search translations</h4>
            </div>
            <div class="modal-body">
                <script>
                    $('#searchModel').on('ajax:success', function(event, data, status, xhr) {
                        $('#searchModel .results').html(data);
                    });
                </script>
                <form id="search-form" class="form-inline" method="GET" action="<?= action($controller.'@getSearch') ?>" data-remote="true">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="search" name="q" class="form-control">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
                <div class="results">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>