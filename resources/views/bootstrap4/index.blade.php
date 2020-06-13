@extends(config('translation-manager.layout'))

@php($controller='\Barryvdh\TranslationManager\Controller')

@section('documentTitle')
Translation Manager
@stop
@include('translation-manager::bootstrap4._notifications')
@section('content')

@include('translation-manager::bootstrap4.blocks._mainBlock')
@include('translation-manager::bootstrap4.blocks._addEditGroupKeys')
@if($group)
@include('translation-manager::bootstrap4.blocks._edit')
@else
@include('translation-manager::bootstrap4.blocks._supportedLocales')
@include('translation-manager::bootstrap4.blocks._publishAll')
@endif
@stop

@push('styles')
{{--<link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>--}}
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.1/bootstrap3-editable/css/bootstrap-editable.css" />
@endpush

@push('scripts')
{{--<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>--}}
<script
src="https://cdn.jsdelivr.net/gh/Talv/x-editable@develop/dist/bootstrap4-editable/js/bootstrap-editable.min.js"></script>
@include('translation-manager::jsScript')
@endpush
