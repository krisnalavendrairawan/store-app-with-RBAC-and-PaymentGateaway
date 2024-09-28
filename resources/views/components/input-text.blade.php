<div class="form-group row">
    <label for="{{ $name }}" class="col-sm-3 col-form-label">{{ __($label) }}</label>
    <div class="col-sm-9">
        <input type="{{ $type }}" class="form-control" id="{{ $name }}" name="{{ $name }}"
            placeholder="{{ __($placeholder) }} " value="{{ $old }}">
    </div>
</div>
