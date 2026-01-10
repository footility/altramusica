<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;

class FilterBar extends Component
{
    public array $filters;
    public string $route;

    public function __construct(array $filters = [], string $route = '')
    {
        $this->filters = $filters;
        $this->route = $route;
    }

    public function render()
    {
        return view('components.admin.filter-bar');
    }
}
