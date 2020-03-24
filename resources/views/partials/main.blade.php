<div class="card">
    <div class="card-body">
        <p>{!! trans('manager::translations.warning') !!}</p>

        @if(!isset($group))
            <form class="form-import" method="POST" action="{!! action($controller.'@postImport') !!}" data-remote="true" role="form">
                @csrf()
                <div class="row form-group">
                    <div class="col-auto">
                        <select name="replace" class="form-control">
                            <option value="0">{{ trans('manager::translations.appendNewTranslations') }}</option>
                            <option value="1">{{ trans('manager::translations.replaceExistingTranslations') }}</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-success btn-block" data-disable-with="Loading..">{{ trans('manager::translations.importGroups') }}</button>
                    </div>
                </div>
            </form>
            @if (config('translation-manager.blade.search_translations_enabled', true))
            <form class="form-find" method="POST" action="{!! action($controller.'@postFind') !!}" data-remote="true" role="form"
                  data-confirm="Are you sure you want to scan you app folder? All found translation keys will be added to the database.">
                @csrf()
                <div class="form-group">
                    <button type="submit" class="btn btn-info" data-disable-with="Searching..">{{ trans('manager::translations.findTranslationInFiles') }}</button>
                </div>
            </form>
            @endif
        @else
            <form class="form-inline form-publish" method="POST" action="{!! action($controller.'@postPublish', $group)  !!}" data-remote="true"
                  role="form" data-confirm="Are you sure you want to publish the translations group '{{$group}}? This will overwrite existing language files.">
                @csrf()
                <div class="btn-group" role="group">
                    <button type="submit" class="btn btn-info" data-disable-with="Publishing..">{{ trans('manager::translations.publish') }}</button>
                    <a href="{{action($controller.'@getIndex') }}" class="btn btn-secondary">{{ trans('manager::translations.back') }}</a>
                </div>
            </form>
        @endif
    </div>
</div>
