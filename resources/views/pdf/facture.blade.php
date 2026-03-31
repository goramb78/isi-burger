{{-- resources/views/pdf/facture.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Facture #{{ str_pad($commande->id, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #1d1d1d;
            background: #fff;
            padding: 40px;
        }

        /* ── En-tête ── */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #e63946;
        }
        .brand { font-size: 28px; font-weight: bold; color: #e63946; }
        .brand span { color: #1d1d1d; font-weight: 300; }
        .brand-sub { color: #888; font-size: 11px; margin-top: 4px; }

        .facture-info { text-align: right; }
        .facture-info h2 {
            font-size: 22px; color: #e63946;
            text-transform: uppercase; letter-spacing: 2px;
        }
        .facture-info p { color: #555; margin-top: 4px; font-size: 11px; }

        /* ── Parties ── */
        .parties {
            display: flex;
            justify-content: space-between;
            margin-bottom: 35px;
            gap: 20px;
        }
        .partie-box {
            flex: 1;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            border-left: 4px solid #e63946;
        }
        .partie-box h4 {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #888;
            margin-bottom: 8px;
        }
        .partie-box p { color: #333; line-height: 1.6; }
        .partie-box .name { font-weight: bold; font-size: 13px; }

        /* ── Tableau articles ── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        thead tr {
            background: #1d1d1d;
            color: #fff;
        }
        thead th {
            padding: 10px 12px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        thead th.right { text-align: right; }
        tbody tr { border-bottom: 1px solid #f0f0f0; }
        tbody tr:nth-child(even) { background: #fafafa; }
        tbody td { padding: 10px 12px; }
        tbody td.right { text-align: right; }
        tbody td.center { text-align: center; }

        /* ── Totaux ── */
        .totaux {
            width: 280px;
            margin-left: auto;
            margin-bottom: 30px;
        }
        .totaux table { margin: 0; }
        .totaux td { padding: 6px 10px; border: none; }
        .totaux tr.total-final {
            background: #e63946;
            color: #fff;
            font-weight: bold;
            font-size: 14px;
        }
        .totaux tr.total-final td { padding: 10px; border-radius: 4px; }

        /* ── Paiement ── */
        .paiement-box {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 30px;
            font-size: 11px;
        }
        .paiement-box.unpaid {
            background: #fff3cd;
            border-color: #ffc107;
        }

        /* ── Pied de page ── */
        .footer {
            text-align: center;
            color: #aaa;
            font-size: 10px;
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        /* ── Badge statut ── */
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-info    { background: #d1ecf1; color: #0c5460; }
    </style>
</head>
<body>

    {{-- ── En-tête ── --}}
    <div class="header">
        <div>
            <div class="brand">🍔 ISI<span>BURGER</span></div>
            <div class="brand-sub">
                Dakar, Sénégal<br>
                contact@isiburger.com | +221 77 000 00 00
            </div>
        </div>
        <div class="facture-info">
            <h2>Facture</h2>
            <p><strong>#{{ str_pad($commande->id, 4, '0', STR_PAD_LEFT) }}</strong></p>
            <p>Date : {{ $commande->created_at->format('d/m/Y') }}</p>
            @if($commande->paiement)
                <p>Payée le : {{ $commande->paiement->date_paiement->format('d/m/Y à H:i') }}</p>
            @endif
            <p style="margin-top:8px;">
                <span class="badge {{ $commande->statut === 'payee' ? 'badge-success' : 'badge-warning' }}">
                    {{ $commande->statut_label }}
                </span>
            </p>
        </div>
    </div>

    {{-- ── Parties ── --}}
    <div class="parties">
        <div class="partie-box">
            <h4>Émetteur</h4>
            <p>
                <span class="name">ISI BURGER Restaurant</span><br>
                Dakar, Sénégal<br>
                contact@isiburger.com<br>
                +221 77 000 00 00
            </p>
        </div>
        <div class="partie-box">
            <h4>Facturé à</h4>
            <p>
                <span class="name">{{ $commande->user->name }}</span><br>
                {{ $commande->user->email }}<br>
                Commande passée le {{ $commande->created_at->format('d/m/Y à H:i') }}
            </p>
        </div>
    </div>

    {{-- ── Tableau des articles ── --}}
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Catégorie</th>
                <th class="right">Prix unit.</th>
                <th class="right">Qté</th>
                <th class="right">Sous-total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($commande->items as $item)
            <tr>
                <td>
                    <strong>{{ $item->burger->nom }}</strong>
                    @if($item->burger->description)
                        <br><small style="color:#888">{{ Str::limit($item->burger->description, 60) }}</small>
                    @endif
                </td>
                <td>{{ $item->burger->category->nom ?? '—' }}</td>
                <td class="right">{{ number_format($item->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                <td class="center">{{ $item->quantite }}</td>
                <td class="right"><strong>{{ number_format($item->sous_total, 0, ',', ' ') }} FCFA</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ── Totaux ── --}}
    <div class="totaux">
        <table>
            <tr>
                <td>Sous-total HT</td>
                <td class="right">{{ number_format($commande->total, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td style="color:#888;">TVA (0%)</td>
                <td class="right" style="color:#888;">0 FCFA</td>
            </tr>
            <tr class="total-final">
                <td>TOTAL TTC</td>
                <td class="right">{{ number_format($commande->total, 0, ',', ' ') }} FCFA</td>
            </tr>
        </table>
    </div>

    {{-- ── Infos paiement ── --}}
    @if($commande->paiement)
    <div class="paiement-box">
        ✅ <strong>Paiement reçu</strong> —
        {{ number_format($commande->paiement->montant, 0, ',', ' ') }} FCFA en espèces
        le {{ $commande->paiement->date_paiement->format('d/m/Y à H:i') }}
    </div>
    @else
    <div class="paiement-box unpaid">
        ⏳ <strong>Paiement en attente</strong> —
        Montant dû : <strong>{{ number_format($commande->total, 0, ',', ' ') }} FCFA</strong>
        (règlement en espèces au comptoir)
    </div>
    @endif

    {{-- ── Pied de page ── --}}
    <div class="footer">
        <p>Merci de votre confiance — ISI BURGER 🍔</p>
        <p>Cette facture a été générée automatiquement le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

</body>
</html>
