# Tesztek

## Kézi tesztelés (request.rest)
A kézi tesztelést a `server/request.rest` fájlban végeztük. Itt találhatók a bejelentkezés/kijelentkezés kérései, valamint több CRUD példa (users, units, days, recipes, meals, ingredients, meal_requirements, mealofdays, rawingredients, weekdays).

## Pingelés
A pingeléshez egyszerű GET kérés használható a backend tesztelő végpontra: `GET /api/x`.

## Frontend automata tesztek (Vitest)
A frontend automata tesztek a `client/src/__tests__` mappában találhatók. A tesztek Vitesttel és Vue Test Utilsszal futnak.

### Futtatás
A frontend mappába kell belépni:

```powershell
cd client
```

Az összes unit és komponens teszt egyszeri lefuttatása:

```powershell
npm run test:unit -- --run
```

Figyelő mód indítása, ahol a tesztek fájlváltozás után újrafutnak:

```powershell
npm run test:unit
```

Egy konkrét tesztfájl futtatása:

```powershell
npm run test:unit -- --run src/__tests__/MealRequirementView.spec.js
```

Build ellenőrzés:

```powershell
npm run build
```

### Jelenlegi eredmény
A tesztcsomag jelenlegi állapota:

```text
10 test file passed
34 test passed
```

### Tesztfajták
1. View tesztek

   Fajl: `client/src/__tests__/MealRequirementView.spec.js`

   Ez a teszt ellenőrzi, hogy a `MealRequirementView.vue`:
   - betölti-e az adatokat a service rétegből,
   - megjeleníti-e az étkezés elvárásokat,
   - működtetni tudja-e a keresést,
   - megnyitja-e a törlési megerősítő ablakot,
   - törlésnél meghívja-e a megfelelő service metódust,
   - validációs hiba esetén átadja-e a hibákat a form komponensnek.

2. Form/komponens tesztek

   Fajlok:
   - `client/src/__tests__/FormMealRequirement.spec.js`
   - `client/src/__tests__/FormRecipe.spec.js`
   - `client/src/__tests__/PasswordField.spec.js`
   - `client/src/__tests__/ConfirmModal.spec.js`

   Ezek a tesztek ellenőrzik:
   - a mezők és select opciók megjelenítését,
   - a form adatok módosítását,
   - a mentési események kibocsátását,
   - a szerver oldali validációs hibák megjelenítését,
   - a jelszó láthatóságának ki- és bekapcsolását,
   - a megerősítő modal `confirm` és `cancel` eseményeit.

3. Store/unit tesztek

   Fajl: `client/src/__tests__/stores.spec.js`

   Ezek a tesztek Pinia store-okat ellenőriznek:
   - globális betöltési állapot,
   - kereső szó tárolása és kisbetűsített getter,
   - toast üzenetek automatikus törlése,
   - bejelentkezési állapot és jogosultságellenőrzés.

4. API/service tesztek

   Fajl: `client/src/__tests__/apiServices.spec.js`

   Ezek mockolt axios klienssel ellenőrzik:
   - a megfelelő API endpointok hívását,
   - a megfelelő HTTP metódusokat,
   - az `id` mező eltávolítását create/update payloadból,
   - a recept képfeltöltéshez szükséges `multipart/form-data` beállítást.

5. Router jogosultság tesztek

   Fajl: `client/src/__tests__/routerGuards.spec.js`

   Ezek a tesztek ellenőrzik:
   - vendég felhasználó védett oldalról login oldalra kerül,
   - admin felhasználó eléri az admin oldalt,
   - nem megfelelő jogosultságú bejelentkezett felhasználó a főoldalra kerül és hiba toastot kap.

6. UI állapot tesztek

   Fajl: `client/src/__tests__/AppUiState.spec.js`

   Ezek a tesztek ellenőrzik:
   - a mobil oldalsó menü nyitását és zárását,
   - a globális betöltési overlay megjelenését,
   - a toast üzenetek megjelenítését és bezárását.

### Megjegyzés
A projektben a `jsdom` verziója `26.1.0`, mert a jelenlegi Node verzió `20.17.0`. Az újabb Vite verzió figyelmeztethet, hogy Node `20.19+` vagy `22.12+` ajánlott.
