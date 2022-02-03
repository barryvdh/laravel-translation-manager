@extends(config('translation-manager.layout'))
@php($controller = \Barryvdh\TranslationManager\Controller::class)

@push('documentTitle')
    Translation Manager
@endpush

@push('styles')
    <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
    <style>
        a.status-1 {
            font-weight: bold;
        }
    </style>
@endpush

@push('scripts')
    <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
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
                <a href="{{ action($controller . '@getIndex') }}" class="navbar-brand">
                    Translation Manager
                </a>
            </div>
        </div>
    </header>
    <div class="container-fluid">
        <p>Warning, translations are not visible until they are exported back to the app/lang file, using <code>php artisan translation:export</code> command or publish button.</p>
        <div class="alert alert-success success-import" style="display:none;">
            <p>Done importing, processed <strong class="counter">N</strong> items! Reload this page to refresh the groups!</p>
        </div>
        <div class="alert alert-success success-find" style="display:none;">
            <p>Done searching for translations, found <strong class="counter">N</strong> items!</p>
        </div>
        <div class="alert alert-success success-publish" style="display:none;">
            <p>Done publishing the translations for group '{{ $group }}'!</p>
        </div>
        <div class="alert alert-success success-publish-all" style="display:none;">
            <p>Done publishing the translations for all group!</p>
        </div>
        @if(Session::has('successPublish'))
            <div class="alert alert-info">
                {{ Session::get('successPublish') }}
            </div>
        @endif
        <p>
        @if(!isset($group))
            <form class="form-import" method="POST" action="{{ action($controller . '@postImport') }}" data-remote="true" role="form">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-4">
                            <select name="replace" class="form-control">
                                <option value="0">Append new translations</option>
                                <option value="1">Replace existing translations</option>
                            </select>
                        </div>
                        <div class="col-sm-5 col-md-4">
                            <button type="submit" class="btn btn-success btn-block" data-disable-with="Loading..">Import groups</button>
                        </div>
                    </div>
                </div>
            </form>
            <form class="form-find" method="POST" action="{{ action($controller . '@postFind') }}" data-remote="true" role="form"
                  data-confirm="Are you sure you want to scan you app folder? All found translation keys will be added to the database.">
                <div class="form-group">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="submit" class="btn btn-info" data-disable-with="Searching..">Find translations in files</button>
                </div>
            </form>
        @else
            <form class="form-inline form-publish" method="POST"
                  action="{{ action($controller . '@postPublish', $group) }}" data-remote="true" role="form"
                  data-confirm="Are you sure you want to publish the translations group '{{ $group }}'? This will overwrite existing language files.">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <button type="submit" class="btn btn-info" data-disable-with="Publishing..">Publish translations</button>
                <a href="{{ action($controller . '@getIndex') }}" class="btn btn-default">Back</a>
            </form>
        @endif

        <form role="form" method="POST" action="{{ action($controller . '@postAddGroup') }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="form-group">
                <p>Choose a group to display the group translations. If no groups are visible, make sure you have run the migrations and imported the translations.</p>
                <select name="group" id="group" class="form-control group-select">
                    @foreach($groups as $key => $value)
                    <option value="{{ $key }}" {{ $key == $group ? ' selected' : '' }}>{{ $value }}</option>
                    @endforeach
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
        @if($group)
            <form action="{{ action($controller . '@postAdd', [$group]) }}" method="POST" role="form">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group">
                    <label>Add new keys to this group</label>
                    <textarea class="form-control" rows="3" name="keys" placeholder="Add 1 key per line, without the group prefix">{{ old('keys') }}</textarea>
                </div>
                <div class="form-group">
                    <input type="submit" value="Add keys" class="btn btn-primary">
                </div>
            </form>
            <hr>
            <h4>Total: {{ $numTranslations }}, changed: {{ $numChanged }}</h4>
            <table class="table">
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
                        <tr id="{{ htmlentities($key, ENT_QUOTES, 'UTF-8', false) }}">
                            <td>{{ htmlentities($key, ENT_QUOTES, 'UTF-8', false) }}</td>
                            @foreach ($locales as $locale)
                                @php($t = isset($translation[$locale]) ? $translation[$locale] : null)

                                <td>
                                    <a href="#edit"
                                       class="editable status-{{ $t ? $t->status : 0 }} locale-{{ $locale }}"
                                       data-locale="{{ $locale }}"
                                       data-name="{{ $locale . "|" . htmlentities($key, ENT_QUOTES, 'UTF-8', false) }}"
                                       id="username" data-type="textarea" data-pk="{{ $t ? $t->id : 0 }}"
                                       data-url="{{ $editUrl }}"
                                       data-title="Enter translation">{{ $t ? htmlentities($t->value, ENT_QUOTES, 'UTF-8', false) : '' }}</a>
                                </td>
                            @endforeach
                            @if ($deleteEnabled)
                                <td>
                                    <a href="{{ action($controller . '@postDelete', [$group, $key]) }}" class="delete-key"
                                       data-confirm="Are you sure you want to delete the translations for '{{ htmlentities($key, ENT_QUOTES, 'UTF-8', false) }}'?">
                                        <span class="glyphicon glyphicon-trash"></span>
                                    </a>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <fieldset>
                <legend>Supported locales</legend>
                <p>
                    Current supported locales:
                </p>
                <form class="form-remove-locale" method="POST" role="form"
                      action="{{ action($controller . '@postRemoveLocale') }}"
                      data-confirm="Are you sure to remove this locale and all of data?">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <ul class="list-locales">
                        @foreach($locales as $locale)
                            <li>
                                <div class="form-group">
                                    <button type="submit" name="remove-locale[{{ $locale }}]"
                                            class="btn btn-danger btn-xs" data-disable-with="...">
                                        &times;
                                    </button>
                                    {{ $locale }}
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </form>
                <form class="form-add-locale" method="POST" role="form"
                      action="{{ action($controller . '@postAddLocale') }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
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
                      action="{{ action($controller . '@postPublish', '*') }}" data-remote="true" role="form"
                      data-confirm="Are you sure you want to publish all translations group? This will overwrite existing language files.">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="submit" class="btn btn-primary" data-disable-with="Publishing..">Publish all</button>
                </form>
            </fieldset>

        @endif
    </div>
@stop
