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

10 tesztfájl sikeres

34 teszt sikeres

```

### Tesztfajták

## 1. View tesztek

Fájl: `client/src/__tests__/MealRequirementView.spec.js`

Ez a teszt ellenőrzi, hogy a `MealRequirementView.vue`:

- Helyesen betölti-e az adatokat a szolgáltatás rétegből,

- Megjeleníti-e az étkezés elvárásokat,

- Lehetővé teszi-e a keresést,

- Megnyitja-e a törlési megerősítő ablakot,

- Törlésnél meghívja-e a megfelelő szolgáltatás metódust,

- Validációs hiba esetén átadja-e a hibákat a form komponensnek.

## 2. Form/komponens tesztek

Fájlok:

- `client/src/__tests__/FormMealRequirement.spec.js`

- `client/src/__tests__/FormRecipe.spec.js`

- `client/src/__tests__/PasswordField.spec.js`

- `client/src/__tests__/ConfirmModal.spec.js`

Ezek a tesztek ellenőrzik:

- A mezők és select opciók megjelenítését,

- A form adatok módosítását,

- A mentési események kibocsátását,

- A szerver oldali validációs hibák megjelenítését,

- A jelszó láthatóságának ki- és bekapcsolását,

- A megerősítő modal `confirm` és `cancel` eseményeit.

## 3. Store/unit tesztek

Fájl: `client/src/__tests__/stores.spec.js`

Ezek a tesztek a Pinia store-okat ellenőrzik:

- Globális betöltési állapot,

- Kereső szó tárolása és kisbetűsített getter,

- Toast üzenetek automatikus törlése,

- Bejelentkezési állapot és jogosultságellenőrzés.

## 4. API/szolgáltatás tesztek

Fájl: `client/src/__tests__/apiServices.spec.js`

Ezek mockolt axios klienssel ellenőrzik:

- A megfelelő API endpointok hívását,

- A megfelelő HTTP metódusokat,

- Az `id` mező eltávolítását create/update payloadból,

- A recept képfeltöltéshez szükséges `multipart/form-data` beállítást.

## 5. Router jogosultság tesztek

Fájl: `client/src/__tests__/routerGuards.spec.js`

Ezek a tesztek ellenőrzik:

- Vendég felhasználó védett oldalról login oldalra kerül,

- Admin felhasználó eléri az admin oldalt,

- Nem megfelelő jogosultságú bejelentkezett felhasználó a főoldalra kerül és hiba toastot kap.

## 6. UI állapot tesztek

Fájl: `client/src/__tests__/AppUiState.spec.js`

Ezek a tesztek ellenőrzik:

- A mobil oldalsó menü nyitását és zárását,

- A globális betöltési overlay megjelenését,

- A toast üzenetek megjelenítését és bezárását.
