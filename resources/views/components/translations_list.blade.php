<hr>
<h4>Total: {{ $numTranslations }}, changed: {{ $numChanged }}</h4>
<table class="table table-striped table-hover">
    <thead>
    <tr>
        <th width="15%">Key</th>
        @foreach ($locales as $locale)
            <th>{{ $locale }}</th>
        @endforeach
        @if ($deleteEnabled)
            <th>&nbsp;</th>
        @endif
    </tr>
    </thead>
    <tbody>

    @foreach ($translations as $key => $translation)
        <?php
        $isEmpty = false;
        if ($group == "") {
            foreach ($locales as $locale) {
                if (isset($translation[$locale])){
                    $group = $translation[$locale]->group;
                } else {
                    $isEmpty = true;
                }
            }
        }
        ?>
        <tr id="{!! htmlentities($key, ENT_QUOTES, 'UTF-8', false) !!}" @if( $isEmpty ) class="danger" @endif>
            <td @if( config('translation-manager.warn_in_code', false ) && \Barryvdh\TranslationManager\Models\Translation::sourceLocations( $group, $key )->count() == 0 ) class="danger" @endif>{!! htmlentities($key, ENT_QUOTES, 'UTF-8', false) !!}
                <a href="{{ route('translation-manager.translation', [ "groupKey" => $group, "translationKey" => $key ]) }}"><span
                            class="glyphicon glyphicon-new-window"></span></a>
            </td>
            @foreach ($locales as $locale)
                <?php $t = isset($translation[$locale]) ? $translation[$locale] : null ?>

                <td>
                    <a href="#edit"
                       class="editable status-{{ $t ? $t->status : 0 }} locale-{{ $locale }}"
                       data-locale="{{ $locale }}"
                       data-name="{!! $locale."|".htmlentities($key, ENT_QUOTES, 'UTF-8', false) !!}"
                       id="username" data-type="textarea" data-pk="{{ $t ? $t->id : 0 }}"
                       data-url="{{ route('translation-manager.translation.edit', ["groupKey" => $group]) }}"
                       data-title="Enter translation">{!! $t ? htmlentities($t->value, ENT_QUOTES, 'UTF-8',
                    false) : '' !!}</a>
                </td>
            @endforeach
            @if ($deleteEnabled)
                <td>
                    <a href="{{ action('\Barryvdh\TranslationManager\Controller@postDelete', [$group, $key]) }}"
                       class="delete-key"
                       data-confirm="Are you sure you want to delete the translations for '{!! htmlentities($key,
                   ENT_QUOTES, 'UTF-8', false) !!}?"><span
                                class="glyphicon glyphicon-trash"></span></a>
                </td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>