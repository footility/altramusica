<form method="GET" action="{{ $route }}" class="mb-3">
    <div class="row g-3">
        @foreach($filters as $filter)
            <div class="col-md-{{ $filter['width'] ?? 3 }}">
                @if($filter['type'] === 'text')
                    <input 
                        type="text" 
                        name="{{ $filter['name'] }}" 
                        class="form-control" 
                        placeholder="{{ $filter['placeholder'] ?? '' }}" 
                        value="{{ request($filter['name']) }}"
                    >
                @elseif($filter['type'] === 'select')
                    <select name="{{ $filter['name'] }}" class="form-select">
                        <option value="">{{ $filter['placeholder'] ?? 'Tutti' }}</option>
                        @foreach($filter['options'] as $key => $label)
                            <option value="{{ $key }}" {{ request($filter['name']) == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>
        @endforeach
        <div class="col-md-auto">
            <button type="submit" class="btn btn-outline-primary">Cerca</button>
        </div>
        <div class="col-md-auto">
            <a href="{{ $route }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </div>
</form>
