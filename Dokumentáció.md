# Étlap tervező
- Github link: https://github.com/ngyptrk/etlaptervezo
- Fejlesztők: Suki Zsolt, Nagy Patrik
- Csoportvezető: Nagy Patrik

## A szoftver célja:
- Egyedi étlap/recept ajánlás napokra/hetekre.
- A program képes választani az adatbázisban lévő receptekből és összeállítani napokra, vagy akár hetekre étrendet, melynek alapanyagait a bevásárláshoz leírja, és napra felosztja, hogy mit kell megvenni. 



## Feladat felosztas:
### S. Zsolt: Dokumentáció, Frontend fejlesztése
### N. Patrik: Backend fejlesztése, Frontend fejlesztése, Dokumentáció, Projekt repo kezelés


## A szoftver használata:
- A program indítása után a felhasználó kiválaszthatja, hogy hány napra vagy hétre szeretne étrendet összeállítani. A szoftver ezután az adatbázisban tárolt receptek közül automatikusan kiválasztja a megfelelő fogásokat, és napokra lebontva elkészíti az étrendet.

A felhasználó lehetőséget kap arra, hogy:

- Megtekintse az egyes napokra ajánlott ételeket,

- Kedvenc receptjeit bejelölje,

- Email alapon kézhez kapja a hozzávalókat.

Későbbiekben:

- Hozzávaló szűrés,

- Saját recept hozzáadása,

- Segítség kérés online felületen más felhasználoktól.

Továbbá a felhasználó saját recepteket is generálhat, ami az adatbázisba is bele kerül.

# Fejlesztői környezet: Visual Studio Code
## Adatbázis:
    - MySQL: Az adatok tárolása

    Táblák: összesen 10 tábla
    - User: Ez a tábla tartalmazza a felhasználót, admint, látogatót
    - Ingredients: Ez a tábla tartalmazza a hozzávalók id, mennyiségét, egységét
    - Raw_ingredients_id: Ez a tábla tartalmazza a nyers hozzávalókat
    - Units: Ez a tábla tartalmazza a hozzávalók egységét.
    - Days: Ez a tábla tartalmazza a napi ajánlatoknak a paramétereit.
    - Recipes: Ez a tábla tartalmazza a recepteket, ételeket
    - Meals: Ez a tábla tartalmazza a napi fogásokat.
    - Meals_of_day: Ez a tábla tartalmazza a reggeli, ebéd, vacsora id-t.
    - Meals_requirements: Az ételek követelménye
    - WeekDays: A hét napjait tartalmazza



## Backend: 
        - PHP, a szerveroldali logika megvalósítására szolgáló programozási nyelv. A backend felelős a receptek generálásáért, illetve a kezeléséért, valamint az adatbázissal való kommunikációért.
        - Laravel telepítéséhez: composer create-project laravel/laravel projekt_neve
        - Laravel parancsok:
          - php artisan migrate
          - php artisan db:seed
          - php artisan migrate:fresh
          - php artisan migrate:refresh
  
### Migráció
#### Days tábla migráció
 ```php
    public function up(): void
    {
        Schema::create('days', function (Blueprint $table) {
            $table->id();

            $table->foreignId('weekday_id')
                ->constrained('weekdays')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('recipe_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('meal_requirement_id')
                ->constrained('meal_requirements')
                ->cascadeOnDelete();

            $table->timestamps();

            // Egyedi kombináció
            $table->unique(
                ['user_id', 'weekday_id', 'meal_requirement_id'],
                'days_user_weekday_mealreq_unique'
            );
        });
    }
```
- A legfontosabb táblánk a Days tábla, ez a szive a szoftvernek.
  
### Seeder
 - Adataink nagy részét CSV fájlokból olvassuk be.
```php
   public static function csvToArray(string $fileName, string $delimiter = ';'): array
    {
        $filePath = database_path(path: $fileName);
        $data = [];

        if (!File::exists($filePath)) {
            return $data;
        }

        if (($handle = fopen($filePath, 'r')) !== false) {
            $header = fgetcsv($handle, 0, $delimiter);

            while (($cols = fgetcsv($handle, 0, $delimiter)) !== false) {
                if ($header && count($header) === count($cols)) {
                    $data[] = array_combine($header, $cols);
                }
            }
            
            fclose($handle);
        }

        return $data;
    }
```
### Seeder működése
- A seederünk egy alap letisztúlt kódot használ:
```php
     public function run(): void
    {
        //
        $sql = "INSERT INTO `meal_of_days` (`meal_of_day`) VALUES
        ('Reggeli'),
        ('Ebéd'),
        ('Vacsora')
        ";
        DB::statement($sql);
    }
```

### Endpointok
-  A védett tartalmakat egy user rang rendszerrel védjük le. Melyet a routes/api.php ban kezelünk.
```php
     Route::post('days', [DayController::class, 'store'])
    ->middleware(['auth:sanctum', 'ability:days:post']);
```
### Autentikáció
- A be és kijelentkezés egyszerűen történik, a saját token megadásával amit kedvünk szerint állítunk be, lejárati időpontra.

## Frontend
#### Használt modulok:
    - HTML: az alkalmazás felépítésének és szerkezetének kialakítására.
    - CSS: a felhasználói felület megjelenésének és stílusának kialakítására.
    - VueJS: a frontend logika megvalósítására szolgáló keretrendszer.
    - Bootstrap: a dizájn elemek kialakítására szolgáló rendszer.
    - JavaScript: a felhasználói interakciók és a dinamikus működés megvalósításáért felelős.
  
#### Oldal szerkezet
- Belépési pont: App.vue, main.js
- A headerben található az oldal neve, és a jelenlegi fiók neve és szerepköre.
- A menü magába foglalja az egesz oldal dizájnját, amit egy oldalsó navbarral díszítettünk, a könnyű tájékozódásért.
  
#### Jogosultsági rendszer kezelése
- Backend
  - Laravel Sanctum token‑alapú auth megy: bejelentkezéskor token készül role‑függő abilities listával.
  - Policy‑alapú védelem a felhasználó saját adatainál: saját profil nézhető/módosítható, admin sem módosíthatja saját role‑ját; admin nem törölhet admint.
  
- Menü
  - A menü elemek csak akkor jelennek meg, ha a route meta.roles alapján a felhasználó eléri az adott route‑ot (hasMenuAccessByName + canAccess)
  
- Route
  - Frontend oldalon globális route guard ellenőrzi a meta.roles értéket, és ha nincs jogosultság, loginra dob vagy hiba toastot ad.

#### Program szerkezet
- A receptek kiírásához kártyákat használunk, melyet nagy figyelemmel formáztunk.
- Az oldalunk valódi SMTP Email küldést használ, mely a WeeklyFoodGeneratorController.php ban történik meg.
- Figyeltünk a megfelelő elosztásra, mindennek meglegyen a saját űrlapja és ne legyen zsúfolt tér, ezt követően a validálást (szerepkör hozzáférést) a client/src/router/index.js-ben történik.
- Komponenseink a könyvtárban újrafelhasználható UI elemek vannak. Melyek egységes szép működést kínál a használónak.
- A dizájn bootstrap, css segítségével készűlt. Ezért az oldal reszponzívan működik.

### Források
[Deep Ai link](https://deepai.org/chat/ai-code)
[W3Schools link](https://www.w3schools.com/)
[Bootstrap link](https://getbootstrap.com/docs/5.3/getting-started/introduction/)
[ChatGPT link](https://chatgpt.com/)
[Unsplash link](https://unsplash.com/s/photos/food)
Google alapú kép keresés
Iskolában tanult anyagog használata

## Verziókezelés:
    - GitHub


## Tesztek:
A Teszteket HTML Fileba illesztettük. Hibátlanúl lefutott minden teszt.

