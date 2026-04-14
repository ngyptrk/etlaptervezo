<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etrend osszefoglalo</title>
</head>
<body style="margin:0;padding:0;background:#0f1115;color:#f4d14a;font-family:Arial,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#0f1115;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="640" cellspacing="0" cellpadding="0" style="max-width:640px;background:#161a20;border:1px solid #f4d14a;border-radius:14px;padding:20px;">
                    <tr>
                        <td style="font-size:28px;font-weight:700;color:#f4d14a;padding-bottom:8px;">
                            Etlaptervezo
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:16px;color:#f5f5f5;padding-bottom:16px;">
                            Szia {{ $user->name }}, itt van az etrended osszefoglaloja.
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:14px;color:#e5e5e5;line-height:1.6;">
                            Az emailhez csatoltunk egy PDF fajlt is, benne:
                            <ul style="margin:8px 0 0 18px;padding:0;">
                                <li>napi receptekkel</li>
                                <li>referencia kepekkel</li>
                                <li>osszesitett bevasarlolistaval</li>
                            </ul>

                            @php($list = $shoppingList ?? collect())
                            @if($list->isNotEmpty())
                                <div style="margin-top:18px;font-size:15px;font-weight:700;color:#f4d14a;">
                                    Osszesitett bevasarlolista
                                </div>
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-top:10px;border-collapse:collapse;">
                                    <thead>
                                        <tr>
                                            <th align="left" style="padding:8px 10px;border:1px solid rgba(244, 209, 74, 0.35);color:#f5f5f5;font-size:12px;">Alapanyag</th>
                                            <th align="right" style="padding:8px 10px;border:1px solid rgba(244, 209, 74, 0.35);color:#f5f5f5;font-size:12px;">Mennyiseg</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($list as $item)
                                            <tr>
                                                <td style="padding:8px 10px;border:1px solid rgba(244, 209, 74, 0.2);color:#e5e5e5;font-size:12px;">
                                                    {{ $item['name'] ?? '-' }}
                                                </td>
                                                <td align="right" style="padding:8px 10px;border:1px solid rgba(244, 209, 74, 0.2);color:#e5e5e5;font-size:12px;">
                                                    {{ $item['amount'] ?? 0 }} {{ $item['unit'] ?? '' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top:18px;font-size:12px;color:#bbbbbb;">
                            Ez egy automatikus uzenet, kerlek ne valaszolj ra.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
