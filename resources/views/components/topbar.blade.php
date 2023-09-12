@props(['navigation'])
@props(['hasTopNavigation' => true])
@props(['isSidebarFullyCollapsibleOnDesktop' => true])
@props(['isSidebarCollapsibleOnDesktop' => true])
@props(['hasNavigation' => true, 'hasTenancy' => false])
@props(['isMainTopbar' => false])
@props(['title' => null, 'description' => null])

<div {{ $attributes->class(['fi-topbar sticky top-0 z-20 overflow-x-clip']) }}>
    <nav @class([
        'flex items-center gap-x-4',
        'mb-4' => !$isMainTopbar,
        'h-16 md:px-6 lg:px-8' => $isMainTopbar,
        'px-4 py-2 rounded bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10',
    ]) class=" ">
        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::topbar.start') }}

        <x-filament::icon-button color="gray" icon="heroicon-o-bars-3" icon-alias="panels::topbar.open-sidebar-button"
            icon-size="lg" :label="__('filament-panels::layout.actions.sidebar.expand.label')" x-cloak x-data="{}" x-on:click="$store.sidebar.open()"
            x-show="! $store.sidebar.isOpen" @class([
                '-ms-1.5',
                'lg:hidden' =>
                    !$isSidebarFullyCollapsibleOnDesktop || $isSidebarCollapsibleOnDesktop,
            ]) />

        <x-filament::icon-button color="gray" icon="heroicon-o-x-mark" icon-alias="panels::topbar.close-sidebar-button"
            icon-size="lg" :label="__('filament-panels::layout.actions.sidebar.collapse.label')" x-cloak x-data="{}" x-on:click="$store.sidebar.close()"
            x-show="$store.sidebar.isOpen" class="-ms-1.5 lg:hidden" />

        @if ($hasTopNavigation)
            @if ($isMainTopbar)
                <div class="me-6 hidden lg:flex">
                    @if ($homeUrl = filament()->getHomeUrl())
                        <a href="{{ $homeUrl }}" {{-- wire:navigate --}}>
                            <x-filament-panels::logo />
                        </a>
                    @else
                        <x-filament-panels::logo />
                    @endif
                </div>

                @if ($hasTenancy)
                    <x-filament-panels::tenant-menu class="hidden lg:block" />
                @endif
            @elseif($title || $description)
                <div class="me-6 hidden lg:flex">
                    <div>
                        <h3 class="text-base font-medium text-slate-700 dark:text-navy-100 truncate block">
                            {{ $title }}
                        </h3>
                        <p class="text-xs text-gray-500 truncate block">{{ $description }}</p>
                    </div>
                </div>
            @endif

            @if ($hasNavigation)
                <ul class="me-4 hidden items-center gap-x-4 lg:flex">
                    @foreach ($navigation as $group)
                        @if ($groupLabel = $group->getLabel())
                            <x-filament::dropdown placement="bottom-start" teleport>
                                <x-slot name="trigger">
                                    <x-filament-panels::topbar.item :active="$group->isActive()" :icon="$group->getIcon()">
                                        {{ $groupLabel }}
                                    </x-filament-panels::topbar.item>
                                </x-slot>

                                <x-filament::dropdown.list>
                                    @foreach ($group->getItems() as $item)
                                        @php
                                            $icon = $item->getIcon();
                                            $shouldOpenUrlInNewTab = $item->shouldOpenUrlInNewTab();
                                        @endphp

                                        <x-filament::dropdown.list.item :badge="$item->getBadge()" :badge-color="$item->getBadgeColor()"
                                            :href="$item->getUrl()" :icon="$item->isActive() ? $item->getActiveIcon() ?? $icon : $icon" tag="a" :target="$shouldOpenUrlInNewTab ? '_blank' : null"
                                            {{-- :wire:navigate="$shouldOpenUrlInNewTab ? null : true" --}}>
                                            {{ $item->getLabel() }}
                                        </x-filament::dropdown.list.item>
                                    @endforeach
                                </x-filament::dropdown.list>
                            </x-filament::dropdown>
                        @else
                            @foreach ($group->getItems() as $item)
                                <x-filament-panels::topbar.item :active="$item->isActive()" :active-icon="$item->getActiveIcon()" :badge="$item->getBadge()"
                                    :badge-color="$item->getBadgeColor()" :icon="$item->getIcon()" :should-open-url-in-new-tab="$item->shouldOpenUrlInNewTab()" :url="$item->getUrl()">
                                    {{ $item->getLabel() }}
                                </x-filament-panels::topbar.item>
                            @endforeach
                        @endif
                    @endforeach
                </ul>
            @endif
        @endif

        @if ($isMainTopbar)
            <div {{-- x-persist="topbar.end" --}} class="ms-auto flex items-center gap-x-4">
                {{ \Filament\Support\Facades\FilamentView::renderHook('panels::global-search.before') }}

                @if (filament()->isGlobalSearchEnabled())
                    @livewire(Filament\Livewire\GlobalSearch::class, ['lazy' => true])
                @endif

                {{ \Filament\Support\Facades\FilamentView::renderHook('panels::global-search.after') }}

                @if (filament()->hasDatabaseNotifications())
                    @livewire(Filament\Livewire\DatabaseNotifications::class, ['lazy' => true])
                @endif

                <x-filament-panels::user-menu />
            </div>
        @endif

        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::topbar.end') }}
    </nav>
</div>
