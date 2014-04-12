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
        <style>
            a.status-1{
                font-weight: bold;
            }
        </style>
        <script>
            $(document).ready(function(){
                $('.editable').editable();

                $('.group-select').on('change', function(){
                    window.location.href = '<?= action('Barryvdh\TranslationManager\Controller@getIndex') ?>/'+$(this).val();
                })

                $('.editable').on('hidden', function(e, reason){
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

                $('a.delete-key').on('click', function(e){
                    e.stopPropagation();
                    var el = $(this);
                    var key = el.data('key');
                    if(!confirm('Are you sure you want to delete all translations for key "'+key+'"?')){
                        return;
                    }

                    $.post( "<?= action('Barryvdh\TranslationManager\Controller@postDelete', [$group]) ?>", {key: key }, function( data ) {
                        el.closest('tr').remove();
                    });
                });

                $('a.import').click(function () {
                    var btn = $(this);
                    btn.button('loading');

                    $.get("<?= action('Barryvdh\TranslationManager\Controller@getImport') ?>",
                        null,
                        function(data) {
                            btn.button('reset');
                            $('div.success-import strong.counter').text(data.counter);
                            $('div.success-import').slideDown();
                        }
                    );
                });
            })
        </script>
    </head>
    <body>
        <div style="width: 80%; margin: auto;">
            <h1>Translation Manager</h1>
            <p>Warning, translations are not visible until they are exported back to the app/lang file, using 'php artisan translation:export'</p>
            <div class="alert alert-success success-import" style="display:none;">
                <p>Done importing, processed <strong class="counter">N</strong> items! Reload this page to refresh the groups!</p>
            </div>
            <?php if(Session::has('successPublish')) : ?>
            <div class="alert alert-info">
                <?php echo Session::get('successPublish'); ?>
            </div>
            <?php endif; ?>
            <p>
                <?php if(!isset($group)) : ?>
                <a href="<?= action('Barryvdh\TranslationManager\Controller@getImport') ?>" class="btn btn-success">Import groups</a>
                <?php endif; ?>
                <?php if(isset($group)) : ?>
                <a href="<?= action('Barryvdh\TranslationManager\Controller@getPublish', $group) ?>" class="btn btn-info">Publish translations</a>
                <?php endif; ?>
            </p>
            <form role="form">
                <div class="form-group">
                    <?= Form::select('group', $groups, $group, ['class'=>'form-control group-select']) ?>
                </div>
            </form>
            <?php if($group): ?>
            <form action="<?= action('Barryvdh\TranslationManager\Controller@postAdd', [$group]) ?>" method="POST"  role="form">
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
                    <th><?= $locale ?></th>
                    <?php endforeach; ?>
                    <th>&nbsp;</th>
                </tr>
                </thead>
                <tbody>

                <?php foreach($translations as $key => $translation): ?>
                <tr>
                    <td><?= $key ?></td>
                    <?php foreach($locales as $locale): ?>
                    <?php $t = isset($translation[$locale]) ? $translation[$locale] : null?>

                    <td>
                        <a href="#edit" class="editable status-<?= $t ? $t->status : 0 ?> locale-<?= $locale ?>" data-locale="<?= $locale ?>" data-name="<?= $locale . "|" . $key ?>" id="username" data-type="textarea" data-pk="<?= $t ? $t->id : 0 ?>" data-url="<?= $editUrl ?>" data-title="Enter translation"><?= $t ? $t->value : '' ?></a>
                    </td>
                    <?php endforeach; ?>
                    <td>
                        <a href="#delete" class="delete-key" data-key="<?= $key ?>"><span class="glyphicon glyphicon-trash"></span></a>
                    </td>
                </tr>
                <?php endforeach; ?>

                </tbody>
            </table>
        </div>
    </body>
</html>
