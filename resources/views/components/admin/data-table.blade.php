<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                @foreach($columns as $column)
                    <th>{{ $column['label'] ?? ucfirst(str_replace('_', ' ', $column['key'])) }}</th>
                @endforeach
                @if(count($actions) > 0)
                    <th class="text-end">Azioni</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
                <tr>
                    @foreach($columns as $column)
                        <td>
                            @if(isset($column['format']) && $column['format'] === 'date')
                                {{ $item->{$column['key']} ? $item->{$column['key']}->format('d/m/Y') : '-' }}
                            @elseif(isset($column['format']) && $column['format'] === 'datetime')
                                {{ $item->{$column['key']} ? $item->{$column['key']}->format('d/m/Y H:i') : '-' }}
                            @elseif(isset($column['format']) && $column['format'] === 'currency')
                                € {{ number_format($item->{$column['key']}, 2, ',', '.') }}
                            @elseif(isset($column['format']) && $column['format'] === 'badge')
                                @php
                                    $value = $item->{$column['key']};
                                    $isBool = is_bool($value);
                                @endphp
                                @if($isBool)
                                    <span class="badge bg-{{ $value ? ($column['badge_class'] ?? 'success') : 'secondary' }}">
                                        {{ $value ? ($column['badge_true'] ?? 'Sì') : ($column['badge_false'] ?? 'No') }}
                                    </span>
                                @else
                                    <span class="badge bg-{{ $column['badge_class'] ?? 'secondary' }}">
                                        {{ $value }}
                                    </span>
                                @endif
                            @elseif(isset($column['relation']))
                                {{ $item->{$column['relation']}->{$column['key']} ?? '-' }}
                            @else
                                {{ $item->{$column['key']} ?? '-' }}
                            @endif
                        </td>
                    @endforeach
                    @if(count($actions) > 0)
                        <td class="text-end">
                            <div class="btn-group" role="group">
                                @foreach($actions as $action)
                                    @if($action['type'] === 'show')
                                        <a href="{{ route($action['route'], $item) }}" class="btn btn-sm btn-outline-info" title="Visualizza">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    @elseif($action['type'] === 'edit')
                                        <a href="{{ route($action['route'], $item) }}" class="btn btn-sm btn-outline-primary" title="Modifica">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @elseif($action['type'] === 'delete')
                                        <form action="{{ route($action['route'], $item) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ $action['confirm'] ?? 'Sei sicuro?' }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Elimina">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endforeach
                            </div>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columns) + (count($actions) > 0 ? 1 : 0) }}" class="text-center">
                        {{ $emptyMessage }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if(method_exists($items, 'links'))
    <div class="mt-3">
        {{ $items->links() }}
    </div>
@endif
