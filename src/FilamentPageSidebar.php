<?php

namespace AymanAlhattami\FilamentPageWithSidebar;

use AymanAlhattami\FilamentPageWithSidebar\Enums\PageSidebarLayoutEnum;
use Closure;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Filament\Support\Concerns\EvaluatesClosures;

class FilamentPageSidebar extends NavigationBuilder
{
    use EvaluatesClosures;

    protected ?Page $page;
    protected string | Closure | null  $layout = null;
    protected string | Closure | null  $title = null;
    protected string | Closure | null  $description = null;
    protected array $navigationItems;

    static public function make(?Page $page = null): static
    {
        return new static($page);
    }

    public function __construct(?Page $page = null)
    {
        $this->page = $page;
    }

    public function setTitle(string | Closure $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->evaluate($this->title);
    }

    public function setDescription(string | Closure $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->evaluate($this->description);
    }


    public function setNavigationItems(array $navigationItems): static
    {
        $this->navigationItems = $navigationItems;

        return $this;
    }

    public function getNavigationItems(): array
    {
        return $this->navigationItems;
    }

    public function layout($layout): static
    {
        $this->layout = $layout;
        return $this;
    }

    public function getLayout()
    {
        return $this->evaluate($this->layout) ?: $this->getResource()::getSidebarNavigationLayout();
    }

    public function isLayout(PageSidebarLayoutEnum $layout): bool
    {
        return $this->getLayout() === $layout;
    }

    public function getResource()
    {
        return $this->page->getResource();
    }

    public function getRecord()
    {
        return $this->page->getRecord();
    }


    // Sidebar methods:

    public function getSidebarNavigation(): array
    {
        // If defined, get directly
        $sidebarNavigationObject = $this->getResource()::getSidebarNavigation($this);
        $sidebarSubNavigationObject = $this->getResource()::getSidebarSubNavigation(self::make($this->page));

        $sidebarSubNavigationData = $sidebarSubNavigationObject->getNavigation();

        // When sub-group will be available by Filament:
        // For now: no nested groups ! :(
        if ($this->isLayout(PageSidebarLayoutEnum::TOP)) {
            // $sidebarNavigationObject->group('More', $sidebarSubNavigationData);
        }

        // Return Navigation, or the previous one
        $sidebarNavigationData = $sidebarNavigationObject->getNavigation();
        $sidebarNavigationData = $sidebarNavigationData ?: $this->getSidebarNavigationConverted();

        if (!empty($sidebarNavigationData)) {
            return [$sidebarNavigationData, $sidebarSubNavigationData];
        }

        return [];
    }

    protected function getSidebarNavigationConverted(): array
    {
        $sidebarNavigationObject = $this;

        // Convert previous version to NavigationGroup & Item
        $sidebarFirstVersion = $this->getResource()::sidebar($this->getRecord());
        $sidebarNavigationObject->groups(
            collect($sidebarFirstVersion->getNavigationItems())
                ->map(function ($item) {
                    if ($item instanceof PageNavigationItem) {
                        return $item;
                        /*return NavigationItem::make(null)
                            ->label($item->getLabel())
                            ->icon($item->getIcon())
                            ->activeIcon($item->getActiveIcon())
                            ->group($item->getGroup())
                            ->badge($item->getBadge(), $item->getBadgeColor())
                            ->url($item->getUrl())
                            ->act($item->isActive());*/
                    }
                })
                ->filter()
                ->groupBy(function ($it) {
                    return $it->getGroup();
                })
                ->map(function ($items, $group) {
                    return NavigationGroup::make($group)
                        ->items($items->toArray());
                })
                ->toArray()
        );
        $sidebarNavigationData = $sidebarNavigationObject->getNavigation();
        if (!empty($sidebarNavigationData)) {
            $this->setTitle($sidebarFirstVersion->getTitle());
            $this->setDescription($sidebarFirstVersion->getDescription());
            return $sidebarNavigationData;
        }

        return [];
    }


    public function getSidebarSubNavigation(): array
    {
        // If defined, get directly
        $sidebarNavigationObject = $this->getResource()::getSidebarSubNavigation(self::make($this->page));
        $sidebarNavigationData = $sidebarNavigationObject->getNavigation();
        if (!empty($sidebarNavigationData)) {
            return $sidebarNavigationData;
        }

        return [];
    }
}





    /*public function getSidebarNavigation()
    {
        $builder = new \Filament\Navigation\NavigationBuilder();
        $builder = $builder
            ->items([
                \Filament\Navigation\NavigationItem::make('home')
                    ->label('Dashboard')
                    ->icon('heroicon-o-home')
                    ->badge('2')
                    ->activeIcon('heroicon-s-users')
                    ->url($urlPeople = '?activeTab=People')
                    ->isActiveWhen(fn () => request()->fullUrlIs('*' . $urlPeople . '*')),
            ])
            ->groups([
                \Filament\Navigation\NavigationGroup::make('pages1')
                    ->label('Pages group 1')
                    ->items([
                        \Filament\Navigation\NavigationItem::make('item11')
                            ->label('toto item')
                            ->icon('heroicon-o-users'),

                        \Filament\Navigation\NavigationItem::make('item12')
                            ->label('toto item 2')
                            ->icon('heroicon-o-pencil')
                            ->badge('badge name'),
                    ]),

                \Filament\Navigation\NavigationGroup::make('pages2')
                    ->label('Pages Group 2')
                    ->items([
                        \Filament\Navigation\NavigationItem::make('item21')
                            ->label('toto item')
                            ->icon('heroicon-o-users'),

                        \Filament\Navigation\NavigationItem::make('item22')
                            ->label('toto item 2')
                            ->icon('heroicon-o-pencil')
                            ->badge('76', 'success'),
                    ]),
            ]);

        return $builder->getNavigation();
    }*/