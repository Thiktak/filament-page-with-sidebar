<?php

namespace AymanAlhattami\FilamentPageWithSidebar;

use Closure;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Navigation\NavigationItem;
use Illuminate\Support\HtmlString;

class PageNavigationBlock extends \Livewire\Component implements HasInfolists
{
    use InteractsWithInfolists;
    use \Filament\Infolists\Components\Concerns\HasChildComponents;

    protected string | Closure | null $group = null;

    /*public function isHiddenWhen(Closure|bool $condition): static
    {
        $this->isHidden = $condition instanceof Closure ? $condition() : $condition;

        return $this;
    }*/



    public function group(string | Closure | null $group): static
    {
        $this->group = $group;

        return $this;
    }

    public function getGroup(): mixed
    {
        return $this->group;
    }

    public function isHidden()
    {
        return false;
    }

    static public function make(): static
    {
        return new self();
    }

    public function blockInfolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema($this->childComponents);
    }

    public function render()
    {
        $i = new \Filament\Infolists\Infolist($this);
        $i->name('PageNavigationBlock');

        return new HtmlString($this->blockInfolist($i)->render());
    }
}
