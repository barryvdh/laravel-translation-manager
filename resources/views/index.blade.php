@extends(config('translation-manager.blade.layout', 'translation-manager::layout'))

@php($controller='\Barryvdh\TranslationManager\Controller')

@section('documentTitle')
    Translation Manager
@stop

@include(config('translation-manager.blade.partials.notifications', 'translation-manager::partials.notifications'))

@section('content')
    @include(config('translation-manager.blade.partials.main', 'translation-manager::partials.main'))
    @include(config('translation-manager.blade.partials.addEditGroupKeys', 'translation-manager::partials.addEditGroupKeys'))

    @if ($group)
        @include(config('translation-manager.blade.partials.edit', 'translation-manager::partials.edit'))
    @else
        @include(config('translation-manager.blade.partials.locales', 'translation-manager::partials.locales'))
        @include(config('translation-manager.blade.partials.publishAll', 'translation-manager::partials.publishAll'))
    @endif
@stop

@push('styles')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.1/bootstrap3-editable/css/bootstrap-editable.css" />
@endpush

@push('scripts')
    <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.1/bootstrap3-editable/js/bootstrap-editable.js"></script>
    <script src="//cdn.jsdelivr.net/gh/rails/jquery-ujs@master/src/rails.js"></script>
    @include('translation-manager::jsScript')
@endpush
