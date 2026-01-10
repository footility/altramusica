<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;

class FormField extends Component
{
    public string $name;
    public string $label;
    public string $type;
    public $value;
    public bool $required;
    public string $placeholder;
    public array $options;
    public string $help;
    public string $error;

    public function __construct(
        string $name,
        string $label = '',
        string $type = 'text',
        $value = null,
        bool $required = false,
        string $placeholder = '',
        array $options = [],
        string $help = '',
        string $error = ''
    ) {
        $this->name = $name;
        $this->label = $label ?: ucfirst(str_replace('_', ' ', $name));
        $this->type = $type;
        $this->value = old($name, $value);
        $this->required = $required;
        $this->placeholder = $placeholder;
        $this->options = $options;
        $this->help = $help;
        $this->error = $error ?: (session('errors')?->first($name) ?? '');
    }

    public function render()
    {
        return view('components.admin.form-field');
    }
}
