{{-- resources/views/emails/nouvelle-commande-gestionnaire.blade.php --}}
<x-mail::message>
#  Nouvelle commande reçue !

Une nouvelle commande vient d'être passée sur **ISI BURGER**.

---

<x-mail::panel>
**Commande #{{ str_pad($commande->id, 4, '0', STR_PAD_LEFT) }}**

 Client : {{ $commande->user->name }} ({{ $commande->user->email }})  
 Date : {{ $commande->created_at->format('d/m/Y à H:i') }}  
 Total : **{{ number_format($commande->total, 0, ',', ' ') }} FCFA**  
 Statut : **En attente**
</x-mail::panel>

## Articles commandés

<x-mail::table>
| Burger | Quantité | Sous-total |
|:-------|:---:|---:|
@foreach($commande->items as $item)
| {{ $item->burger->nom }} | {{ $item->quantite }} | {{ number_format($item->sous_total, 0, ',', ' ') }} FCFA |
@endforeach
| **TOTAL** | | **{{ number_format($commande->total, 0, ',', ' ') }} FCFA** |
</x-mail::table>

<x-mail::button :url="route('gestionnaire.commandes.show', $commande)" color="red">
Traiter cette commande
</x-mail::button>

Cordialement,<br>
**Système ISI BURGER**
</x-mail::message>
