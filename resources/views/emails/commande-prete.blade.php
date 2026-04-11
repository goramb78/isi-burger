{{-- resources/views/emails/commande-prete.blade.php --}}
<x-mail::message>
# 🍔 Votre commande est prête !

Bonjour **{{ $commande->user->name }}**,

Bonne nouvelle ! Votre commande **#{{ str_pad($commande->id, 4, '0', STR_PAD_LEFT) }}**
est **prête** et vous attend.

---

##  Facture

Votre facture est jointe en pièce jointe à cet email (PDF).

<x-mail::table>
| Burger | Qté | Prix unitaire | Sous-total |
|:-------|:---:|:---:|---:|
@foreach($commande->items as $item)
| {{ $item->burger->nom }} | {{ $item->quantite }} | {{ number_format($item->prix_unitaire, 0, ',', ' ') }} FCFA | {{ number_format($item->sous_total, 0, ',', ' ') }} FCFA |
@endforeach
</x-mail::table>

<x-mail::panel>
** Montant total à payer : {{ number_format($commande->total, 0, ',', ' ') }} FCFA**

Mode de paiement : Espèces (au comptoir)
</x-mail::panel>

---

<x-mail::button :url="route('client.commandes.show', $commande)" color="red">
Voir ma commande
</x-mail::button>

À très bientôt,<br>
**L'équipe ISI BURGER** 🍔
</x-mail::message>
