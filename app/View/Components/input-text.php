<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class inputText extends Component
{
    /**
     * Create a new component instance.
     */


    public $label, $name, $type, $placeholder, $old;



    public function __construct($label, $name, $type = 'text', $placeholder = '', $old = '')
    {
        $this->label = $label;
        $this->name = $name;
        $this->type = $type;
        $this->placeholder = $placeholder;
        $this->old = $old;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.input-text');
    }
}
