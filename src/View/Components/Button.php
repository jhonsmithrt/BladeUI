<?php

namespace BladeUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Button extends Component
{

    public string $uuid;

    public string $tooltipPosition = 'lg:tooltip-top';

    public function __construct(
        public ?string $label = null,
        public ?string $icon = null,
        public ?string $iconRight = null,
        public ?string $spinner = null,
        public ?string $link = null,
        public ?bool $external = false,
        public ?bool $noWireNavigate = false,
        public ?bool $responsive = false,
        public ?string $badge = null,
        public ?string $badgeClasses = null,
        public ?string $tooltip = null,
        public ?string $tooltipLeft = null,
        public ?string $tooltipRight = null,
        public ?string $tooltipBottom = null,
        public ?string $btn = "Default",
        public ?string $color = "primary",
        public ?string $size = "md",
    ) {
        $this->uuid = "bladeui" . md5(serialize($this));
        $this->tooltip = $this->tooltip ?? $this->tooltipLeft ?? $this->tooltipRight ?? $this->tooltipBottom;
        $this->tooltipPosition = $this->tooltipLeft ? 'lg:tooltip-left' : ($this->tooltipRight ? 'lg:tooltip-right' : ($this->tooltipBottom ? 'lg:tooltip-bottom' : 'lg:tooltip-top'));
    }

    public function spinnerTarget(): ?string
    {
        if ($this->spinner == 1) {
            return $this->attributes->whereStartsWith('wire:click')->first();
        }

        return $this->spinner;
    }

    public function render(): View|Closure|string
    {
        return <<<'HTML'
                @if($link)
                    <a href="{!! $link !!}"
                @else
                    <button
                @endif

                    wire:key="{{ $uuid }}"
                    {{ $attributes->whereDoesntStartWith('class')->merge(['type' => 'button']) }}
                    {{ $attributes->class([$this->getClass(),$this->btnSize(), "!inline-flex lg:tooltip $tooltipPosition" => $tooltip]) }}

                    @if($link && $external)
                        target="_blank"
                    @endif

                    @if($link && !$external && !$noWireNavigate)
                        wire:navigate
                    @endif

                    @if($tooltip)
                        data-tip="{{ $tooltip }}"
                    @endif

                    @if($spinner)
                        wire:target="{{ $spinnerTarget() }}"
                        wire:loading.attr="disabled"
                    @endif
                >

                    <!-- SPINNER LEFT -->
                    @if($spinner && !$iconRight)
                        <span wire:loading wire:target="{{ $spinnerTarget() }}" class="loading loading-spinner w-5 h-5"></span>
                    @endif

                    <!-- ICON -->
                    @if($icon)
                        <span class="block" @if($spinner) wire:loading.class="hidden" wire:target="{{ $spinnerTarget() }}" @endif>
                            <x-bladeui-icon :name="$icon" />
                        </span>
                    @endif

                    <!-- LABEL / SLOT -->
                    @if($label)
                        <span @class(["hidden lg:block" => $responsive ])>
                            {{ $label }}
                        </span>
                        @if(strlen($badge ?? '') > 0)
                            <span class="badge badge-primary {{ $badgeClasses }}">{{ $badge }}</span>
                        @endif
                    @else
                        {{ $slot }}
                    @endif

                    <!-- ICON RIGHT -->
                    @if($iconRight)
                        <span class="block" @if($spinner) wire:loading.class="hidden" wire:target="{{ $spinnerTarget() }}" @endif>
                            <x-bladeui-icon :name="$iconRight" />
                        </span>
                    @endif

                    <!-- SPINNER RIGHT -->
                    @if($spinner && $iconRight)
                        <span wire:loading wire:target="{{ $spinnerTarget() }}" class="loading loading-spinner w-5 h-5"></span>
                    @endif

                @if(!$link)
                    </button>
                @else
                    </a>
                @endif
            HTML;
    }
    public function getClass(): string
    {
        switch ($this->btn) {
            case 'default':
                return $this->Default();
            case 'outline':
                return $this->Outline();
            case 'gradiant':
                return $this->Gradient();
            default:
                return $this->Default();
        }
    }

    public function Default(): string
    {
        switch ($this->color) {
            case 'primary':
                return 'text-white bg-primary hover:bg-primary/80 focus:ring-1 focus:ring-primary font-medium rounded-lg shadow-lg';
            case 'secondary':
                return 'text-white bg-secondary hover:bg-secondary/80 focus:ring-1 focus:ring-secondary font-medium rounded-lg shadow-lg';
            case 'accent':
                return 'text-white bg-accent hover:bg-accent/80 focus:ring-1 focus:ring-accent font-medium rounded-lg shadow-lg';
            case 'neutral':
                return 'text-white bg-neutral hover:bg-neutral/80 focus:ring-1 focus:ring-neutral font-medium rounded-lg shadow-lg';
            case 'success':
                return 'text-white bg-success hover:bg-success/80 focus:ring-1 focus:ring-success font-medium rounded-lg shadow-lg';
            case 'warning':
                return 'text-black bg-warning hover:bg-warning/80 focus:ring-1 focus:ring-warning font-medium rounded-lg mb-2 shadow-lg';
            case 'error':
                return 'text-white bg-error hover:bg-error/80 focus:ring-1 focus:ring-error font-medium rounded-lg shadow-lg';
            default:
                return 'text-black bg-base-100 hover:bg-base-100/80 focus:ring-4 focus:ring-base-100 font-medium rounded-lg shadow-lg';
        }
    }
    public function Outline(): string
    {
        switch ($this->color) {
            case 'primary':
                return 'text-primary hover:text-white hover:bg-primary hover:border-primary border-primary border rounded-lg shadow-lg';
            case 'secondary':
                return 'text-secondary hover:text-white hover:bg-secondary hover:border-secondary border-secondary border rounded-lg shadow-lg';
            case 'accent':
                return 'text-accent hover:text-white hover:bg-accent hover:border-accent border-accent border rounded-lg shadow-lg';
            case 'neutral':
                return 'text-neutral hover:text-white hover:bg-neutral hover:border-neutral border-neutral border rounded-lg shadow-lg';
            case 'success':
                return 'text-success hover:text-white hover:bg-success hover:border-success border-success border rounded-lg shadow-lg';
            case 'warning':
                return 'text-warning hover:text-white hover:bg-warning hover:border-warning border-warning border rounded-lg shadow-lg';
            case 'error':
                return 'text-error hover:text-white hover:bg-error hover:border-error border-error border rounded-lg shadow-lg';
            default:
                return 'text-gray-500 hover:text-black hover:bg-base-100 hover:border-gray-500 border-gray-300 border rounded-lg shadow-lg';
        }
    }
    public function Gradient(): string
    {
        switch ($this->color) {
            case 'primary':
                return 'text-white bg-gradient-to-r from-primary/50 via-primary/80 to-primary hover:bg-gradient-to-br focus:outline-none shadow-md rounded-lg';
            case 'secondary':
                return 'text-white bg-gradient-to-r from-secondary/50 via-secondary/80 to-secondary hover:bg-gradient-to-br focus:outline-none shadow-md rounded-lg';
            case 'accent':
                return 'text-white bg-gradient-to-r from-accent/50 via-accent/80 to-accent hover:bg-gradient-to-br focus:outline-none shadow-md rounded-lg';
            case 'neutral':
                return 'text-white bg-gradient-to-r from-neutral/50 via-neutral/80 to-neutral hover:bg-gradient-to-br focus:outline-none shadow-md rounded-lg';
            case 'success':
                return 'text-white bg-gradient-to-r from-success/50 via-success/80 to-success hover:bg-gradient-to-br focus:outline-none shadow-md rounded-lg';
            case 'warning':
                return 'text-black bg-gradient-to-r from-warning/50 via-warning/80 to-warning hover:bg-gradient-to-br focus:outline-none shadow-md rounded-lg';
            case 'error':
                return 'text-white bg-gradient-to-r from-error/50 via-error/80 to-error hover:bg-gradient-to-br focus:outline-none shadow-md rounded-lg';
            default:
                return 'text-white bg-gradient-to-r from-gray-400 via-gray-500 to-gray-600 hover:bg-gradient-to-br focus:outline-none shadow-md rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2  shadow-md rounded-lg';
        }

    }

    public function btnSize(): string
    {
        switch ($this->size) {
            case 'xs':
                return 'px-3 py-2 text-xs font-medium ';
            case 'sm':
                return 'px-3 py-2 text-sm font-medium';
            case 'md':
                return 'px-5 py-2.5 text-sm font-medium';
            case 'lg':
                return 'px-5 py-3 text-base font-medium';
            case 'xl':
                return 'px-6 py-3.5 text-base font-medium';
            default:
                return 'px-5 py-2.5 text-sm font-medium';
        }
    }
}