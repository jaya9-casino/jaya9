# Jaya9 Satellite - WordPress-тема

Классическая PHP-тема по макету jaya9-bangladesh (index.html + style.css). Весь контент редактируется через ACF Pro, без кода.

## Требования

- WordPress 6.4+, PHP 8.1+
- ACF Pro 6.x (Flexible Content, Repeater, Options Page) - обязательно
- Yoast SEO - Title, Description, canonical, базовая schema
- W3 Total Cache - по желанию
- Polylang - когда понадобится BN-версия

## Установка

1. Appearance -> Themes -> Add New -> Upload Theme -> `jaya9.zip` -> Activate.
2. Установить и активировать ACF Pro и Yoast SEO.
3. Appearance -> Jaya9 Demo Import -> Run import. Создаются 11 страниц, три меню, загружаются логотип и обложка, главная заполняется текстом макета и назначается главной страницей. Существующие страницы не перезаписываются.
4. Theme Settings -> General -> Referral URL - куда ведёт `/go/`.
5. Settings -> Permalinks -> Post name.

Поля ACF лежат в `acf-json/`. Если ACF покажет «Sync available», нажмите Sync в ACF -> Field Groups (поля работают и без этого).

## Где что редактировать

| Что | Где |
| --- | --- |
| Логотип, реф-ссылка, кнопки хедера, бейдж | Theme Settings -> General |
| Тексты баннеров по умолчанию | Theme Settings -> Banners |
| Текст и колонки футера, копирайт | Theme Settings -> Footer |
| Дисклеймер 18+ | Theme Settings -> Responsible Gaming |
| Меню хедера и футера | Appearance -> Menus (Header Menu, Footer Menu 1, Footer Menu 2) |
| Hero, оглавление, блоки страницы | Редактор страницы -> Page Builder |
| Title, Description | Yoast-блок под страницей |

## Добавить страницу

Pages -> Add New -> заголовок -> Page Builder:

- Hero: тип Full (с обложкой) или Simple (одна колонка, для EEAT-страниц).
- Content -> Add Block: Heading, Text, Table, List, Callout, Chips, Platform cards, Banner, FAQ, Image.
- Каждый Heading уровня H2 начинает раздел и попадает в оглавление.
- Таблицы: ячейки через `|`, например `Promotion | Bonus | Maximum`.

## /go/

`/go/` отдаёт 302 на Referral URL с `X-Robots-Tag: noindex, nofollow` и без кеша. В robots.txt добавляется `Disallow: /go/`. Все кнопки и ссылки на `/go/` получают `rel="noopener nofollow"`. Если Referral URL пустой, `/go/` ведёт на главную.

## W3 Total Cache

- Performance -> Page Cache -> Advanced -> Never cache the following pages: `/go/` (тема и так запрещает кеш на `/go/`).
- Lazy Load: добавить `no-lazy` в исключения (обложка Hero).
- Удаление неиспользуемого CSS не включать или исключить `style.css`.
- После сохранения Theme Settings и меню тема сама сбрасывает кеш.

## BN-версия (Polylang)

1. Установить Polylang, добавить English (по умолчанию) и Bengali.
2. Появится Theme Settings (BN). Пустые поля берутся из английских настроек.
3. Перевести страницы в Polylang и заполнить их Page Builder.
4. Назначить меню для BN в Appearance -> Menus.
5. Перевести строки темы: Languages -> Translations, группа Jaya9.
6. Не включать синхронизацию custom fields в Languages -> Settings -> Synchronization.

hreflang и lang при включённом Polylang выводит Polylang, тема свои отключает. `/bn/go/` берёт реф-ссылку из BN-настроек.

## Шрифты

Readex Pro (400-700) и знак ৳ из Noto Sans Bengali лежат в `assets/fonts` (OFL), Google Fonts не подключается.
