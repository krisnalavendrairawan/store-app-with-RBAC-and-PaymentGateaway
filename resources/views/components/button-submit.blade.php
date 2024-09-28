@props(['cancelRoute'])

<div class="form-group row">
    <div class="col-sm-9 offset-sm-3">
        <button type="submit" class="btn btn-primary">{{ __('label.save') }}</button>
        <a href="{{ $cancelRoute }}" class="btn btn-secondary">{{ __('label.cancel') }}</a>
    </div>
</div>