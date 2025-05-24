@foreach (\Modules\Service\Constants\ServiceCategory::IMPORT_CATEGORY_COLS as $value)
    <div class="form-group">
        <label class="col-md-2">
            {{__('service::dashboard.service_categories.form.import_selects.'.$value)}}
        </label>
        <div class="col-md-9">
            <select name="{{$value}}"
                    class="form-control select2"
                    data-name="{{$value}}">
                <option value=""></option>
                @foreach ($excel_cols as $col)
                    <option value="{{ $col }}" {{$col == $value ? 'selected' : ''}}>
                        {{ $col }}
                    </option>
                @endforeach
            </select>
            <div class="help-block"></div>
        </div>
    </div>
@endforeach
