<div class="mb-3">
    <label for="{{ $name }}" class="form-label">
        {{ $label }}
        @if($required)
            <span class="text-danger">*</span>
        @endif
    </label>

    @if($type === 'select')
        <select 
            name="{{ $name }}" 
            id="{{ $name }}" 
            class="form-select @error($name) is-invalid @enderror"
            {{ $required ? 'required' : '' }}
        >
            <option value="">{{ $placeholder ?: 'Seleziona...' }}</option>
            @foreach($options as $key => $option)
                <option value="{{ $key }}" {{ $value == $key ? 'selected' : '' }}>
                    {{ is_array($option) ? $option['label'] : $option }}
                </option>
            @endforeach
        </select>
    @elseif($type === 'textarea')
        <textarea 
            name="{{ $name }}" 
            id="{{ $name }}" 
            class="form-control @error($name) is-invalid @enderror"
            rows="3"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
        >{{ $value }}</textarea>
    @elseif($type === 'checkbox')
        <div class="form-check">
            <input 
                type="checkbox" 
                name="{{ $name }}" 
                id="{{ $name }}" 
                value="1"
                class="form-check-input @error($name) is-invalid @enderror"
                {{ $value ? 'checked' : '' }}
            >
            <label class="form-check-label" for="{{ $name }}">
                {{ $placeholder ?: $label }}
            </label>
        </div>
    @elseif($type === 'date')
        <input 
            type="date" 
            name="{{ $name }}" 
            id="{{ $name }}" 
            class="form-control @error($name) is-invalid @enderror"
            value="{{ $value ? (is_string($value) ? $value : $value->format('Y-m-d')) : '' }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
        >
    @else
        <input 
            type="{{ $type }}" 
            name="{{ $name }}" 
            id="{{ $name }}" 
            class="form-control @error($name) is-invalid @enderror"
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
        >
    @endif

    @if($help)
        <div class="form-text">{{ $help }}</div>
    @endif

    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
