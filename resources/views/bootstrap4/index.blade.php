@extends(config('translation-manager.layout'))
@php($controller = \Barryvdh\TranslationManager\Controller::class)

@section('documentTitle')
    Translation Manager
@stop
@include('translation-manager::bootstrap4._notifications')
@section('content')

    @include('translation-manager::bootstrap4.blocks._mainBlock')
    @if(!$selectedModel)
        @include('translation-manager::bootstrap4.blocks._addEditGroupKeys')
    @else
        @include('translation-manager::bootstrap4.blocks._selectEditModel')
    @endif
    @if($group)
        @include('translation-manager::bootstrap4.blocks._edit')
    @elseif($selectedModel)
        @include('translation-manager::bootstrap4.blocks._editModel')
    @else
        @include('translation-manager::bootstrap4.blocks._selectEditModel')
        @include('translation-manager::bootstrap4.blocks._supportedLocales')
        @include('translation-manager::bootstrap4.blocks._publishAll')
    @endif
@stop

@push('styles')
    {{--<link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>--}}
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.1/bootstrap3-editable/css/bootstrap-editable.css"/>
@endpush

@push('scripts')
    {{--<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>--}}
    <script src="https://cdn.jsdelivr.net/gh/Talv/x-editable@develop/dist/bootstrap4-editable/js/bootstrap-editable.min.js"></script>
    @include('translation-manager::jsScript')
@endpush
