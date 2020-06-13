<div class="card mt-2">
    <div class="card-body">
        <form action="{{ action($controller.'@postAdd', array($group))}}" method="POST" role="form">
               @csrf()
          <div class="form-group">
                <label>Add new keys to this group</label>
                <textarea class="form-control" rows="3" name="keys" placeholder="Add 1 key per line, without the group prefix"></textarea>
            </div>
            <div class="form-group">
                <input type="submit" value="Add keys" class="btn btn-primary">
            </div>
        </form>
    <hr>
    <h4>Total: {{$numTranslations}}, changed: {{$numChanged}}</h4>
    <table class="table">
            <thead>
            <tr>
                <th width="15%">Key</th>
                @foreach ($locales as $locale)
                    <th>{{$locale}}</th>
                @endforeach
                @if ($deleteEnabled)
                    <th>&nbsp;</th>
                @endif
            </tr>
            </thead>
            <tbody>

           @foreach ($translations as $key => $translation)
               <tr id="{{{$key}}}">
                    <td>{{{$key}}}</td>
                   @foreach ($locales as $locale)
                       @php( $t = isset($translation[$locale]) ? $translation[$locale] : null)
                       <td>
                            <a href="#edit"
                               class="editable status-{{$t ? $t->status : 0 }} locale-{{$locale}}"
                               data-locale="{{$locale}}" data-name="{{$locale}} | {{{$key}}}"
                               id="username" data-type="textarea" data-pk="{{ $t ? $t->id : 0}}"
                               data-url="{{$editUrl}}"
                               data-title="Enter translation"><?php echo $t ? htmlentities($t->value, ENT_QUOTES, 'UTF-8', false) : '' ?></a>
                        </td>
                   @endforeach
                   @if ($deleteEnabled)
                       <td>
                            <a href="<?php echo action($controller . '@postDelete', [$group, $key]) ?>"
                               class="delete-key"
                               data-confirm="Are you sure you want to delete the translations for '{{{$key}}}?"><span
                                    class=" fa fa-trash"></span></a>
                        </td>
                   @endif
                </tr>
           @endforeach
            </tbody>
        </table>
    </div>
</div>
