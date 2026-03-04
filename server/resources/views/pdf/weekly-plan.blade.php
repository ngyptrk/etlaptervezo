<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Heti étrend</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1c1c1c; font-size: 12px; }
        h1, h2, h3 { margin: 0 0 8px 0; }
        .header { background: #101216; color: #f4d14a; padding: 10px 12px; border-radius: 6px; margin-bottom: 12px; }
        .day-block { margin-bottom: 14px; border: 1px solid #d3b640; border-radius: 6px; overflow: hidden; }
        .day-title { background: #1a1d24; color: #f4d14a; padding: 8px 10px; font-weight: 700; }
        .meal { padding: 8px 10px; border-top: 1px solid #ececec; }
        .meal-title { font-weight: 700; margin-bottom: 4px; }
        .recipe-image { width: 120px; height: 90px; object-fit: cover; border: 1px solid #d0d0d0; border-radius: 4px; margin-top: 4px; }
        .ingredients-title { margin-top: 6px; font-weight: 700; }
        .ingredients-list { margin: 2px 0 0 14px; padding: 0; }
        .ingredients-list li { margin: 1px 0; }
        .shopping { margin-top: 18px; border: 1px solid #d3b640; border-radius: 6px; overflow: hidden; }
        .shopping th { background: #1a1d24; color: #f4d14a; text-align: left; padding: 8px; }
        .shopping td { padding: 8px; border-top: 1px solid #ececec; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="font-size:22px;">Étlaptervező - Étrend összefoglaló</h1>
        <div>Felhasználó: {{ $user->name }} ({{ $user->email }})</div>
        @if($selectedWeek > 0)
            <div>Kiválasztott hét: {{ $selectedWeek }}.</div>
        @else
            <div>Minden generált hét</div>
        @endif
    </div>

    @php
        $grouped = $rows->groupBy(function($row) {
            return ($row->plan_week ?? 1) . '|' . ($row->weekday?->day ?? 'Ismeretlen nap');
        });
    @endphp

    @foreach($grouped as $groupKey => $items)
        @php
            [$week, $dayName] = explode('|', $groupKey, 2);
        @endphp
        <div class="day-block">
            <div class="day-title">{{ $week }}. hét - {{ $dayName }}</div>
            @foreach($items as $item)
                @php
                    $picture = $item->recipe?->picture ?? '';
                    $imgUrl = '';
                    if ($picture) {
                        $imgUrl = str_starts_with($picture, 'http://') || str_starts_with($picture, 'https://')
                            ? $picture
                            : rtrim($appUrl, '/') . '/' . ltrim($picture, '/');
                    }
                @endphp
                <div class="meal">
                    <div class="meal-title">
                        {{ $item->mealRequirement?->mealOfDay?->meal_of_day ?? 'Étkezés' }}
                        -
                        {{ $item->mealRequirement?->meal?->meal ?? 'Típus' }}
                    </div>
                    <div><strong>Recept:</strong> {{ $item->recipe?->name ?? 'Nincs recept' }}</div>
                    <div>{{ $item->recipe?->description ?? '' }}</div>

                    <div class="ingredients-title">Hozzávalók:</div>
                    @if(($item->recipe?->ingredients?->count() ?? 0) > 0)
                        <ul class="ingredients-list">
                            @foreach($item->recipe->ingredients as $ingredient)
                                <li>
                                    {{ (int) ($ingredient->amount ?? 0) }}
                                    {{ $ingredient->unit?->unit ?? '' }}
                                    -
                                    {{ $ingredient->rawIngredient?->raw_ingredient ?? 'Ismeretlen hozzávaló' }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div>Nincs hozzávaló megadva ehhez a recepthez.</div>
                    @endif

                    @if($imgUrl)
                        <div><img src="{{ $imgUrl }}" class="recipe-image" alt="Recept kép"></div>
                    @endif
                </div>
            @endforeach
        </div>
    @endforeach

    <table class="shopping" width="100%" cellspacing="0" cellpadding="0">
        <thead>
            <tr>
                <th>Bevásárló lista</th>
                <th style="width:140px;">Mennyiség</th>
            </tr>
        </thead>
        <tbody>
            @forelse($shoppingList as $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ $item['amount'] }} {{ $item['unit'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">Nem található hozzávaló.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
