{{-- resources/views/emails/confirmation.blade.php --}}
<x-mail::message>
# ✅ Commande confirmée !

Bonjour **{{ $commande->user->name }}**,

Votre commande **#{{ str_pad($commande->id, 4, '0', STR_PAD_LEFT) }}** a bien été reçue et est en cours de préparation.

---

## 🛒 Récapitulatif

<x-mail::table>
| Burger | Qté | Prix |
|:-------|:---:|-----:|
@foreach($commande->items as $item)
| {{ $item->burger->nom }} | {{ $item->quantite }} | {{ number_format($item->sous_total, 0, ',', ' ') }} FCFA |
@endforeach
| **TOTAL** | | **{{ number_format($commande->total, 0, ',', ' ') }} FCFA** |
</x-mail::table>

---

Vous recevrez un email avec votre **facture PDF** dès que votre commande sera prête.

<x-mail::button :url="route('client.commandes.show', $commande)" color="red">
Suivre ma commande
</x-mail::button>

Merci pour votre confiance,<br>
**L'équipe ISI BURGER** 🍔
</x-mail::message>
