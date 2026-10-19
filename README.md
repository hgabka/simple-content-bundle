# hgabka/simple-content-bundle

Egyszerű, névvel azonosított, nyelvenként szerkeszthető szöveges (HTML) tartalmak kezelése Sonata Adminból, és megjelenítésük Twigből. Tipikus felhasználás: kisebb statikus szövegrészek, láblécek, üzenetek, amiket a szerkesztők a CMS-ben módosítanak, anélkül hogy külön oldalt kellene készíteni nekik.

## Telepítés

```bash
composer require hgabka/simple-content-bundle
```

```php
// config/bundles.php
Hgabka\SimpleContentBundle\HgabkaSimpleContentBundle::class => ['all' => true],
```

Függőségek: Symfony 7/8, `hgabka/utils-bundle`, `hgabka/doctrine-translatable-bundle`, Sonata Admin (az admin felülethez). Az entitások (`hg_simple_content`, `hg_simple_content_translation`) táblái Doctrine migrációval/schema update-tel hozhatók létre.

## Konfiguráció

```yaml
# config/packages/hgabka_simple_content.yaml
hgabka_simple_content:
    editor_mode: ~    # a WYSIWYG mező "type" attribútuma (pl. egyszerűsített szerkesztőhöz)
```

## Használat

### Twig

```twig
{{ render_simple_content('footer_text') }}
{{ render_simple_content('welcome', {'%name%': user.name}, 'en') }}
```

Paraméterek: a tartalom neve, opcionális behelyettesítő tömb (`strtr`-rel kerül alkalmazásra), opcionális nyelv (alapból az aktuális). Ha a tartalom nem létezik, üres stringet ad vissza.

### PHP

A `Hgabka\SimpleContentBundle\Helper\SimpleContentManager` szolgáltatás (`getContent($name, $params = [], $locale = null)`) ugyanezt végzi. Az értékek `FilesystemAdapter` cache-ben (`<cache_dir>/simplecontent`) tárolódnak nyelvenként; az admin mentéskor/törléskor frissíti, ill. törli a megfelelő cache-elemet (`addContentToCache`, `removeFromCache`, `clearCache`, `regenerateCache`).

### Admin

A `SimpleContentAdmin` Sonata admin (csoport: `hg_simple_content.group`) listázza és szerkeszti a tartalmakat:

- minden szerkesztő látja a `description` és `value` (WYSIWYG, CKEditor) mezőket nyelvenként;
- `ROLE_SUPER_ADMIN` esetén ezenfelül szerkeszthető a `name` (az azonosító, egyedi), a `css`, a `cssFiles` (a szerkesztő tartalmához betöltött stíluslapok) és a `width` (a szerkesztő szélessége, > 50);
- a lista kereshető névre és leírásra; extra route: `downloadCss` (a tartalom CSS-ének letöltése).

## Entitások

- `SimpleContent` – `name`, `css`, `cssFiles`, `width`, időbélyegek (`TimestampableEntity`), fordítható (`TranslatableTrait`)
- `SimpleContentTranslation` – `description`, `value`

Fordítások: `Resources/translations` (`hu`, `en`).
