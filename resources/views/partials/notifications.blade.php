
@push('notifications')
    <div class="alert alert-success success-import" style="display:none;">
        <p>{!! trans('manager::translations.doneImporting') !!}</p>
    </div>
    <div class="alert alert-success success-find" style="display:none;">
        <p>{!! trans('manager::translations.doneSearching') !!}</p>
    </div>
    <div class="alert alert-success success-publish" style="display:none;">
        <p>{{ trans('manager::translations.donePublishingGroup', ['group' => $group]) }}</p>
    </div>
    <div class="alert alert-success success-publish-all" style="display:none;">
        <p>{{ trans('manager::translations.donePublishing') }}</p>
    </div>

    @if(Session::has('successPublish'))
        <div class="alert alert-info">
           {!! Session::get('successPublish') !!}
        </div>
    @endif
 @endpush
