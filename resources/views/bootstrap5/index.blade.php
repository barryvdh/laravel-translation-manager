@extends(config('translation-manager.layout'))
@php($controller = \Barryvdh\TranslationManager\Controller::class)

@section('documentTitle')
    Translation Manager
@stop

@include('translation-manager::bootstrap5._notifications')

@section('content')
    @include('translation-manager::bootstrap5.blocks._mainBlock')
    @include('translation-manager::bootstrap5.blocks._addEditGroupKeys')
    @if($group)
        @include('translation-manager::bootstrap5.blocks._edit')
    @else
        @include('translation-manager::bootstrap5.blocks._supportedLocales')
        @include('translation-manager::bootstrap5.blocks._publishAll')
    @endif
@stop

@push('styles')
    {{--<link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>--}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/JoseVte/x-editable@1.5.3/dist/bootstrap5-editable/css/bootstrap-editable.css"/>
@endpush

@push('scripts')
    {{--<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>--}}
    <script src="https://cdn.jsdelivr.net/gh/JoseVte/x-editable@1.5.3/dist/bootstrap5-editable/js/bootstrap-editable.min.js"></script>
    @include('translation-manager::jsScript')
@endpush
