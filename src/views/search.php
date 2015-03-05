<h4>Results found: <?= $numTranslations ?></h4>
<table class="table">
    <thead>
    <tr>
        <th width="10%">Group</th>
        <th width="15%">Key</th>
        <th width="10%">Locale</th>
        <th width="65%">Translation</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($translations as $t): ?>
        <?php $groupUrl = action('Barryvdh\TranslationManager\Controller@getIndex', $t->group); ?>
        <tr>
            <td><a href="<?= $groupUrl ?>#<?= $t->key ?>"><?= $t->group ?></a></td>
            <td><?= $t->key ?></td>
            <td><?= $t->locale ?></td>
            <td><?= htmlentities($t->value, ENT_QUOTES, 'UTF-8', false) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

