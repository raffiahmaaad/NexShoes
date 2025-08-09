<?php

namespace App\View\Components;

use App\Models\User;
use Illuminate\View\Component;

class Avatar extends Component
{
    public User $user;
    public string $size;
    public string $class;

    /**
     * Create a new component instance.
     */
    public function __construct(User $user, string $size = 'md', string $class = '')
    {
        $this->user = $user;
        $this->size = $size;
        $this->class = $class;
    }

    /**
     * Get the size classes for the avatar
     */
    public function getSizeClasses(): string
    {
        return match ($this->size) {
            'xs' => 'w-6 h-6 text-xs',
            'sm' => 'w-8 h-8 text-sm',
            'md' => 'w-10 h-10 text-sm',
            'lg' => 'w-12 h-12 text-base',
            'xl' => 'w-16 h-16 text-lg',
            '2xl' => 'w-20 h-20 text-xl',
            default => 'w-10 h-10 text-sm',
        };
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.user-avatar');
    }
}
