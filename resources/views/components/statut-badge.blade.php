@props(['statut'])
<span {{ $attributes->merge(['class' => 'badge text-bg-'.$statut->couleur()]) }}>{{ $statut->label() }}</span>
