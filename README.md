# Futtatási környezet
## Szükséges szoftverek
A fejlesztési környezet szükséges szoftvere/(i):
Visual Studio Code a szoftver fejlesztéséhez.
[VSC letöltő link](https://code.visualstudio.com/)

dbForge Studio az adatbázis ellenörzéshez.
[dbForge letöltő link](https://www.devart.com/dbforge/mysql/studio/download.html)

xampp / wampp a szerver futtatáshoz.
[xampp letöltő link](https://www.apachefriends.org/)

Git verzíózáshoz.
Alapértelmezett a Visual Studio Codeban.

A composer a PHP csomagkezelője, és a Laravel egyik alap eszköze.
- Telepítés és használat:
    #### 1. Laravel projekt létrehozása
  ```bash
    composer create-project laravel/laravel projektnev
  ```
  - Ez letölti a Laravel teljes vázát és minden szükséges csomagot.

    #### 2. Függőségek telepítése
  ```bash
    composer install
  ```
  - Ez a composer.json alapján telepíti a szükséges csomagokat.
  - Ezek az alapok, hogy működjön a laravel compoeser.
  


## Projekt letöltése 
GitHub link:
[Étlaptervező szoftver](https://github.com/kovacsnandor/School_2026)  

## Projekt futtatása
- 1. Elindítjuk a xampp/wampp-ot.
- 2. Felmegyünk a szoftver GitHub linkjére. 
- 3. Le clone-ozzuk az fájlt.
- 4. Lefuttatjuk a "composer install" parancsot.
- 5. Miután lefutott, el kell indítani a szervert: "cd server; php artisan serve".
- 6. A szerver futása után létre kell hoznunk az adatbázist(Az adatbázis létrehozásához az alábbi parancs elég, mert a rendszer észleli, ha nincs még adatbázis és kérelmezi a létrehozást): "php artisan migrate"
- 7. Ezek után feltöltjük adatokkal: "php artisan db:seed"
- 8. A Frontend kezeléséhez az alábbi parancsot kell futtatnunk: "npm install". Ez a Vue-t telepíti nekünk.
- 9. Ha a telepítés megtörtént az "npm run dev" paranccsal el tudjuk indítani a Frontend rendszerünket.
- 10. Ha a Frontendünk elindult "o" betűt írunk a konzolra majd "enter" gombot ütünk.
- 11. Teszteléshez/használathoz jelentkezzen be a meglévő felhasználók egyikével!