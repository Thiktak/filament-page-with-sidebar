
# Filament Page With Sidebar

[![Latest Version on Packagist](https://img.shields.io/packagist/v/aymanalhattami/filament-page-with-sidebar.svg?style=flat-square)](https://packagist.org/packages/aymanalhattami/filament-page-with-sidebar)
[![Total Downloads](https://img.shields.io/packagist/dt/aymanalhattami/filament-page-with-sidebar.svg?style=flat-square)](https://packagist.org/packages/aymanalhattami/filament-page-with-sidebar)

Organize resource pages in the sidebar in order to make navigation between resource pages more comfortable.


## Screenshots
LTR (Left to Right)
![filament-page-with-sidebar](https://raw.githubusercontent.com/aymanalhattami/filament-page-with-sidebar/main/images/users-view-EN.png)

RTL (Right to Left)
![filament-page-with-sidebar](https://raw.githubusercontent.com/aymanalhattami/filament-page-with-sidebar/main/images/users-view-AR.png)

Please check out this video by Povilas Korop (Laravel Daily) to learn more about our package: [link](https://www.youtube.com/watch?v=J7dH8O-YBnY)

> **Note:**
> For [Filament 2.x](https://filamentphp.com/docs/2.x/admin/installation)  use [version 1.x](https://github.com/aymanalhattami/filament-page-with-sidebar/tree/1.x)

## Installation
```bash
composer require aymanalhattami/filament-page-with-sidebar
```

optionally you can publish config, views and components files
```bash
php artisan vendor:publish --tag="filament-page-with-sidebar-config"
php artisan vendor:publish --tag="filament-page-with-sidebar-views"
```
## Usage
1. First you need to prepare resource pages, for example, we have an edit page, view page, manage page, change password page, and dashboar page for UserResource
```php
use Filament\Resources\Resource;

class UserResource extends Resource 
{
    // ...

    public static function getPages(): array
    {
        return [
            'index' => App\Filament\Resources\UserResource\Pages\ListUsers::route('/'),
            'edit' => App\Filament\Resources\UserResource\Pages\EditUser::route('/{record}/edit'),
            'view' => App\Filament\Resources\UserResource\Pages\ViewUser::route('/{record}/view'),
            'manage' => App\Filament\Resources\UserResource\Pages\ManageUser::route('/{record}/manage'),
            'password.change' => App\Filament\Resources\UserResource\Pages\ChangePasswordUser::route('/{record}/password/change'),
            'dashboard' => App\Filament\Resources\UserResource\Pages\DashboardUser::route('/{record}/dashboard'),
            // ... more pages
        ];
    }

    // ...
}
```

2. Define a $record property in each custom page, example

```php
public ModelName $record; // public User $record;
```

3. Then, define the sidebar method as static in the resource
```php
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Resource;
use AymanAlhattami\FilamentPageWithSidebar\FilamentPageSidebar;
use AymanAlhattami\FilamentPageWithSidebar\PageNavigationItem;

class UserResource extends Resource 
{
    // ....

    public static function sidebar(Model $record): FilamentPageSidebar
    {
        return FilamentPageSidebar::make()
            ->setNavigationItems([
                PageNavigationItem::make('User Dashboard')
                    ->url(function () use ($record) {
                        return static::getUrl('dashboard', ['record' => $record->id]);
                    }),
                PageNavigationItem::make('View User')
                    ->url(function () use ($record) {
                        return static::getUrl('view', ['record' => $record->id]);
                    }),
                PageNavigationItem::make('Edit User')
                    ->url(function () use ($record) {
                        return static::getUrl('edit', ['record' => $record->id]);
                    }),
                PageNavigationItem::make('Manage User')
                    ->url(function () use ($record) {
                        return static::getUrl('manage', ['record' => $record->id]);
                    }),
                PageNavigationItem::make('Change Password')
                    ->url(function () use ($record) {
                        return static::getUrl('password.change', ['record' => $record->id]);
                    }),

                // ... more items
            ]);
    }

    // ....
}
```

4. Use x-filament-page-with-sidebar::page component in the page blade file as a wrapper for the whole content
```php
// filament.resources.user-resource.pages.change-password-user
<x-filament-page-with-sidebar::page>
    // ... page content
</x-filament-page-with-sidebar::page>

```

or add the trait ```AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar``` on any page you want the sidebar included.
This trait will add the sidebar to the Page. Add it to all your Resource Pages :

```php
// ...
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;

class ViewUser extends ViewRecord
{
    use HasPageSidebar; // use this trait to activate the Sidebar

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
```

If you wan to use custom view, you can still overwrite the default value with ```protected static string $hasSidebar = false;``` and ```protected static $view = 'filament.[...].user-resource.pages.view-user';```


## More Options

### Set title and description for sidebar
You can set the title or description by using setTitle and setDescription methods for the sidebar that will be at the beginning of the sidebar on the top, for example 
```php
// ...

public static function sidebar(Model $record): FilamentPageSidebar
{
    return FilamentPageSidebar::make()
        ->setTitle('Sidebar title')
        ->setDescription('Sidebar description')
        ->setNavigationItems([
            PageNavigationItem::make(__('User Dashboard'))
                ->url(function () use ($record) {
                    return static::getUrl('dashboard', ['record' => $record->id]);
                }),
            PageNavigationItem::make(__('View User'))
                ->url(function () use ($record) {
                    return static::getUrl('view', ['record' => $record->id]);
                }),

            // ... more items
        ]);
}

// ...
```

### Add icon
You can add an icon to the item by using the icon method, for example 
```php
// ...

public static function sidebar(Model $record): FilamentPageSidebar
{
    return FilamentPageSidebar::make()
        ->setNavigationItems([
            PageNavigationItem::make('Change Password')
                ->url(function () use ($record) {
                    return static::getUrl('password.change', ['record' => $record->id]);
                })->icon('heroicon-o-collection')

            // ... more items
        ]);
}

// ...
```

### Set active item
You can make an item active "has a different background color" by using isActiveWhen method, for example 
```php
// ...
public static function sidebar(Model $record): FilamentPageSidebar
{
    return FilamentPageSidebar::make()
        ->setNavigationItems([
            PageNavigationItem::make('Change Password')
                ->url(function () use ($record) {
                    return static::getUrl('password.change', ['record' => $record->id]);
                })
                ->isActiveWhen(function () {
                    return request()->route()->action['as'] == 'filament.resources.users.password.change';
                })
            // ... more items
        ]);
}
// ...
```

### Hide the item
You can hide an item from the sidebar by using isHiddenWhen method, for example 
```php
// ...

public static function sidebar(Model $record): FilamentPageSidebar
{
    return FilamentPageSidebar::make()
        ->setNavigationItems([
            PageNavigationItem::make('Change Password')
                ->url(function () use ($record) {
                    return static::getUrl('password.change', ['record' => $record->id]);
                })
                ->isHiddenWhen(false)
            // ... more items
        ]);
}
    ,
// ...
```

### Add bage to the item
You can add a badge to the item by using the badge method, for example 
```php
// ...
public static function sidebar(Model $record): FilamentPageSidebar
{
    return FilamentPageSidebar::make()
        ->setNavigationItems([
            PageNavigationItem::make('Change Password')
                ->url(function () use ($record) {
                    return static::getUrl('password.change', ['record' => $record->id]);
                })
                ->badge("badge name")
            // ... more items
        ]);
}
    ,
// ...
```

### Translate the item
You can translate a label by using translateLabel method, for example 
```php
// ...
public static function sidebar(Model $record): FilamentPageSidebar
{
    return FilamentPageSidebar::make()->translateLabel()
        ->setNavigationItems([
            PageNavigationItem::make('Change Password')
                ->url(function () use ($record) {
                    return static::getUrl('password.change', ['record' => $record->id]);
                })
            // ... more items
        ]);
}
    ,
// ...
```

[Demo Project Link](https://github.com/aymanalhattami/filament-page-with-sidebar-project)


# Version with Filament objets

```php
    public static function getSidebarSubNavigation(FilamentPageSidebar $builder)
    {
        return $builder
            ->items([
                \Filament\Navigation\NavigationItem::make('home')
                    ->label('Profile')
                    ->icon('heroicon-o-home')
                    ->activeIcon('heroicon-s-users')
                    ->url($urlPeople = '?activeTab=People')
                    ->isActiveWhen(fn () => request()->fullUrlIs('*' . $urlPeople . '*')),

                \Filament\Navigation\NavigationItem::make('home')
                    ->label('Feeds')
                    ->icon('heroicon-o-home')
                    ->badge('2')
                    ->activeIcon('heroicon-s-users')
                    ->url($urlPeople = '?activeTab=People0')
                    ->isActiveWhen(fn () => request()->fullUrlIs('*' . $urlPeople . '*')),

                \Filament\Navigation\NavigationItem::make('home')
                    ->label('News')
                    ->icon('heroicon-o-home')
                    ->activeIcon('heroicon-s-users')
                    ->url($urlPeople = '?activeTab=People2')
                    ->isActiveWhen(fn () => request()->fullUrlIs('*' . $urlPeople . '*')),

                \Filament\Navigation\NavigationItem::make('home')
                    ->label('Photos')
                    ->icon('heroicon-o-home')
                    ->activeIcon('heroicon-s-users')
                    ->url($urlPeople = '?activeTab=People3')
                    ->isActiveWhen(fn () => request()->fullUrlIs('*' . $urlPeople . '*')),
            ])
            ->groups([
                \Filament\Navigation\NavigationGroup::make('More')
                    ->items([
                        \Filament\Navigation\NavigationItem::make('More')
                            ->label('More !!')
                            ->icon('heroicon-o-home')
                            ->activeIcon('heroicon-s-users')
                            ->url($urlPeople = '?activeTab=People3')
                            ->isActiveWhen(fn () => request()->fullUrlIs('*' . $urlPeople . '*')),

                        \Filament\Navigation\NavigationItem::make('More2')
                            ->label('More !!')
                            ->icon('heroicon-o-home')
                            ->activeIcon('heroicon-s-users')
                            ->url($urlPeople = '?activeTab=People3')
                            ->isActiveWhen(fn () => request()->fullUrlIs('*' . $urlPeople . '*')),
                    ])
            ]);
    }

    public static function sidebar(Model $record): FilamentPageSidebar
    {
        return FilamentPageSidebar::make()

            ->setTitle('My title test') //$record)
            ->setDescription(fn () => __($record->kind . ' dashboard'))

            ->setNavigationItems([
                PageNavigationItem::make('Dashboard')
                    ->label(fn () => __($record->kind . ' dashboard'))
                    ->url(function () use ($record) {
                        return '#';
                    })
                    ->icon('heroicon-o-bookmark')
                    ->isActiveWhen(fn () => true),

                PageNavigationItem::make('Organization')
                    ->url(function () use ($record) {
                        return '#';
                    })
                    ->icon('heroicon-o-user-group'),

                PageNavigationItem::make('Matrix')
                    ->url(function () use ($record) {
                        return '#';
                    })
                    ->icon('heroicon-m-table-cells')
                    ->group('Matrix'),

                PageNavigationItem::make('Answers')
                    ->url(function () use ($record) {
                        return '#';
                    })
                    ->icon('heroicon-m-table-cells')
                    ->group('Matrix'),

                PageNavigationItem::make('History')
                    ->url(function () use ($record) {
                        return '#';
                    })
                    ->icon('heroicon-o-clock')
                    ->group('Matrix')
                    ->badge("badge name"),

                PageNavigationItem::make('Review')
                    ->url(function () use ($record) {
                        return '#'; //static::getUrl('dashboard', ['record' => $record->id]);
                    })
                    ->icon('heroicon-s-magnifying-glass-plus')
                    ->group('Actions'),

                /*PageNavigationBlock::make()
                    ->schema([
                        self::infolistComposantChecklist()
                            ->label(false)
                    ])
                    ->group('More')*/

                /*PageNavigationHTML::make()
                    ->setHtml('<hr class="my-2" />')*/
            ]);
    }
```
