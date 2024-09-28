<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ButtonSubmit extends Component
{
    public $label, $icon, $iconPosition, $cancelRoute;

    /**
     * Create a new component instance.
     */

    public function __construct($label = 'Submit', $icon = 'mdi mdi-content-save', $iconPosition = 'left', $cancelRoute = ''){
        $this->label = $label;
        $this->icon = $icon;
        $this->iconPosition = $iconPosition;
        $this->cancelRoute = $cancelRoute;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.button-submit');
    }
}
