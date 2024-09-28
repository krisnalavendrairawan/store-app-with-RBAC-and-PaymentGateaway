<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TextAreae extends Component
{

    public $label, $old, $placeholder, $name;
    /**
     * Create a new component instance.
     */
    public function __construct($label, $old, $placeholder, $name)
    {
        $this->label = $label;
        $this->old = $old;
        $this->placeholder = $placeholder;
        $this->name = $name;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.text-area');
    }
}
