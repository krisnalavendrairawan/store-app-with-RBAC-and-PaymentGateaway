<div class="form-group row">
    <label for="{{ $name }}" class="col-sm-3 col-form-label">{{ __($label) }}</label>
    <div class="col-sm-9">
        <select name="{{ $name }}" id="{{ $name }}" class="form-select select2"
            aria-label="{{ __($label) }}">
            <option value="">Pilih {{ $label }}</option>
            @foreach ($options as $value => $text)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>
                    {{ $text }}
                </option>
            @endforeach
        </select>
    </div>
</div>
