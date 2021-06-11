<?php $_translation = null ?>
<div class="col-sm-8">
    <form class="form-add-locale" method="POST" role="form"
          action="{{ route('translation-manager.translation.edit-all', [ "groupKey" => $group, "translationKey" => $key]) }}">
        @csrf

        <div class="form-group">
            <label>group</label>
            <input class="form-control" disabled value="{{ $group }}"/>
        </div>
        <div class="form-group">
            <label>key</label>
            <input class="form-control" disabled value="{{ $key }}"/>
        </div>
        @foreach($locales as $locale)
            <?php
            if (isset($translations[ $key ][ $locale ])) {
                $_translation = $translations[ $key ][ $locale ];
            }
            ?>
            <div class="form-group">
                <label>{{ $locale }}</label>
                <textarea class="form-control" rows="3" name="value[{{ $locale }}]"
                          placeholder="">{!! isset($translations[$key][$locale]) ? $translations[$key][$locale]->value : "" !!}</textarea>
            </div>
        @endforeach
        <div class="form-group row">
            <div class="col-xs-6">
                <input type="submit" value="Save" class="btn btn-primary">
                <a href="{{ route( 'translation-manager.group.list', [ "groupKey" => $group ] ) }}"
                   class="btn btn-default">Cancel</a>

            </div>
            <div class="col-xs-6 text-right">
                <div class="btn-group">
                    @if( $prevTranslation != null )
                        <a href="{{ route( 'translation-manager.translation', [ "groupKey" => $prevTranslation['group'], "translationKey" => $prevTranslation['key'] ] ) }}"
                           class="btn btn-default"><span
                                    class="glyphicon glyphicon-chevron-left"></span> {{ $prevTranslation['key'] }}</a>
                    @endif
                    <a href="#" class="btn btn-default disabled">{{ $key }}</a>
                    @if( $nextTranslation != null )
                        <a href="{{ route( 'translation-manager.translation', [ "groupKey" => $nextTranslation['group'], "translationKey" => $nextTranslation['key'] ] ) }}"
                           class="btn btn-default">{{ $nextTranslation['key'] }} <span
                                    class="glyphicon glyphicon-chevron-right"></span></a>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>
@if( $_translation != null )
    <div class="col-sm-4">
        <fieldset>
            <legend>Variables</legend>
            <ul>
                @foreach( \Barryvdh\TranslationManager\Models\Translation::possibleVariables( $group, $key)->get() as $entry )
                    <li>{{ $entry->attribute }}</li>
                @endforeach
            </ul>
        </fieldset>
        <fieldset>
            <legend>URLs</legend>
            <ul>
                @foreach( \Barryvdh\TranslationManager\Models\Translation::urls( $group, $key)->get() as $entry )
                    <li>{{ $entry->url }}</li>
                @endforeach
            </ul>
        </fieldset>
        <fieldset>
            <legend>Source Locations</legend>
            <ul>
                @foreach( \Barryvdh\TranslationManager\Models\Translation::sourceLocations( $group, $key)->get() as $entry )
                    <li>{{ $entry->file_path }}:{{ $entry->file_line }}</li>
                @endforeach
            </ul>
        </fieldset>
    </div>
@endif