<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;

class DataTable extends Component
{
    public $items;
    public array $columns;
    public array $actions;
    public string $emptyMessage;

    public function __construct(
        $items,
        array $columns = [],
        array $actions = [],
        string $emptyMessage = 'Nessun elemento trovato.'
    ) {
        $this->items = $items;
        $this->columns = $columns;
        $this->actions = $actions;
        $this->emptyMessage = $emptyMessage;
    }

    public function render()
    {
        return view('components.admin.data-table');
    }
}
